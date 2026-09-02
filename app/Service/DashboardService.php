<?php

namespace App\Service;

use App\Models\Exam;
use App\Models\ExamRejection;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Monta os dados do dashboard. O professor monitora os exames de todo o
 * sistema; o aluno monitora apenas os exames que ele mesmo realizou.
 */
class DashboardService
{
    private const RECENT_MONTHS = 6;

    private const STALE_APPROVAL_HOURS = 48;

    private const INACTIVE_STUDENT_DAYS = 30;

    private const RANKING_LIMIT = 5;

    private const LIST_LIMIT = 10;

    private const PERCENT = 100;

    /** Ordem fixa de exibição dos status. */
    private const STATUS_ORDER = [
        ExamService::STATUS_APPROVED,
        ExamService::STATUS_PENDING_APPROVAL,
        ExamService::STATUS_PENDING,
        ExamService::STATUS_REJECTED,
    ];

    private const MONTH_ABBREVIATIONS = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

    public function __construct(private readonly ExamService $examService) {}

    /**
     * @return array<string, mixed>
     */
    public function buildTeacherDashboard(): array
    {
        $statusCounts = $this->countByStatus(Exam::query());

        return [
            'stats' => [
                $this->stat('Exames no mês', Exam::where('date', '>=', now()->startOfMonth())->count(), 'primary', 'desde '.now()->startOfMonth()->format('d/m')),
                $this->stat('Aguardando aprovação', $statusCounts[ExamService::STATUS_PENDING_APPROVAL] ?? 0, 'warning', 'enviados por alunos'),
                $this->stat('Taxa de aprovação', $this->approvalRate($statusCounts), 'success', 'aprovados ÷ (aprovados + rejeitados)', '%'),
                $this->stat('Pacientes cadastrados', Patient::count(), 'primary'),
            ],
            'statusDistribution' => $this->statusDistribution($statusCounts),
            'monthlyVolume' => $this->monthlyVolume(Exam::query()),
            'rejectionRateByExamType' => $this->rejectionRateByExamType(),
            'staleApprovals' => $this->staleApprovals(),
            'rejectionRanking' => $this->rejectionRanking(),
            'inactiveStudents' => $this->inactiveStudents(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildStudentDashboard(User $student): array
    {
        $ownExams = fn (): Builder => Exam::where('user_id', $student->id);
        $statusCounts = $this->countByStatus($ownExams());

        return [
            'stats' => [
                $this->stat('Meus exames', (int) $statusCounts->sum(), 'primary'),
                $this->stat('Aguardando aprovação', $statusCounts[ExamService::STATUS_PENDING_APPROVAL] ?? 0, 'warning', 'na fila do professor'),
                $this->stat('Aprovados', $statusCounts[ExamService::STATUS_APPROVED] ?? 0, 'success'),
                $this->stat('Taxa de aprovação', $this->approvalRate($statusCounts), 'success', 'aprovados ÷ (aprovados + rejeitados)', '%'),
            ],
            'statusDistribution' => $this->statusDistribution($statusCounts),
            'monthlyVolume' => $this->monthlyVolume($ownExams()),
            'actionRequired' => $this->actionRequired($ownExams()),
            'recentRejections' => $this->recentRejections($student),
        ];
    }

    /**
     * @return array{label: string, value: int|float, tone: string, hint: string|null, suffix: string|null}
     */
    private function stat(string $label, int|float $value, string $tone, ?string $hint = null, ?string $suffix = null): array
    {
        return compact('label', 'value', 'tone', 'hint', 'suffix');
    }

    /**
     * @param  Builder<Exam>  $query
     * @return Collection<string, int>
     */
    private function countByStatus(Builder $query): Collection
    {
        return $query
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total);
    }

    /**
     * @param  Collection<string, int>  $statusCounts
     */
    private function approvalRate(Collection $statusCounts): float
    {
        $approved = $statusCounts[ExamService::STATUS_APPROVED] ?? 0;
        $rejected = $statusCounts[ExamService::STATUS_REJECTED] ?? 0;
        $reviewed = $approved + $rejected;

        return $reviewed === 0 ? 0.0 : round($approved / $reviewed * self::PERCENT, 1);
    }

    /**
     * @param  Collection<string, int>  $statusCounts
     * @return array<int, array{status: string, label: string, count: int}>
     */
    private function statusDistribution(Collection $statusCounts): array
    {
        $labels = $this->examService->getStatusOptions();

        return array_map(fn (string $status) => [
            'status' => $status,
            'label' => $labels[$status] ?? $status,
            'count' => $statusCounts[$status] ?? 0,
        ], self::STATUS_ORDER);
    }

    /**
     * Exames por mês (data do exame) nos últimos meses, incluindo meses sem exames.
     *
     * @param  Builder<Exam>  $query
     * @return array<int, array{label: string, count: int}>
     */
    private function monthlyVolume(Builder $query): array
    {
        $start = now()->startOfMonth()->subMonths(self::RECENT_MONTHS - 1);

        $countsByMonth = $query
            ->where('date', '>=', $start)
            ->pluck('date')
            ->countBy(fn (CarbonInterface $date) => $date->format('Y-m'));

        $volume = [];

        for ($month = $start->copy(); $month->lte(now()); $month->addMonth()) {
            $volume[] = [
                'label' => self::MONTH_ABBREVIATIONS[$month->month - 1].'/'.$month->format('y'),
                'count' => $countsByMonth[$month->format('Y-m')] ?? 0,
            ];
        }

        return $volume;
    }

    /**
     * Tipos de exame com maior proporção de rejeições (qualidade técnica).
     *
     * @return array<int, array{label: string, rate: float, rejected: int, total: int}>
     */
    private function rejectionRateByExamType(): array
    {
        $rows = DB::table('exams')
            ->whereNull('deleted_at')
            ->select('exam_type_id', DB::raw('count(*) as total'))
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as rejected', [ExamService::STATUS_REJECTED])
            ->groupBy('exam_type_id')
            ->having('rejected', '>', 0)
            ->get();

        $names = ExamType::whereIn('id', $rows->pluck('exam_type_id'))->pluck('name', 'id');

        return $rows
            ->map(fn ($row) => [
                'label' => $names[$row->exam_type_id] ?? "Tipo #{$row->exam_type_id}",
                'rate' => round((int) $row->rejected / (int) $row->total * self::PERCENT, 1),
                'rejected' => (int) $row->rejected,
                'total' => (int) $row->total,
            ])
            ->sortByDesc('rate')
            ->take(self::RANKING_LIMIT)
            ->values()
            ->all();
    }

    /**
     * Exames aguardando aprovação há mais tempo que o limite (alerta).
     *
     * @return array<int, array{id: int, student: string, exam_type: string, patient: string, waiting_hours: int}>
     */
    private function staleApprovals(): array
    {
        return Exam::with(['user', 'examType', 'patient.user'])
            ->where('status', ExamService::STATUS_PENDING_APPROVAL)
            ->where('updated_at', '<=', now()->subHours(self::STALE_APPROVAL_HOURS))
            ->orderBy('updated_at')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (Exam $exam) => [
                'id' => $exam->id,
                'student' => $exam->user->name ?? '—',
                'exam_type' => $exam->examType->name ?? '—',
                'patient' => $exam->patient->user->name ?? '—',
                'waiting_hours' => (int) $exam->updated_at->diffInHours(now()),
            ])
            ->all();
    }

    /**
     * Alunos com mais rejeições acumuladas.
     *
     * @return array<int, array{name: string, total: int}>
     */
    private function rejectionRanking(): array
    {
        return DB::table('exam_rejections')
            ->join('exams', 'exams.id', '=', 'exam_rejections.exam_id')
            ->join('users', 'users.id', '=', 'exams.user_id')
            ->whereNull('exams.deleted_at')
            ->select('users.name', DB::raw('count(*) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->orderBy('users.name')
            ->limit(self::RANKING_LIMIT)
            ->get()
            ->map(fn ($row) => ['name' => $row->name, 'total' => (int) $row->total])
            ->all();
    }

    /**
     * Alunos ativos sem nenhum exame registrado no período.
     *
     * @return array<int, string>
     */
    private function inactiveStudents(): array
    {
        return User::where('role', 'student')
            ->where('active', true)
            ->whereDoesntHave('exams', fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(self::INACTIVE_STUDENT_DAYS)))
            ->orderBy('name')
            ->limit(self::LIST_LIMIT)
            ->pluck('name')
            ->all();
    }

    /**
     * Exames do aluno que ainda dependem dele (pendentes ou rejeitados).
     *
     * @param  Builder<Exam>  $query
     * @return array<int, array{id: int, exam_type: string, patient: string, status: string, date: string|null}>
     */
    private function actionRequired(Builder $query): array
    {
        return $query
            ->with(['examType', 'patient.user'])
            ->whereIn('status', [ExamService::STATUS_PENDING, ExamService::STATUS_REJECTED])
            ->orderByDesc('date')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (Exam $exam) => [
                'id' => $exam->id,
                'exam_type' => $exam->examType->name ?? '—',
                'patient' => $exam->patient->user->name ?? '—',
                'status' => $exam->status,
                'date' => $exam->date->toDateString(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{exam_id: int, exam_type: string, justification: string, rejected_by: string, rejected_at: string}>
     */
    private function recentRejections(User $student): array
    {
        return ExamRejection::with(['exam.examType', 'user'])
            ->whereHas('exam', fn (Builder $query) => $query->where('user_id', $student->id))
            ->latest()
            ->limit(self::RANKING_LIMIT)
            ->get()
            ->map(fn (ExamRejection $rejection) => [
                'exam_id' => $rejection->exam_id,
                'exam_type' => $rejection->exam->examType->name ?? '—',
                'justification' => $rejection->justification,
                'rejected_by' => $rejection->user->name ?? '—',
                'rejected_at' => $rejection->created_at->toIso8601String(),
            ])
            ->all();
    }
}
