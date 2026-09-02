<?php

namespace App\Service;

use App\Models\Exam;
use App\Models\ExamTypeField;
use App\Models\ExamTypeFieldReference;
use App\Models\Patient;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Cruza os resultados de um exame com os valores de referência do tipo de
 * exame, escolhendo para cada campo a referência mais específica que se
 * aplica ao sexo e à idade do paciente na data do exame.
 */
class ExamReferenceService
{
    public const STATUS_WITHIN = 'within';

    public const STATUS_BELOW = 'below';

    public const STATUS_ABOVE = 'above';

    public const STATUS_LABELS = [
        self::STATUS_WITHIN => 'Normal',
        self::STATUS_BELOW => 'Abaixo',
        self::STATUS_ABOVE => 'Acima',
    ];

    /**
     * @return array<string, array{criteria_label: string, range_label: string, status: string|null, status_label: string|null}>
     */
    public function evaluateResults(Exam $exam): array
    {
        $exam->loadMissing(['patient', 'examType.fields.references']);

        $patient = $exam->patient;
        $age = $this->patientAgeAt($patient, $exam->date);
        $results = is_array($exam->results) ? $exam->results : [];
        $examType = $exam->examType;
        $fields = $examType ? $examType->fields : collect();
        $evaluated = [];

        foreach ($fields as $field) {
            $reference = $this->findApplicableReference($field, $patient?->sex, $age);

            if (! $reference) {
                continue;
            }

            $status = $this->classify($results[$field->name] ?? null, $reference);

            $evaluated[$field->name] = [
                'criteria_label' => $reference->criteria_label,
                'range_label' => $reference->range_label,
                'status' => $status,
                'status_label' => $status ? self::STATUS_LABELS[$status] : null,
            ];
        }

        return $evaluated;
    }

    public function findApplicableReference(ExamTypeField $field, ?string $sex, ?int $age): ?ExamTypeFieldReference
    {
        return $field->references
            ->filter(fn (ExamTypeFieldReference $reference) => $reference->appliesTo($sex, $age))
            ->sortByDesc(fn (ExamTypeFieldReference $reference) => $reference->specificity())
            ->first();
    }

    private function patientAgeAt(?Patient $patient, ?CarbonInterface $date): ?int
    {
        if (! $patient || ! $patient->birthday || ! $date) {
            return null;
        }

        $years = Carbon::parse($patient->birthday)->diffInYears($date, false);

        return $years < 0 ? null : (int) floor($years);
    }

    private function classify(mixed $value, ExamTypeFieldReference $reference): ?string
    {
        if ($value === null || $value === '' || is_bool($value) || ! is_numeric($value)) {
            return null;
        }

        $numericValue = (float) $value;

        if ($reference->min_value !== null && $numericValue < $reference->min_value) {
            return self::STATUS_BELOW;
        }

        if ($reference->max_value !== null && $numericValue > $reference->max_value) {
            return self::STATUS_ABOVE;
        }

        return self::STATUS_WITHIN;
    }
}
