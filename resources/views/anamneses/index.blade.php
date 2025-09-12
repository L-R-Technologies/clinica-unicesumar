@extends('layouts.base')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Anamneses</h2>
        <a href="{{ route('anamneses.create') }}" class="btn btn-success">Nova Anamnese</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Data</th>
                        <th>Profissional</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anamneses as $anamnese)
                        <tr>
                            <td>{{ $anamnese->id }}</td>
                            <td>{{ $anamnese->patient->name ?? 'N/A' }}</td>
                            <td>{{ $anamnese->date->format('d/m/Y') }}</td>
                            <td>{{ $anamnese->user->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('anamneses.show', $anamnese->id) }}" class="btn btn-info btn-sm">Ver</a>
                                <a href="{{ route('anamneses.edit', $anamnese->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('anamneses.destroy', $anamnese->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir esta anamnese?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Nenhuma anamnese encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
