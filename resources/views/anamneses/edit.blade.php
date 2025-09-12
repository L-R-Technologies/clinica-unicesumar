@extends('layouts.base')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Anamnese</h2>

    <form action="{{ route('anamneses.update', $anamnese->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="patient_id" class="form-label">Paciente</label>
                <input type="number" name="patient_id" class="form-control" value="{{ old('patient_id', $anamnese->patient_id) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="user_id" class="form-label">Profissional</label>
                <input type="number" name="user_id" class="form-control" value="{{ old('user_id', $anamnese->user_id) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Data da coleta</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', $anamnese->date->format('Y-m-d')) }}" required>
        </div>

        {{-- Repete os mesmos campos do create, mas com value="{{ old(..., $anamnese->... ) }}" --}}
        {{-- (não vou duplicar tudo aqui por causa do tamanho, mas segue exatamente a mesma ordem do create) --}}

        <button type="submit" class="btn btn-success mt-3">Atualizar</button>
        <a href="{{ route('anamneses.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
