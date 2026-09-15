<?php

namespace Tests\Feature;

use App\Models\ExamType;
use App\Models\ExamTypeField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTypeReferenceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $fieldOverrides
     * @param  array<int, array<string, mixed>>  $references
     * @return array<string, mixed>
     */
    private function examTypePayload(array $fieldOverrides = [], array $references = []): array
    {
        return [
            'name' => 'Glicemia',
            'description' => 'Dosagem de glicose',
            'fields' => [
                array_merge([
                    'name' => 'glucose',
                    'label' => 'Glicose',
                    'field_type' => 'float',
                    'unit' => 'mg/dL',
                    'references' => $references,
                ], $fieldOverrides),
            ],
        ];
    }

    public function test_teacher_can_store_reference_values_by_sex_and_age(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->examTypePayload([], [
            ['sex' => null, 'age_min' => null, 'age_max' => null, 'min_value' => '70', 'max_value' => '99'],
            ['sex' => 'female', 'age_min' => '18', 'age_max' => '59', 'min_value' => '65', 'max_value' => '95'],
        ]);

        $this->actingAs($teacher)
            ->post(route('exam-type.store'), $payload)
            ->assertRedirect(route('exam-type.index'));

        $field = ExamTypeField::where('name', 'glucose')->firstOrFail();
        $femaleReference = $field->references->firstWhere('sex', 'female');

        $this->assertCount(2, $field->references);
        $this->assertNotNull($femaleReference);
        $this->assertSame(18, $femaleReference->age_min);
        $this->assertSame(59, $femaleReference->age_max);
        $this->assertSame('Feminino, 18 a 59 anos', $femaleReference->criteria_label);
        $this->assertSame('65 a 95', $femaleReference->range_label);
        $this->assertSame('Ambos os sexos, todas as idades', $field->references->firstWhere('sex', null)->criteria_label);
    }

    public function test_reference_values_are_rejected_for_text_fields(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->examTypePayload(['field_type' => 'string', 'unit' => null], [
            ['min_value' => '1', 'max_value' => '2'],
        ]);

        $this->actingAs($teacher)
            ->from(route('exam-type.create'))
            ->post(route('exam-type.store'), $payload)
            ->assertRedirect(route('exam-type.create'))
            ->assertSessionHasErrors('fields.0.references');

        $this->assertDatabaseMissing('exam_types', ['name' => 'Glicemia']);
    }

    public function test_reference_max_value_must_not_be_lower_than_min_value(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->examTypePayload([], [
            ['min_value' => '100', 'max_value' => '50'],
        ]);

        $this->actingAs($teacher)
            ->from(route('exam-type.create'))
            ->post(route('exam-type.store'), $payload)
            ->assertSessionHasErrors('fields.0.references.0.max_value');
    }

    public function test_reference_requires_min_or_max_value(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->examTypePayload([], [
            ['sex' => 'male', 'age_min' => '18'],
        ]);

        $this->actingAs($teacher)
            ->from(route('exam-type.create'))
            ->post(route('exam-type.store'), $payload)
            ->assertSessionHasErrors('fields.0.references.0.min_value');
    }

    public function test_updating_exam_type_removes_references_absent_from_request(): void
    {
        $teacher = User::factory()->teacher()->create();
        $examType = ExamType::factory()->create();
        $field = $examType->fields()->create([
            'name' => 'glucose',
            'label' => 'Glicose',
            'field_type' => 'float',
            'unit' => 'mg/dL',
        ]);
        $kept = $field->references()->create(['min_value' => 70, 'max_value' => 99]);
        $removed = $field->references()->create(['sex' => 'male', 'min_value' => 72, 'max_value' => 100]);

        $payload = [
            'name' => $examType->name,
            'description' => '',
            'fields' => [[
                'id' => $field->id,
                'name' => 'glucose',
                'label' => 'Glicose',
                'field_type' => 'float',
                'unit' => 'mg/dL',
                'references' => [
                    ['id' => $kept->id, 'sex' => null, 'age_min' => null, 'age_max' => null, 'min_value' => '70', 'max_value' => '110'],
                ],
            ]],
        ];

        $this->actingAs($teacher)
            ->put(route('exam-type.update', $examType->id), $payload)
            ->assertRedirect(route('exam-type.index'));

        $this->assertDatabaseMissing('exam_type_field_references', ['id' => $removed->id]);
        $this->assertSame(110.0, $kept->fresh()->max_value);
    }

    public function test_changing_field_to_text_discards_its_references(): void
    {
        $teacher = User::factory()->teacher()->create();
        $examType = ExamType::factory()->create();
        $field = $examType->fields()->create([
            'name' => 'glucose',
            'label' => 'Glicose',
            'field_type' => 'float',
            'unit' => null,
        ]);
        $field->references()->create(['min_value' => 70, 'max_value' => 99]);

        $payload = [
            'name' => $examType->name,
            'description' => '',
            'fields' => [[
                'id' => $field->id,
                'name' => 'glucose',
                'label' => 'Glicose',
                'field_type' => 'string',
                'unit' => null,
                'references' => [],
            ]],
        ];

        $this->actingAs($teacher)
            ->put(route('exam-type.update', $examType->id), $payload)
            ->assertRedirect(route('exam-type.index'));

        $this->assertDatabaseCount('exam_type_field_references', 0);
    }
}
