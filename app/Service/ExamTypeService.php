<?php

namespace App\Service;

use App\Models\ExamType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamTypeService
{
    public function validateExamTypeData(array $data, $examTypeId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function validateFieldsData(array $fields)
    {
        $rules = [
            '*.name' => 'required|string|max:255',
            '*.label' => 'required|string|max:255',
            '*.field_type' => 'required|string|in:int,float,string,boolean',
            '*.unit' => 'nullable|string|max:50',
        ];

        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function createExamType(array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data);
            $examType = ExamType::create($validated);

            if (! empty($data['fields'])) {
                $validatedFields = $this->validateFieldsData($data['fields']);

                foreach ($validatedFields as $fieldData) {
                    $examType->fields()->create($fieldData);
                }
            }

            DB::commit();

            return $examType;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateExamType(ExamType $examType, array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data, $examType->id);
            $examType->update($validated);

            if (isset($data['fields'])) {
                $validatedFields = $this->validateFieldsData($data['fields']);

                $examType->fields()->delete();

                foreach ($validatedFields as $fieldData) {
                    $examType->fields()->create($fieldData);
                }
            }

            DB::commit();

            return $examType->fresh('fields');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteExamType(ExamType $examType)
    {
        $examType->update(['is_active' => false]);
    }

    public function toggleStatus(ExamType $examType)
    {
        $examType->update(['is_active' => ! $examType->is_active]);

        return $examType;
    }

    public function getFilteredExamTypes(array $filters = [])
    {
        $query = ExamType::with('fields');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['exam_type_id'])) {
            $query->where('id', $filters['exam_type_id']);
        }

        return $query->orderBy('name')->paginate(20)->withQueryString();
    }

    public function getAllExamTypes()
    {
        return ExamType::where('is_active', true)->orderBy('name')->get();
    }
}
