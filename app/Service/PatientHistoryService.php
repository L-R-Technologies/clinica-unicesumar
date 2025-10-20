<?php

namespace App\Service;

use App\Models\Patient;
use App\Models\PatientHistory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PatientHistoryService
{
    /**
     * Valida todos os dados de uma anamnese.
     */
    public function validateData(array $data): array
    {
        $validator = Validator::make($data, [
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'recorded_at' => 'required|date',

            'fasting' => 'nullable|boolean',
            'fasting_hours' => 'nullable|integer|min:0',
            'alcohol_last_24h' => 'nullable|boolean',
            'on_medication' => 'nullable|boolean',
            'medications' => 'nullable|string|max:255',
            'on_supplements' => 'nullable|boolean',
            'supplements' => 'nullable|string|max:255',
            'chronic_disease' => 'nullable|boolean',
            'chronic_disease_details' => 'nullable|string|max:255',
            'recent_surgery' => 'nullable|boolean',
            'surgery_details' => 'nullable|string|max:255',
            'allergies' => 'nullable|boolean',
            'allergy_details' => 'nullable|string|max:255',
            'pregnant_or_lactating' => 'nullable|boolean',
            'menstrual_period' => 'nullable|string|max:255',
            'smokes' => 'nullable|boolean',
            'cigarettes_per_day' => 'nullable|integer|min:0',
            'physically_active' => 'nullable|boolean',
            'recent_fever_or_flu' => 'nullable|boolean',
            'observation' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Cria uma nova anamnese.
     */
    public function create(array $data): PatientHistory
    {
        return PatientHistory::create($data);
    }

    /**
     * Atualiza uma anamnese existente.
     */
    public function update(PatientHistory $anamnesis, array $data): bool
    {
        return $anamnesis->update($data);
    }

    /**
     * Exclui uma anamnese.
     */
    public function delete(PatientHistory $anamnesis): bool
    {
        return $anamnesis->delete();
    }

    /**
     * Retorna todos os pacientes.
     */
    public function getPatients()
    {
        return Patient::with('user')->get()->sortBy('user.name');
    }
}
