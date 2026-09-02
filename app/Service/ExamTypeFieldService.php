<?php

namespace App\Service;

use App\Models\ExamType;
use App\Models\ExamTypeField;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidatorInstance;

/**
 * Validação e sincronização dos campos personalizados de um tipo de exame e
 * dos seus valores de referência (faixa esperada por sexo e/ou idade).
 */
class ExamTypeFieldService
{
    public const NUMERIC_FIELD_TYPES = ['int', 'float'];

    private const MIN_AGE = 0;

    private const MAX_AGE = 150;

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     *
     * @throws ValidationException
     */
    public function validateFields(array $input): array
    {
        $ageRule = 'nullable|integer|min:'.self::MIN_AGE.'|max:'.self::MAX_AGE;

        $validator = Validator::make($input, [
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|integer',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|string|in:int,float,string,boolean',
            'fields.*.unit' => 'nullable|string|max:50',
            'fields.*.references' => 'nullable|array',
            'fields.*.references.*.id' => 'nullable|integer',
            'fields.*.references.*.sex' => 'nullable|string|in:male,female',
            'fields.*.references.*.age_min' => $ageRule,
            'fields.*.references.*.age_max' => $ageRule,
            'fields.*.references.*.min_value' => 'nullable|numeric',
            'fields.*.references.*.max_value' => 'nullable|numeric',
        ]);

        $fields = is_array($input['fields'] ?? null) ? $input['fields'] : [];

        $validator->after(fn (ValidatorInstance $validator) => $this->validateReferencesConsistency($validator, $fields));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $fields;
    }

    /**
     * Sincroniza os campos personalizados do tipo de exame: cria os novos,
     * atualiza os existentes e remove os que não vieram na requisição. O mesmo
     * vale para os valores de referência de cada campo.
     *
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function syncFields(ExamType $examType, array $fields): void
    {
        $this->deleteMissing($examType->fields(), $fields);

        foreach ($fields as $fieldData) {
            $field = $this->upsertField($examType, $fieldData);
            $references = is_array($fieldData['references'] ?? null) ? $fieldData['references'] : [];

            $this->syncReferences($field, $references);
        }
    }

    /**
     * @param  array<string, mixed>  $fieldData
     */
    private function upsertField(ExamType $examType, array $fieldData): ExamTypeField
    {
        $attributes = [
            'name' => $fieldData['name'],
            'label' => $fieldData['label'],
            'field_type' => $fieldData['field_type'],
            'unit' => $this->nullIfBlank($fieldData['unit'] ?? null),
        ];

        if (empty($fieldData['id'])) {
            return $examType->fields()->create($attributes);
        }

        $field = $examType->fields()->findOrFail($fieldData['id']);
        $field->update($attributes);

        return $field;
    }

    /**
     * @param  array<int, array<string, mixed>>  $references
     */
    private function syncReferences(ExamTypeField $field, array $references): void
    {
        // Referências só fazem sentido para campos numéricos; ao mudar o tipo
        // do campo para texto/booleano, as referências antigas são descartadas.
        if (! in_array($field->field_type, self::NUMERIC_FIELD_TYPES, true)) {
            $field->references()->delete();

            return;
        }

        $this->deleteMissing($field->references(), $references);

        foreach ($references as $referenceData) {
            $attributes = [
                'sex' => $this->nullIfBlank($referenceData['sex'] ?? null),
                'age_min' => $this->nullIfBlank($referenceData['age_min'] ?? null),
                'age_max' => $this->nullIfBlank($referenceData['age_max'] ?? null),
                'min_value' => $this->nullIfBlank($referenceData['min_value'] ?? null),
                'max_value' => $this->nullIfBlank($referenceData['max_value'] ?? null),
            ];

            if (empty($referenceData['id'])) {
                $field->references()->create($attributes);

                continue;
            }

            $field->references()->findOrFail($referenceData['id'])->update($attributes);
        }
    }

    /**
     * Remove, dentro da relação, os registros cujo id não veio na requisição.
     *
     * @param  \Illuminate\Database\Eloquent\Relations\HasMany<covariant \Illuminate\Database\Eloquent\Model, covariant \Illuminate\Database\Eloquent\Model>  $relation
     * @param  array<int, array<string, mixed>>  $incoming
     */
    private function deleteMissing($relation, array $incoming): void
    {
        $existingIds = $relation->pluck('id')->all();
        $incomingIds = array_filter(array_column($incoming, 'id'));
        $idsToDelete = array_diff($existingIds, $incomingIds);

        if ($idsToDelete !== []) {
            $relation->whereIn('id', $idsToDelete)->delete();
        }
    }

    /**
     * @param  array<int, mixed>  $fields
     */
    private function validateReferencesConsistency(ValidatorInstance $validator, array $fields): void
    {
        foreach ($fields as $fieldIndex => $field) {
            $references = is_array($field['references'] ?? null) ? $field['references'] : [];

            if ($references === []) {
                continue;
            }

            if (! in_array($field['field_type'] ?? null, self::NUMERIC_FIELD_TYPES, true)) {
                $validator->errors()->add(
                    "fields.{$fieldIndex}.references",
                    'Valores de referência só podem ser definidos para campos numéricos (inteiro ou decimal).',
                );

                continue;
            }

            foreach ($references as $referenceIndex => $reference) {
                if (is_array($reference)) {
                    $this->validateReferenceRow($validator, "fields.{$fieldIndex}.references.{$referenceIndex}", $reference);
                }
            }
        }
    }

    /**
     * Regras cruzadas (mín/máx) que o validador nativo não resolve bem com
     * wildcards quando o campo comparado está vazio.
     *
     * @param  array<string, mixed>  $reference
     */
    private function validateReferenceRow(ValidatorInstance $validator, string $prefix, array $reference): void
    {
        $minValue = $this->nullIfBlank($reference['min_value'] ?? null);
        $maxValue = $this->nullIfBlank($reference['max_value'] ?? null);
        $ageMin = $this->nullIfBlank($reference['age_min'] ?? null);
        $ageMax = $this->nullIfBlank($reference['age_max'] ?? null);

        if ($minValue === null && $maxValue === null) {
            $validator->errors()->add("{$prefix}.min_value", 'Informe ao menos o valor mínimo ou o valor máximo.');
        } elseif ($minValue !== null && $maxValue !== null && is_numeric($minValue) && is_numeric($maxValue)
            && (float) $minValue > (float) $maxValue) {
            $validator->errors()->add("{$prefix}.max_value", 'O valor máximo deve ser maior ou igual ao valor mínimo.');
        }

        if ($ageMin !== null && $ageMax !== null && is_numeric($ageMin) && is_numeric($ageMax)
            && (int) $ageMin > (int) $ageMax) {
            $validator->errors()->add("{$prefix}.age_max", 'A idade máxima deve ser maior ou igual à idade mínima.');
        }
    }

    private function nullIfBlank(mixed $value): mixed
    {
        return $value === '' ? null : $value;
    }
}
