<?php

namespace App\Service;

use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Monta o conjunto de dados enviado à IA a partir dos exames aprovados.
 * Nada que identifique o paciente sai daqui: sem nome, CPF, e-mail, endereço,
 * telefone ou datas exatas. Cada paciente vira "Paciente N" com sexo e faixa
 * etária; cada resultado leva rótulo, valor, unidade, referência e situação.
 */
class AnonymizedExamDatasetService
{
    /** Limite de pacientes por análise, para manter o prompt em tamanho razoável. */
    public const MAX_PATIENTS = 150;

    private const AGE_BAND_SIZE = 10;

    private const SEX_LABELS = [
        'male' => 'Masculino',
        'female' => 'Feminino',
        'other' => 'Outro',
    ];

    private const UNKNOWN_LABEL = 'não informado';

    public function __construct(private readonly ExamReferenceService $examReferenceService) {}

    /**
     * @param  array{date_from?: string|null, date_to?: string|null, exam_type_ids?: array<int, int>|null}  $filters
     * @return array{text: string, patients_count: int, exams_count: int, truncated: bool}
     */
    public function build(array $filters): array
    {
        $exams = $this->approvedExamsQuery($filters)->get();
        $examsByPatient = $exams->groupBy('patient_id');
        $totalPatients = $examsByPatient->count();
        $includedPatients = $examsByPatient->take(self::MAX_PATIENTS);

        $blocks = $includedPatients->values()->map(
            fn (Collection $patientExams, int $index) => $this->describePatient($index + 1, $patientExams),
        );

        return [
            'text' => $blocks->implode("\n\n"),
            'patients_count' => $includedPatients->count(),
            'exams_count' => $includedPatients->flatten(1)->count(),
            'truncated' => $totalPatients > self::MAX_PATIENTS,
        ];
    }

    /**
     * @param  array{date_from?: string|null, date_to?: string|null, exam_type_ids?: array<int, int>|null}  $filters
     * @return Builder<Exam>
     */
    private function approvedExamsQuery(array $filters): Builder
    {
        return Exam::with(['patient', 'examType.fields.references'])
            ->where('status', ExamService::STATUS_APPROVED)
            ->whereNotNull('results')
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('date', '<=', $to))
            ->when($filters['exam_type_ids'] ?? null, fn (Builder $query, array $ids) => $query->whereIn('exam_type_id', $ids))
            ->orderBy('patient_id')
            ->orderBy('date');
    }

    /**
     * @param  Collection<int, Exam>  $patientExams
     */
    private function describePatient(int $number, Collection $patientExams): string
    {
        $latestExam = $patientExams->last();
        $patient = $latestExam->patient;

        $sex = self::SEX_LABELS[$patient->sex ?? ''] ?? self::UNKNOWN_LABEL;
        $ageBand = $this->ageBand($patient?->birthday, $latestExam->date);

        $lines = ["Paciente {$number} ({$sex}, {$ageBand})"];

        foreach ($patientExams as $exam) {
            array_push($lines, ...$this->describeResults($exam));
        }

        return implode("\n", $lines);
    }

    /**
     * @return array<int, string>
     */
    private function describeResults(Exam $exam): array
    {
        $examTypeName = $exam->examType->name ?? 'Exame';
        $fields = $exam->examType?->fields?->keyBy('name') ?? collect();
        $references = $this->examReferenceService->evaluateResults($exam);
        $lines = [];

        foreach ($exam->results ?? [] as $fieldName => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $field = $fields->get($fieldName);
            $label = $field->label ?? $fieldName;
            $unit = $field->unit ?? null;
            $reference = $references[$fieldName] ?? null;

            $line = "- {$examTypeName} / {$label}: ".$this->formatValue($value).($unit ? " {$unit}" : '');

            if ($reference) {
                $line .= " (Ref: {$reference['range_label']})";
                if ($reference['status_label']) {
                    $line .= " [{$reference['status_label']}]";
                }
            }

            $lines[] = $line;
        }

        return $lines;
    }

    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        return (string) $value;
    }

    private function ageBand(mixed $birthday, mixed $referenceDate): string
    {
        if (! $birthday || ! $referenceDate) {
            return 'idade '.self::UNKNOWN_LABEL;
        }

        $age = (int) floor(Carbon::parse($birthday)->diffInYears(Carbon::parse($referenceDate), false));

        if ($age < 0) {
            return 'idade '.self::UNKNOWN_LABEL;
        }

        $bandStart = intdiv($age, self::AGE_BAND_SIZE) * self::AGE_BAND_SIZE;
        $bandEnd = $bandStart + self::AGE_BAND_SIZE - 1;

        return "{$bandStart}-{$bandEnd} anos";
    }
}
