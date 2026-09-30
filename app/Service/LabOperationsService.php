<?php

namespace App\Service;

use App\Models\Calibration;
use App\Models\Machine;
use App\Models\Sample;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Indicadores operacionais do laboratório (amostras e equipamentos)
 * exibidos no dashboard do professor.
 */
class LabOperationsService
{
    /** Mesmo limite usado pelo comando CheckFreezerSamples. */
    public const FREEZER_ALERT_DAYS = 7;

    /** Prazo máximo, em dias, sem calibração para um equipamento ativo. */
    public const CALIBRATION_DUE_DAYS = 30;

    private const LIST_LIMIT = 10;

    private const MACHINE_STATUS_ACTIVE = 'active';

    private const SAMPLE_STATUS_STORED = 'stored';

    private const SAMPLE_STATUS_ORDER = ['under review', 'stored', 'discarded'];

    public function __construct(private readonly SampleService $sampleService) {}

    /**
     * @return array{distribution: array<int, array{status: string, label: string, count: int}>, overdueInFreezer: array<int, array{id: int, code: string, patient: string, days: int}>}
     */
    public function samplesOverview(): array
    {
        return [
            'distribution' => $this->sampleDistribution(),
            'overdueInFreezer' => $this->samplesOverdueInFreezer(),
        ];
    }

    /**
     * Equipamentos com a última calibração registrada, os que precisam de
     * atenção primeiro.
     *
     * @return array<int, array{id: int, name: string, status: string, last_calibration_at: string|null, last_calibration_status: string|null, is_calibration_due: bool}>
     */
    public function machinesOverview(): array
    {
        $latestCalibration = fn (string $column) => Calibration::select($column)
            ->whereColumn('machine_id', 'machines.id')
            ->orderByDesc('calibration_date')
            ->orderByDesc('id')
            ->limit(1);

        return Machine::query()
            ->addSelect([
                'machines.*',
                'last_calibration_at' => $latestCalibration('calibration_date'),
                'last_calibration_status' => $latestCalibration('status'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Machine $machine) => $this->transformMachine($machine))
            ->sortByDesc('is_calibration_due')
            ->take(self::LIST_LIMIT)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{status: string, label: string, count: int}>
     */
    private function sampleDistribution(): array
    {
        $counts = Sample::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = $this->sampleService->getStatusOptions();

        return array_map(fn (string $status) => [
            'status' => $status,
            'label' => $labels[$status] ?? $status,
            'count' => (int) ($counts[$status] ?? 0),
        ], self::SAMPLE_STATUS_ORDER);
    }

    /**
     * Amostras armazenadas há mais tempo que o limite do freezer.
     *
     * @return array<int, array{id: int, code: string, patient: string, days: int}>
     */
    private function samplesOverdueInFreezer(): array
    {
        return Sample::with('patient.user')
            ->where('status', self::SAMPLE_STATUS_STORED)
            ->where('stored_at', '<=', now()->subDays(self::FREEZER_ALERT_DAYS))
            ->orderBy('stored_at')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn (Sample $sample) => [
                'id' => $sample->id,
                'code' => $sample->code,
                'patient' => $sample->patient->user->name ?? '—',
                'days' => (int) $sample->stored_at->diffInDays(now()),
            ])
            ->all();
    }

    /**
     * @return array{id: int, name: string, status: string, last_calibration_at: string|null, last_calibration_status: string|null, is_calibration_due: bool}
     */
    private function transformMachine(Machine $machine): array
    {
        // Colunas vindas dos subselects de addSelect(), não são atributos do model.
        $lastCalibrationAtRaw = $machine->getAttribute('last_calibration_at');
        $lastCalibrationAt = is_string($lastCalibrationAtRaw) ? Carbon::parse($lastCalibrationAtRaw) : null;
        $lastCalibrationStatus = $machine->getAttribute('last_calibration_status');
        $isActive = $machine->status === self::MACHINE_STATUS_ACTIVE;
        $isOverdue = $lastCalibrationAt === null || $lastCalibrationAt->lt(now()->subDays(self::CALIBRATION_DUE_DAYS));

        return [
            'id' => $machine->id,
            'name' => $machine->name,
            'status' => $machine->status,
            'last_calibration_at' => $lastCalibrationAt?->toDateString(),
            'last_calibration_status' => is_string($lastCalibrationStatus) ? $lastCalibrationStatus : null,
            'is_calibration_due' => $isActive && $isOverdue,
        ];
    }
}
