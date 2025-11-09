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
            'recorded_at' => 'required|date|before_or_equal:today',

            'fasting' => 'required|boolean',
            'fasting_hours' => 'required_if:fasting,true|nullable|integer|min:0',
            'alcohol_last_24h' => 'required|boolean',
            'on_medication' => 'required|boolean',
            'medications' => 'required_if:on_medication,true|nullable|string|max:255',
            'on_supplements' => 'required|boolean',
            'supplements' => 'required_if:on_supplements,true|nullable|string|max:255',
            'chronic_disease' => 'required|boolean',
            'chronic_disease_details' => 'required_if:chronic_disease,true|nullable|string|max:255',
            'infectious_disease_history' => 'required|boolean',
            'infectious_disease_details' => 'required_if:infectious_disease_history,true|nullable|string|max:255',
            'recent_surgery' => 'required|boolean',
            'surgery_details' => 'required_if:recent_surgery,true|nullable|string|max:255',
            'allergies' => 'required|boolean',
            'allergy_details' => 'required_if:allergies,true|nullable|string|max:255',
            'pregnant_or_lactating' => 'required|boolean',
            'menstrual_period' => 'required|string|in:yes,no,n/a|max:255',
            'smokes' => 'required|boolean',
            'cigarettes_per_day' => 'required_if:smokes,true|nullable|integer|min:0',
            'physically_active' => 'required|boolean',
            'recent_fever_or_flu' => 'required|boolean',
            'observation' => 'nullable|string|max:1000',
        ], [
            'fasting_hours.required_if' => 'As horas de jejum são obrigatórias quando em jejum.',
            'medications.required_if' => 'Os medicamentos são obrigatórios quando há uso de medicação.',
            'supplements.required_if' => 'Os suplementos são obrigatórios quando há uso de suplementos.',
            'chronic_disease_details.required_if' => 'Os detalhes da doença crônica são obrigatórios.',
            'infectious_disease_details.required_if' => 'Os detalhes da doença infecciosa são obrigatórios.',
            'surgery_details.required_if' => 'Os detalhes da cirurgia são obrigatórios.',
            'allergy_details.required_if' => 'Os detalhes da alergia são obrigatórios.',
            'cigarettes_per_day.required_if' => 'A quantidade de cigarros por dia é obrigatória quando é fumante.',
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
