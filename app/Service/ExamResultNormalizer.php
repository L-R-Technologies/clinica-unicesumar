<?php

namespace App\Service;

use App\Models\ExamType;
use App\Models\ExamTypeField;

class ExamResultNormalizer
{
    private const TRUE_WORDS = ['true', '1', 'sim', 'yes', 'positivo', 'reagente'];

    private const FALSE_WORDS = ['false', '0', 'nao', 'não', 'no', 'negativo', 'nao reagente', 'não reagente'];

    /**
     * @param  array<string, mixed>  $results
     * @return array<string, string|int|float|bool>
     */
    public function normalize(ExamType $examType, array $results): array
    {
        $examType->loadMissing('fields');
        $normalized = [];

        foreach ($examType->fields as $field) {
            if (! array_key_exists($field->name, $results)) {
                continue;
            }

            $value = $this->castValue($field, $results[$field->name]);

            if ($value !== null) {
                $normalized[$field->name] = $value;
            }
        }

        return $normalized;
    }

    private function castValue(ExamTypeField $field, mixed $value): string|int|float|bool|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($field->field_type) {
            'int' => $this->castInt($value),
            'float' => $this->castFloat($value),
            'boolean' => $this->castBoolean($value),
            default => is_scalar($value) ? trim((string) $value) : null,
        };
    }

    private function castInt(mixed $value): ?int
    {
        $number = $this->castFloat($value);

        return $number === null ? null : (int) round($number);
    }

    private function castFloat(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9,.\-]/', '', $value) ?? '';

        if (str_contains($cleaned, ',') && ! str_contains($cleaned, '.')) {
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (str_contains($cleaned, ',')) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        }

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    private function castBoolean(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $word = mb_strtolower(trim((string) $value));

        if (in_array($word, self::TRUE_WORDS, true)) {
            return true;
        }

        if (in_array($word, self::FALSE_WORDS, true)) {
            return false;
        }

        return null;
    }
}
