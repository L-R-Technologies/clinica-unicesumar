@extends('layouts.base')

@section('content')
<div class="container">
    <div class="card mx-auto" style="max-width: 800px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Nova Anamnese</h2>

            <form action="{{ route('anamneses.store') }}" method="POST">
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
                    <input type="date" name="date"
                           class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date') }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="on_supplements_checkbox" name="on_supplements" value="1" {{ old('on_supplements') ? 'checked' : '' }}>
                    <label class="form-check-label" for="on_supplements_checkbox">Faz uso de suplementos alimentares?</label>
                </div>
                <div class="ms-4 mb-3" id="on_supplements_container" style="display: {{ old('on_supplements') ? 'block' : 'none' }}">
                    <input type="text" name="supplements" class="form-control" placeholder="Quais suplementos?" value="{{ old('supplements') }}">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="chronic_disease_checkbox" name="chronic_disease" value="1" {{ old('chronic_disease') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chronic_disease_checkbox">Possui alguma doença crônica?</label>
                </div>
                <div class="ms-4 mb-3" id="chronic_disease_container" style="display: {{ old('chronic_disease') ? 'block' : 'none' }}">
                    <input type="text" name="chronic_disease_details" class="form-control" placeholder="Detalhes" value="{{ old('chronic_disease_details') }}">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="recent_surgery_checkbox" name="recent_surgery" value="1" {{ old('recent_surgery') ? 'checked' : '' }}>
                    <label class="form-check-label" for="recent_surgery_checkbox">Realizou algum procedimento cirúrgico recentemente?</label>
                </div>
                <div class="ms-4 mb-3" id="recent_surgery_container" style="display: {{ old('recent_surgery') ? 'block' : 'none' }}">
                    <input type="text" name="surgery_details" class="form-control" placeholder="Detalhes" value="{{ old('surgery_details') }}">
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="allergies_checkbox" name="allergies" value="1" {{ old('allergies') ? 'checked' : '' }}>
                    <label class="form-check-label" for="allergies_checkbox">Possui histórico de alergias?</label>
                </div>
                <div class="ms-4 mb-3" id="allergies_container" style="display: {{ old('allergies') ? 'block' : 'none' }}">
                    <input type="text" name="allergy_details" class="form-control" placeholder="Detalhes" value="{{ old('allergy_details') }}">
                </div>

                {{-- DADOS ADICIONAIS --}}
                <h4 class="text-center mt-4 mb-3">Dados Adicionais</h4>

                {{-- Tabagismo --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="smokes_checkbox" name="smokes" value="1" {{ old('smokes') ? 'checked' : '' }}>
                    <label class="form-check-label" for="smokes_checkbox">Você fuma?</label>
                </div>
                <div class="ms-4 mb-3" id="smokes_container" style="display: {{ old('smokes') ? 'block' : 'none' }}">
                    <input type="number" name="cigarettes_per_day" class="form-control" placeholder="Quantos cigarros por dia?" value="{{ old('cigarettes_per_day') }}">
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
                    <a href="{{ route('anamneses.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script para exibir/ocultar campos --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleFields = ['fasting', 'on_supplements', 'chronic_disease', 'recent_surgery', 'allergies', 'smokes'];

    toggleFields.forEach(id => {
        const checkbox = document.getElementById(id + '_checkbox');
        const container = document.getElementById(id + '_container');
        if(!checkbox || !container) return;

        checkbox.addEventListener('change', function() {
            container.style.display = this.checked ? 'block' : 'none';
        });
    });
});
</script>
@endsection
