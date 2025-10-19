@extends('layouts.base')

@section('content')
<div class="container">
    <div class="card mx-auto" style="max-width: 800px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Nova Anamnese</h2>

            <form action="{{ route('patient-histories.store') }}" method="POST">
                @csrf

                {{-- Paciente e Profissional --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Paciente</label>
                        <input type="number" name="patient_id"
                               class="form-control @error('patient_id') is-invalid @enderror"
                               value="{{ old('patient_id') }}" required>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profissional</label>
                        <input type="number" name="user_id"
                               class="form-control @error('user_id') is-invalid @enderror"
                               value="{{ old('user_id') }}" required>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Data da coleta --}}
                <div class="mb-4">
                    <label class="form-label">Data da coleta</label>
                    <input type="date" name="recorded_at"
                           class="form-control @error('recorded_at') is-invalid @enderror"
                           value="{{ old('recorded_at') }}" required>
                    @error('recorded_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h4 class="text-center mt-4 mb-3">Dados Clínicos</h4>

                {{-- Jejum --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="fasting_checkbox" name="fasting" value="1" {{ old('fasting') ? 'checked' : '' }}>
                    <label class="form-check-label" for="fasting_checkbox">Você está em jejum?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="number" name="fasting_hours" class="form-control" placeholder="Há quantas horas?" value="{{ old('fasting_hours') }}" {{ old('fasting') ? '' : 'disabled' }}>
                </div>

                {{-- Álcool nas últimas 24h --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="alcohol_last_24h_checkbox" name="alcohol_last_24h" value="1" {{ old('alcohol_last_24h') ? 'checked' : '' }}>
                    <label class="form-check-label" for="alcohol_last_24h_checkbox">Consumiu álcool nas últimas 24 horas?</label>
                </div>

                {{-- Medicação --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="on_medication_checkbox" name="on_medication" value="1" {{ old('on_medication') ? 'checked' : '' }}>
                    <label class="form-check-label" for="on_medication_checkbox">Faz uso de medicamentos?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="medications" class="form-control" placeholder="Quais medicamentos?" value="{{ old('medications') }}" {{ old('on_medication') ? '' : 'disabled' }}>
                </div>

                {{-- Suplementos --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="on_supplements_checkbox" name="on_supplements" value="1" {{ old('on_supplements') ? 'checked' : '' }}>
                    <label class="form-check-label" for="on_supplements_checkbox">Faz uso de suplementos alimentares?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="supplements" class="form-control" placeholder="Quais suplementos?" value="{{ old('supplements') }}" {{ old('on_supplements') ? '' : 'disabled' }}>
                </div>

                {{-- Doença crônica --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="chronic_disease_checkbox" name="chronic_disease" value="1" {{ old('chronic_disease') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_disease_checkbox">Possui alguma doença crônica?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="chronic_disease_details" class="form-control" placeholder="Detalhes" value="{{ old('chronic_disease_details') }}" {{ old('chronic_disease') ? '' : 'disabled' }}>
                </div>

                {{-- Infecções recentes --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="infectious_disease_history_checkbox" name="infectious_disease_history" value="1" {{ old('infectious_disease_history') ? 'checked' : '' }}>
                    <label class="form-check-label" for="infectious_disease_history_checkbox">Possui histórico de doenças infecciosas recentes?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="infectious_disease_details" class="form-control" placeholder="Detalhes" value="{{ old('infectious_disease_details') }}" {{ old('infectious_disease_history') ? '' : 'disabled' }}>
                </div>

                {{-- Cirurgia recente --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="recent_surgery_checkbox" name="recent_surgery" value="1" {{ old('recent_surgery') ? 'checked' : '' }}>
                    <label class="form-check-label" for="recent_surgery_checkbox">Realizou algum procedimento cirúrgico recentemente?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="surgery_details" class="form-control" placeholder="Detalhes" value="{{ old('surgery_details') }}" {{ old('recent_surgery') ? '' : 'disabled' }}>
                </div>

                {{-- Alergias --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="allergies_checkbox" name="allergies" value="1" {{ old('allergies') ? 'checked' : '' }}>
                    <label class="form-check-label" for="allergies_checkbox">Possui histórico de alergias?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="text" name="allergy_details" class="form-control" placeholder="Detalhes" value="{{ old('allergy_details') }}" {{ old('allergies') ? '' : 'disabled' }}>
                </div>

                {{-- DADOS ADICIONAIS --}}
                <h4 class="text-center mt-4 mb-3">Dados Adicionais</h4>

                {{-- Tabagismo --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="smokes_checkbox" name="smokes" value="1" {{ old('smokes') ? 'checked' : '' }}>
                    <label class="form-check-label" for="smokes_checkbox">Você fuma?</label>
                </div>
                <div class="ms-4 mb-3">
                    <input type="number" name="cigarettes_per_day" class="form-control" placeholder="Quantos cigarros por dia?" value="{{ old('cigarettes_per_day') }}" {{ old('smokes') ? '' : 'disabled' }}>
                </div>

                {{-- Atividade física --}}
                <div class="mb-3">
                    <label>Pratica atividades físicas regularmente?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="physically_active" id="physically_active_yes" value="1" {{ old('physically_active') == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="physically_active_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="physically_active" id="physically_active_no" value="0" {{ old('physically_active') === "0" ? 'checked' : '' }}>
                            <label class="form-check-label" for="physically_active_no">Não</label>
                        </div>
                    </div>
                </div>

                {{-- Período menstrual --}}
                <div class="mb-3">
                    <label>Está em período menstrual?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_yes" value="Sim" {{ old('menstrual_period') == 'Sim' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_no" value="Não" {{ old('menstrual_period') == 'Não' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_no">Não</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_na" value="Não se aplica" {{ old('menstrual_period') == 'Não se aplica' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_na">Não se aplica</label>
                        </div>
                    </div>
                </div>

                {{-- Febre / sintomas gripais --}}
                <div class="mb-3">
                    <label>Teve episódios de febre ou sintomas gripais nos últimos dias?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recent_fever_or_flu" id="fever_yes" value="1" {{ old('recent_fever_or_flu') == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="fever_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recent_fever_or_flu" id="fever_no" value="0" {{ old('recent_fever_or_flu') === "0" ? 'checked' : '' }}>
                            <label class="form-check-label" for="fever_no">Não</label>
                        </div>
                    </div>
                </div>

                {{-- Observação --}}
                <div class="mb-3">
                    <label>Observação</label>
                    <textarea name="observation" class="form-control" rows="3">{{ old('observation') }}</textarea>
                </div>

                {{-- Botões --}}
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <a href="{{ route('patient-histories.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
