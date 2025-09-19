@extends('layouts.base')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detalhes da Anamnese</h2>
            <a href="{{ route('anamneses.index') }}" class="btn btn-primary">Voltar</a>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Informações Básicas</h5>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Paciente:</strong> {{ $anamnese->patient->name ?? 'N/A' }}</div>
                <div class="col-md-6"><strong>Profissional:</strong> {{ $anamnese->user->name ?? 'N/A' }}</div>
            </div>
            <div class="mb-3"><strong>Data:</strong> {{ $anamnese->date->format('d/m/Y') }}</div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Jejum e Álcool</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Jejum:</strong> {{ $anamnese->fasting ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Horas de Jejum:</strong> {{ $anamnese->fasting_hours ?? '-' }}</div>
            </div>
            <div class="mb-2"><strong>Álcool (últimas 24h):</strong> {{ $anamnese->alcohol_last_24h ? 'Sim' : 'Não' }}</div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Medicação e Suplementos</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Uso de medicamentos:</strong> {{ $anamnese->on_medication ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Medicamentos:</strong> {{ $anamnese->medications ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Uso de suplementos:</strong> {{ $anamnese->on_supplements ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Suplementos:</strong> {{ $anamnese->supplements ?? '-' }}</div>
            </div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Condições de Saúde</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Doença crônica:</strong> {{ $anamnese->chronic_disease ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Detalhes:</strong> {{ $anamnese->chronic_disease_details ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Cirurgia recente:</strong> {{ $anamnese->recent_surgery ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Detalhes:</strong> {{ $anamnese->surgery_details ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Alergias:</strong> {{ $anamnese->allergies ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Detalhes:</strong> {{ $anamnese->allergy_details ?? '-' }}</div>
            </div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Estilo de Vida</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Gestante/Lactante:</strong> {{ $anamnese->pregnant_or_lactating ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Período menstrual:</strong> {{ $anamnese->menstrual_period ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Fuma:</strong> {{ $anamnese->smokes ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Cigarros por dia:</strong> {{ $anamnese->cigarettes_per_day ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Atividade física:</strong> {{ $anamnese->physically_active ? 'Sim' : 'Não' }}</div>
                <div class="col-md-6"><strong>Febre/Gripe recente:</strong> {{ $anamnese->recent_fever_or_flu ? 'Sim' : 'Não' }}</div>
            </div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="mb-3">Observações</h5>
            <p>{{ $anamnese->observation ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
