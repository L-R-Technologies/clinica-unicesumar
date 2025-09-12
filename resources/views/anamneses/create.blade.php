@extends('layouts.base')

@section('content')
<div class="container">
    <h2 class="mb-4">Nova Anamnese</h2>

    <form action="{{ route('anamneses.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="patient_id" class="form-label">Paciente</label>
                <input type="number" name="patient_id" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_id" class="form-label">Profissional</label>
                <input type="number" name="user_id" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Data da coleta</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Você está em jejum?</label>
                <select name="fasting" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="fasting_hours" class="form-label">Horas de Jejum</label>
                <input type="number" name="fasting_hours" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Você ingeriu álcool nas últimas 24h?</label>
                <select name="alcohol_last_24h" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Está em uso de alguma medicação?</label>
            <select name="on_medication" class="form-control mb-2">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
            <input type="text" name="medications" class="form-control" placeholder="Quais medicamentos?">
        </div>

        <div class="mb-3">
            <label>Faz uso de suplementos alimentares?</label>
            <select name="on_supplements" class="form-control mb-2">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
            <input type="text" name="supplements" class="form-control" placeholder="Quais suplementos?">
        </div>

        <div class="mb-3">
            <label>Possui alguma doença crônica?</label>
            <select name="chronic_disease" class="form-control mb-2">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
            <input type="text" name="chronic_disease_details" class="form-control" placeholder="Detalhes">
        </div>

        <div class="mb-3">
            <label>Realizou algum procedimento cirúrgico recentemente?</label>
            <select name="recent_surgery" class="form-control mb-2">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
            <input type="text" name="surgery_details" class="form-control" placeholder="Detalhes">
        </div>

        <div class="mb-3">
            <label>Possui histórico de alergias?</label>
            <select name="allergies" class="form-control mb-2">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
            <input type="text" name="allergy_details" class="form-control" placeholder="Detalhes">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>É gestante ou lactante?</label>
                <select name="pregnant_or_lactating" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Está em período menstrual?</label>
                <input type="text" name="menstrual_period" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Você fuma?</label>
                <select name="smokes" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Cigarros por dia</label>
                <input type="number" name="cigarettes_per_day" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Pratica atividades físicas regurlamente</label>
            <select name="physically_active" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Teve episódios de febre ou sintomas gripais nos últimos dias?</label>
            <select name="recent_fever_or_flu" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Observação</label>
            <textarea name="observation" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3">Salvar</button>
        <a href="{{ route('anamneses.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
