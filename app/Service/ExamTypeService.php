<?php

namespace App\Service;

use App\Models\ExamType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamTypeService
{
    /**
     * Valida os dados do tipo de exame
     */
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

    /**
     * Cria um novo tipo de exame
     */
    public function createExamType(array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data);

            $examType = ExamType::create($validated);

            DB::commit();

            return $examType;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza um tipo de exame existente
     */
    public function updateExamType(ExamType $examType, array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data, $examType->id);

            $examType->update($validated);

            DB::commit();

            return $examType->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Deleta um tipo de exame
     */
    public function deleteExamType(ExamType $examType)
    {
        try {
            DB::beginTransaction();

            $examType->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Retorna todos os tipos de exame cadastrados (com paginação e filtros)
     */
    public function getFilteredExamTypes(array $filters = [])
    {
        $query = ExamType::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if (!empty($filters['exam_type_id'])) {
            $query->where('id', $filters['exam_type_id']);
        }

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * Retorna todos os tipos de exame (sem paginação)
     */
    public function getAllExamTypes()
    {
        return ExamType::orderBy('name')->get();
    }
}
