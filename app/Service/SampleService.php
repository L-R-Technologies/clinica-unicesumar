<?php

namespace App\Service;

use App\Models\Patient;
use App\Models\Sample;
use App\Models\SampleType;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SampleService
{
    public function validateSampleData(array $data, $sampleId = null)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'sample_type_id' => 'required|exists:sample_types,id',
            'date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:under review,stored,discarded',
            'location' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function createSample(array $sampleData, $userId)
    {
        try {
            DB::beginTransaction();

            $sampleData['user_id'] = $userId;
            $sampleData['code'] = $this->generateSampleCode($sampleData['date']);

            $sample = Sample::create($sampleData);

            DB::commit();

            return $sample;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSample(Sample $sample, array $sampleData)
    {
        try {
            DB::beginTransaction();

            $sample->update($sampleData);

            DB::commit();

            return $sample->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteSample(Sample $sample)
    {
        if ($sample->exams()->count() > 0) {
            throw new Exception('Não é possível excluir esta amostra pois ela está vinculada a '.$sample->exams()->count().' exame(s).');
        }

        try {
            DB::beginTransaction();

            $sample->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getFilteredSamples(array $filters)
    {
        $query = Sample::with(['patient.user', 'user', 'sampleType']);

        // Filtrar por responsável se for aluno
        if (! empty($filters['user_id']) && ! empty($filters['user_role'])) {
            if ($filters['user_role'] === 'student') {
                $query->where('user_id', $filters['user_id']);
            }
            // Professores veem todas as amostras, então não aplicamos filtro
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('sampleType', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('patient.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['sample_type_id'])) {
            $query->where('sample_type_id', $filters['sample_type_id']);
        }

        if (! empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
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
        return Patient::with('user')->get()->sortBy('user.name');
    }

    public function getSampleTypes()
    {
        return SampleType::where('is_active', true)->orderBy('name')->get();
    }

    public function getSampleTypesForEdit($currentSampleTypeId = null)
    {
        $query = SampleType::query();

        if ($currentSampleTypeId) {
            $query->where(function ($q) use ($currentSampleTypeId) {
                $q->where('is_active', true)
                    ->orWhere('id', $currentSampleTypeId);
            });
        } else {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->get();
    }

    public function getStatusOptions()
    {
        return [
            'under review' => 'Em Análise',
            'stored' => 'Armazenada',
            'discarded' => 'Descartada',
        ];
    }

    private function generateSampleCode($date)
    {
        $date = Carbon::parse($date);
        $datePrefix = $date->format('dmY');

        $lastSample = Sample::where('code', 'like', $datePrefix.'-%')
            ->latest('code')
            ->first();

        $sequentialNumber = 1;
        if ($lastSample) {
            $parts = explode('-', $lastSample->code);
            $lastSequentialNumber = (int) end($parts);
            $sequentialNumber = $lastSequentialNumber + 1;
        }

        return $datePrefix.'-'.str_pad((string) $sequentialNumber, 3, '0', STR_PAD_LEFT);
    }
}
