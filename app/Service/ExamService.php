<?php

namespace App\Service;

use App\Mail\ExamApproved;
use App\Mail\ExamPendingApproval;
use App\Mail\ExamRejected;
use App\Mail\ExamResultsAvailable;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\Sample;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ExamService
{
    public function validateExamData(array $data, $examId = null)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'patient_history_id' => 'required|exists:patient_histories,id',
            'sample_id' => 'nullable|exists:samples,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'date' => 'required|date|before_or_equal:today',
            'observation' => 'nullable|string',
            'results' => 'nullable|array',
            'status' => 'nullable|in:pending,pending_approval,rejected,approved',
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

        return $validator->validated();
    }

    public function createExam(array $examData, $userId)
    {
        try {
            DB::beginTransaction();

            $examData['user_id'] = $userId;
            $examData['status'] = 'pending';

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

            $this->handleStatusChangeEmails($exam, $exam->status);

            return $exam->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function handleStatusChangeEmails(Exam $exam, $status)
    {
        $exam->load(['patient.user', 'user.student.supervisor', 'examType']);

        if ($status === 'pending_approval') {
            $this->notifyTeachersForApproval($exam);
        }

        if ($status === 'approved') {
            $user = $exam->user;
            if ($user && $user->role === 'student' && isset($user->email)) {
                Mail::to($user->email)->send(new ExamApproved($exam));
            }
            $patientUser = $exam->patient?->user;
            if (isset($patientUser->email)) {
                Mail::to($patientUser->email)->send(new ExamResultsAvailable($exam));
            }
        }

        if ($status === 'rejected') {
            $user = $exam->user;
            if (isset($user->email)) {
                Mail::to($user->email)->send(new ExamRejected($exam));
            }
        }
    }

    protected function notifyTeachersForApproval(Exam $exam)
    {
        $user = $exam->user;
        if (! $user || $user->role === 'teacher') {
            return;
        }

        $student = $user->student;
        $teacher = $student?->supervisor;

        if (isset($teacher->email)) {
            Mail::to($teacher->email)->send(new ExamPendingApproval($exam));
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
        $query = Exam::with(['user', 'patient.user', 'patientHistory', 'sample', 'examType']);

        // Filtrar por responsável se for aluno
        if (! empty($filters['user_id']) && ! empty($filters['user_role'])) {
            if ($filters['user_role'] === 'student') {
                $query->where('user_id', $filters['user_id']);
            }
            // Professores veem todos os exames, então não aplicamos filtro
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('examType', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('patient.user', function ($q) use ($search) {
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

        if (! empty($filters['exam_type_id'])) {
            $query->where('exam_type_id', $filters['exam_type_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->paginate(10);
    }

    public function getPatients()
    {
        return Patient::with('user')->get();
    }

    public function getPatientHistories($patientId)
    {
        $query = PatientHistory::select('id', 'patient_id', 'recorded_at')
            ->where('patient_id', $patientId);

        return $query->orderBy('recorded_at', 'desc')->get();
    }

    public function getSamples($patientId)
    {
        $query = Sample::with('sampleType')
            ->select('id', 'sample_type_id', 'code')
            ->where('patient_id', $patientId);

        return $query->orderBy('code')->get();
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
        return ExamType::where('is_active', true)->orderBy('name')->get();
    }

    public function getExamTypesForEdit($currentExamTypeId = null)
    {
        $query = ExamType::query();

        if ($currentExamTypeId) {
            $query->where(function ($q) use ($currentExamTypeId) {
                $q->where('is_active', true)
                    ->orWhere('id', $currentExamTypeId);
            });
        } else {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->get();
    }
}
