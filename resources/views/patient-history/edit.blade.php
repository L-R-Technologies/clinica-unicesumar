@extends('layouts.base')

@section('content')
<div class="container">
    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Editar Anamnese</h2>

            <form action="{{ route('patient-histories.update', $anamnese->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Paciente e Profissional --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Paciente</label>
                        <input type="number" name="patient_id"
                               class="form-control @error('patient_id') is-invalid @enderror"
                               value="{{ old('patient_id', $anamnese->patient_id) }}" required>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profissional</label>
                        <input type="number" name="user_id"
                               class="form-control @error('user_id') is-invalid @enderror"
                               value="{{ old('user_id', $anamnese->user_id) }}" required>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Data da coleta --}}
                <div class="mb-3">
                    <label class="form-label">Data da coleta</label>
                    <input type="date" name="recorded_at"
                           class="form-control @error('recorded_at') is-invalid @enderror"
                           value="{{ old('recorded_at', $anamnese->recorded_at->format('Y-m-d')) }}" required>
                    @error('recorded_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Dados Clínicos --}}
                <h4 class="text-center mt-4 mb-3">Dados Clínicos</h4>

                @php
                    $clinicalFields = [
                        ['id'=>'fasting','label'=>'Você está em jejum?','detail'=>'fasting_hours','type'=>'number','placeholder'=>'Há quantas horas?'],
                        ['id'=>'on_supplements','label'=>'Faz uso de suplementos alimentares?','detail'=>'supplements','type'=>'text','placeholder'=>'Quais suplementos?'],
                        ['id'=>'chronic_disease','label'=>'Possui alguma doença crônica?','detail'=>'chronic_disease_details','type'=>'text','placeholder'=>'Detalhes'],
                        ['id'=>'recent_infections','label'=>'Possui histórico de doenças infecciosas recentes?','detail'=>'recent_infections_details','type'=>'text','placeholder'=>'Detalhes'],
                        ['id'=>'recent_surgery','label'=>'Realizou algum procedimento cirúrgico recentemente?','detail'=>'surgery_details','type'=>'text','placeholder'=>'Detalhes'],
                        ['id'=>'allergies','label'=>'Possui histórico de alergias?','detail'=>'allergy_details','type'=>'text','placeholder'=>'Detalhes'],
                    ];
                @endphp

                @foreach($clinicalFields as $field)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="{{ $field['id'] }}_checkbox" name="{{ $field['id'] }}" value="1" {{ old($field['id'], $anamnese->{$field['id']}) ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $field['id'] }}_checkbox">{{ $field['label'] }}</label>
                    </div>
                    <div class="ms-4 mb-3" id="{{ $field['id'] }}_container" style="display: {{ old($field['id'], $anamnese->{$field['id']}) ? 'block' : 'none' }}">
                        <input type="{{ $field['type'] }}" name="{{ $field['detail'] }}" class="form-control"
                               placeholder="{{ $field['placeholder'] }}"
                               value="{{ old($field['detail'], $anamnese->{$field['detail']}) }}">
                    </div>
                @endforeach

                {{-- Tabagismo --}}
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="smokes_checkbox" name="smokes" value="1" {{ old('smokes', $anamnese->smokes) ? 'checked' : '' }}>
                    <label class="form-check-label" for="smokes_checkbox">Você fuma?</label>
                </div>
                <div class="ms-4 mb-3" id="cigarettes_container" style="display: {{ old('smokes', $anamnese->smokes) ? 'block' : 'none' }}">
                    <input type="number" name="cigarettes_per_day" class="form-control" placeholder="Quantos cigarros por dia?" value="{{ old('cigarettes_per_day', $anamnese->cigarettes_per_day) }}">
                </div>

                {{-- Atividade física --}}
                <div class="mb-3">
                    <label>Pratica atividades físicas regularmente?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="physically_active" id="physically_active_yes" value="1" {{ old('physically_active', $anamnese->physically_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="physically_active_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="physically_active" id="physically_active_no" value="0" {{ !old('physically_active', $anamnese->physically_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="physically_active_no">Não</label>
                        </div>
                    </div>
                </div>

                {{-- Período menstrual --}}
                <div class="mb-3">
                    <label>Está em período menstrual?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_yes" value="Sim" {{ old('menstrual_period', $anamnese->menstrual_period) == 'Sim' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_no" value="Não" {{ old('menstrual_period', $anamnese->menstrual_period) == 'Não' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_no">Não</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="menstrual_period" id="menstrual_na" value="Não se aplica" {{ old('menstrual_period', $anamnese->menstrual_period) == 'Não se aplica' ? 'checked' : '' }}>
                            <label class="form-check-label" for="menstrual_na">Não se aplica</label>
                        </div>
                    </div>
                </div>

                {{-- Febre / sintomas gripais --}}
                <div class="mb-3">
                    <label>Teve episódios de febre ou sintomas gripais nos últimos dias?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recent_fever_or_flu" id="fever_yes" value="1" {{ old('recent_fever_or_flu', $anamnese->recent_fever_or_flu) ? 'checked' : '' }}>
                            <label class="form-check-label" for="fever_yes">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recent_fever_or_flu" id="fever_no" value="0" {{ !old('recent_fever_or_flu', $anamnese->recent_fever_or_flu) ? 'checked' : '' }}>
                            <label class="form-check-label" for="fever_no">Não</label>
                        </div>
                    </div>
                </div>

                {{-- Observação --}}
                <div class="mb-3">
                    <label>Observação</label>
                    <textarea name="observation" class="form-control" rows="3">{{ old('observation', $anamnese->observation) }}</textarea>
                </div>

                {{-- Botões --}}
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-success">Atualizar</button>
                    <a href="{{ route('patient-histories.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script para exibir/ocultar campos --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleFields = ['fasting', 'on_supplements', 'chronic_disease', 'recent_infections', 'recent_surgery', 'allergies', 'smokes'];

        toggleFields.forEach(id => {
            const checkbox = document.getElementById(id + '_checkbox');
            const container = document.getElementById(id + (id==='smokes' ? '_container' : '_container'));
            if(!checkbox || !container) return;

            checkbox.addEventListener('change', function() {
                container.style.display = this.checked ? 'block' : 'none';
            });
        });
    });
</script>
@endsection
