<?php

namespace App\Service;

use App\Models\ExamType;
use App\Models\ExamTypeField;
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
     * Valida os campos personalizados do tipo de exame
     */
    public function validateFieldsData(array $fields)
    {
        $rules = [
            '*.name'       => 'required|string|max:255',
            '*.label'      => 'required|string|max:255',
            '*.field_type' => 'required|string|in:text,number,date,select',
            '*.unit'       => 'nullable|string|max:50',
        ];

        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Cria um novo tipo de exame com seus campos
     */
    public function createExamType(array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data);
            $examType = ExamType::create($validated);

            // Se houver campos personalizados
            if (!empty($data['fields'])) {
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

    /**
     * Atualiza um tipo de exame existente e seus campos
     */
    public function updateExamType(ExamType $examType, array $data)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validateExamTypeData($data, $examType->id);
            $examType->update($validated);

            // Atualiza os campos personalizados
            if (isset($data['fields'])) {
                $validatedFields = $this->validateFieldsData($data['fields']);

                // Apagar todos os campos e recriar (forma simples e segura)
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

    /**
     * Deleta um tipo de exame e seus campos
     */
    public function deleteExamType(ExamType $examType)
    {
        try {
            DB::beginTransaction();

            // Remove primeiro os campos relacionados
            $examType->fields()->delete();
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
        // Carrega os campos junto com os tipos de exame
        $query = ExamType::with('fields');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
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
