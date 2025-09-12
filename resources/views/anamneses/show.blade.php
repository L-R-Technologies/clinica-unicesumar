@extends('layouts.base')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalhes da Anamnese</h2>
        <a href="{{ route('anamneses.index') }}" class="btn btn-primary">Voltar</a>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Paciente:</strong> {{ $anamnese->patient->name ?? 'N/A' }}</p>
            <p><strong>Profissional:</strong> {{ $anamnese->user->name ?? 'N/A' }}</p>
            <p><strong>Data:</strong> {{ $anamnese->date->format('d/m/Y') }}</p>

            <hr>

            <p><strong>Jejum:</strong> {{ $anamnese->fasting ? 'Sim' : 'Não' }}</p>
            <p><strong>Horas de Jejum:</strong> {{ $anamnese->fasting_hours ?? '-' }}</p>
            <p><strong>Álcool (últimas 24h):</strong> {{ $anamnese->alcohol_last_24h ? 'Sim' : 'Não' }}</p>
            <p><strong>Uso de medicamentos:</strong> {{ $anamnese->on_medication ? 'Sim' : 'Não' }}</p>
            <p><strong>Medicamentos:</strong> {{ $anamnese->medications ?? '-' }}</p>
            <p><strong>Uso de suplementos:</strong> {{ $anamnese->on_supplements ? 'Sim' : 'Não' }}</p>
            <p><strong>Suplementos:</strong> {{ $anamnese->supplements ?? '-' }}</p>
            <p><strong>Doença crônica:</strong> {{ $anamnese->chronic_disease ? 'Sim' : 'Não' }}</p>
            <p><strong>Detalhes doença crônica:</strong> {{ $anamnese->chronic_disease_details ?? '-' }}</p>
            <p><strong>Cirurgia recente:</strong> {{ $anamnese->recent_surgery ? 'Sim' : 'Não' }}</p>
            <p><strong>Detalhes cirurgia:</strong> {{ $anamnese->surgery_details ?? '-' }}</p>
            <p><strong>Alergias:</strong> {{ $anamnese->allergies ? 'Sim' : 'Não' }}</p>
            <p><strong>Detalhes alergias:</strong> {{ $anamnese->allergy_details ?? '-' }}</p>
            <p><strong>Gestante/Lactante:</strong> {{ $anamnese->pregnant_or_lactating ? 'Sim' : 'Não' }}</p>
            <p><strong>Fuma:</strong> {{ $anamnese->smokes ? 'Sim' : 'Não' }}</p>
            <p><strong>Cigarros por dia:</strong> {{ $anamnese->cigarettes_per_day ?? '-' }}</p>
            <p><strong>Atividade física:</strong> {{ $anamnese->physically_active ? 'Sim' : 'Não' }}</p>
            <p><strong>Período menstrual:</strong> {{ $anamnese->menstrual_period ?? '-' }}</p>
            <p><strong>Febre/Gripe recente:</strong> {{ $anamnese->recent_fever_or_flu ? 'Sim' : 'Não' }}</p>
            <p><strong>Observação:</strong> {{ $anamnese->observation ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
