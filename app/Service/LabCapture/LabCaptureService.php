<?php

namespace App\Service\LabCapture;

use App\Jobs\AnalyzeLabCapture;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\LabAgent;
use App\Models\LabCapture;
use App\Models\User;
use App\Service\ExamResultNormalizer;
use App\Service\ExamService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class LabCaptureService
{
    public const LINK_MODE_EXISTING = 'existing';

    public const LINK_MODE_NEW = 'new';

    private const CAPTURES_PER_PAGE = 20;

    private const MAX_RAW_TEXT_BYTES = 65536;

    private const LINKABLE_EXAM_STATUSES = [
        ExamService::STATUS_PENDING,
        ExamService::STATUS_PENDING_APPROVAL,
        ExamService::STATUS_REJECTED,
    ];

    public function __construct(
        private readonly ExamService $examService,
        private readonly ExamResultNormalizer $resultNormalizer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{raw_text: string, captured_at: string, agent_version?: string|null, serial_settings?: array<string, mixed>|null}
     *
     * @throws ValidationException
     */
    public function validateAgentPayload(array $data): array
    {
        return Validator::make($data, [
            'raw_text' => ['required', 'string', 'max:'.self::MAX_RAW_TEXT_BYTES],
            'raw_hash' => ['nullable', 'string', 'size:64'],
            'captured_at' => ['required', 'date'],
            'agent_version' => ['nullable', 'string', 'max:20'],
            'serial_settings' => ['nullable', 'array'],
        ])->validate();
    }

    /**
     * @param  array{raw_text: string, captured_at: string, serial_settings?: array<string, mixed>|null}  $data
     * @return array{capture: LabCapture, duplicate: bool}
     */
    public function storeFromAgent(LabAgent $agent, array $data): array
    {
        $rawText = $this->normalizeLineBreaks($data['raw_text']);
        $rawHash = hash('sha256', $rawText);

        $existing = LabCapture::where('raw_hash', $rawHash)->first();

        if ($existing) {
            return ['capture' => $existing, 'duplicate' => true];
        }

        $capture = LabCapture::create([
            'lab_agent_id' => $agent->id,
            'machine_id' => $agent->machine_id,
            'raw_text' => $rawText,
            'raw_hash' => $rawHash,
            'serial_settings' => $data['serial_settings'] ?? null,
            'captured_at' => Carbon::parse($data['captured_at']),
            'received_at' => now(),
            'status' => LabCapture::STATUS_RECEIVED,
        ]);

        $this->dispatchAnalysis($capture);

        return ['capture' => $capture, 'duplicate' => false];
    }

    private function dispatchAnalysis(LabCapture $capture): void
    {
        try {
            AnalyzeLabCapture::dispatch($capture);
        } catch (Throwable $exception) {
            Log::warning('Análise da captura falhou na fila síncrona', [
                'lab_capture_id' => $capture->id,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function getFilteredCaptures(array $filters): LengthAwarePaginator
    {
        $query = LabCapture::with(['machine', 'labAgent', 'exam.patient.user', 'exam.examType', 'reviewer'])
            ->orderByDesc('captured_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['machine_id'])) {
            $query->where('machine_id', $filters['machine_id']);
        }

        if (! empty($filters['search'])) {
            $this->applySearch($query, $filters['search']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('captured_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('captured_at', '<=', $filters['date_to']);
        }

        return $query->paginate(self::CAPTURES_PER_PAGE)->withQueryString();
    }

    /**
     * @return array<string, string>
     */
    public function getStatusOptions(): array
    {
        return LabCapture::STATUS_LABELS;
    }

    /**
     * @return Collection<int, ExamType>
     */
    public function getExamTypesWithFields(): Collection
    {
        return ExamType::with('fields')->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getLinkableExams(User $user, int $patientId, ?int $examTypeId): Collection
    {
        $query = Exam::with(['sample', 'examType'])
            ->where('patient_id', $patientId)
            ->whereIn('status', self::LINKABLE_EXAM_STATUSES)
            ->orderByDesc('date');

        if ($examTypeId) {
            $query->where('exam_type_id', $examTypeId);
        }

        if (! $user->hasRole('teacher')) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function retryAnalysis(LabCapture $capture): void
    {
        $capture->update([
            'status' => LabCapture::STATUS_RECEIVED,
            'error_message' => null,
        ]);

        $this->dispatchAnalysis($capture);
    }

    public function discard(LabCapture $capture, User $reviewer): void
    {
        $capture->update([
            'status' => LabCapture::STATUS_DISCARDED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function validateLinkData(array $data): array
    {
        $isNewExam = ($data['mode'] ?? null) === self::LINK_MODE_NEW;

        return Validator::make($data, [
            'mode' => ['required', Rule::in([self::LINK_MODE_EXISTING, self::LINK_MODE_NEW])],
            'exam_type_id' => ['required', 'integer', 'exists:exam_types,id'],
            'results' => ['nullable', 'array'],
            'exam_id' => [Rule::requiredIf(! $isNewExam), 'nullable', 'integer', 'exists:exams,id'],
            'patient_id' => [Rule::requiredIf($isNewExam), 'nullable', 'integer', 'exists:patients,id'],
            'patient_history_id' => [Rule::requiredIf($isNewExam), 'nullable', 'integer', 'exists:patient_histories,id'],
            'sample_id' => ['nullable', 'integer', 'exists:samples,id'],
            'date' => [Rule::requiredIf($isNewExam), 'nullable', 'date', 'before_or_equal:today'],
            'observation' => ['nullable', 'string', 'max:1000'],
        ], [
            'exam_id.required' => 'Selecione o exame que receberá este resultado.',
            'patient_id.required' => 'Selecione o paciente.',
            'patient_history_id.required' => 'Selecione a anamnese do paciente.',
            'date.required' => 'Informe a data do exame.',
        ])->validate();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function linkToExam(LabCapture $capture, User $reviewer, array $data, ?Exam $existingExam): Exam
    {
        $examType = ExamType::with('fields')->findOrFail((int) $data['exam_type_id']);
        $results = $this->resultNormalizer->normalize($examType, $data['results'] ?? []);

        return DB::transaction(function () use ($capture, $reviewer, $data, $existingExam, $examType, $results) {
            $exam = $existingExam
                ? $this->fillExistingExam($existingExam, $examType, $results, $reviewer)
                : $this->createExamFromCapture($data, $examType, $results, $reviewer);

            $capture->update([
                'exam_id' => $exam->id,
                'status' => LabCapture::STATUS_LINKED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'analysis' => array_merge($capture->analysis ?? [], ['confirmed_results' => $results]),
            ]);

            return $exam;
        });
    }

    /**
     * @param  array<string, string|int|float|bool>  $results
     *
     * @throws ValidationException quando o exame escolhido é de outro tipo.
     */
    private function fillExistingExam(Exam $exam, ExamType $examType, array $results, User $reviewer): Exam
    {
        if ($exam->exam_type_id !== $examType->id) {
            throw ValidationException::withMessages([
                'exam_id' => "O exame selecionado é do tipo \"{$exam->examType?->name}\", diferente do tipo escolhido.",
            ]);
        }

        return $this->examService->updateExam($exam, ['results' => $results], $reviewer->role);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, string|int|float|bool>  $results
     */
    private function createExamFromCapture(array $data, ExamType $examType, array $results, User $reviewer): Exam
    {
        $examData = $this->examService->validateExamData([
            'patient_id' => $data['patient_id'],
            'patient_history_id' => $data['patient_history_id'],
            'sample_id' => $data['sample_id'] ?? null,
            'exam_type_id' => $examType->id,
            'date' => $data['date'],
            'observation' => $data['observation'] ?? null,
            'results' => $results,
        ]);

        return $this->examService->createExam($examData, $reviewer->id);
    }

    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $builder) use ($search) {
            $builder->where('raw_text', 'like', "%{$search}%")
                ->orWhereHas('machine', fn (Builder $machine) => $machine->where('name', 'like', "%{$search}%"))
                ->orWhereHas('exam.patient.user', fn (Builder $user) => $user->where('name', 'like', "%{$search}%"));
        });
    }

    private function normalizeLineBreaks(string $text): string
    {
        $normalized = preg_replace("/\r\n|\n\r|\r/", "\n", $text) ?? $text;

        return trim($normalized, "\n");
    }
}
