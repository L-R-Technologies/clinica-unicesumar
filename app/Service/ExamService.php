<?php

namespace App\Service;

use App\Enums\ExamType;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\Sample;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamService
{
    public function validateExamData(array $data, $examId = null)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'patient_history_id' => 'required|exists:patient_histories,id',
            'sample_id' => 'required|exists:samples,id',
            'type' => 'required|string|max:100',
            'date' => 'required|date',
            'observation' => 'nullable|string',
            'results' => 'nullable|string',
            'status' => 'nullable|in:pending,pending_approval,rejected,approved',
            'justification_rejection' => 'nullable|string',
        ];

        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data) {
            if (! empty($data['patient_id']) && ! empty($data['patient_history_id'])) {
                $history = PatientHistory::find($data['patient_history_id']);
                if ($history && $history->patient_id != $data['patient_id']) {
                    $validator->errors()->add('patient_history_id', 'O histórico selecionado não pertence ao paciente escolhido.');
                }
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        if (! empty($validated['results'])) {
            $decoded = json_decode($validated['results'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('results', 'O campo de resultados deve ser um JSON válido.');
                throw new ValidationException($validator);
            }
            $validated['results'] = $decoded;
        }

        return $validated;
    }

    public function createExam(array $examData, $userId)
    {
        try {
            DB::beginTransaction();

            $examData['user_id'] = $userId;
            $examData['status'] = 'pending';

            unset($examData['results']);

            $exam = Exam::create($examData);

            DB::commit();

            return $exam;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateExam(Exam $exam, array $examData)
    {
        try {
            DB::beginTransaction();

            $exam->update($examData);

            DB::commit();

            return $exam->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteExam(Exam $exam)
    {
        try {
            DB::beginTransaction();

            $exam->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getFilteredExams(array $filters)
    {
        $query = Exam::with(['user', 'patient', 'patientHistory', 'sample']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->paginate(15);
    }

    public function getPatients()
    {
        return Patient::with('user')->get();
    }

    public function getPatientHistories($patientId)
    {
        $query = PatientHistory::select('id', 'patient_id', 'date')
            ->where('patient_id', $patientId);

        return $query->orderBy('date')->get();
    }

    public function getSamples($patientId)
    {
        $query = Sample::select('id', 'type', 'code')
            ->where('patient_id', $patientId);

        return $query->orderBy('type')->get();
    }

    public function getStatusOptions()
    {
        return [
            'pending' => 'Pendente',
            'pending_approval' => 'Pendente de Aprovação',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
        ];
    }

    public function getExamTypes()
    {
        return ExamType::getOptions();
    }
}
