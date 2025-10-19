@extends('layouts.base')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Anamneses</h2>
        <a href="{{ route('patient-histories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Anamnese
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <form method="GET" action="{{ route('patient-histories.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="patient" class="form-label">Pesquisar por paciente</label>
                    <input type="text" name="patient" id="patient" class="form-control"
                           value="{{ request('patient') }}" placeholder="Digite o nome do paciente...">
                </div>
                <div class="col-md-3">
                    <label for="professional" class="form-label">Pesquisar por profissional</label>
                    <input type="text" name="professional" id="professional" class="form-control"
                           value="{{ request('professional') }}" placeholder="Digite o nome do profissional...">
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Filtrar por data</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('patient-histories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Lista de Anamneses -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                <div class="col-md-4">Paciente</div>
                <div class="col-md-4">Profissional</div>
                <div class="col-md-2">Data</div>
                <div class="col-md-2 text-end">Ações</div>
            </div>

            @forelse($anamneses as $anamnese)
                <div class="border rounded mb-2">
                    <div class="row g-3 px-3 py-2 align-items-center">
                        <div class="col-md-4" data-label="Paciente">
                            {{ $anamnese->patient->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-4" data-label="Profissional">
                            {{ $anamnese->user->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-2" data-label="Data">
                            {{ $anamnese->recorded_at->format('d/m/Y') }}
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('patient-histories.show', $anamnese->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('patient-histories.edit', $anamnese->id) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('patient-histories.destroy', $anamnese->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Tem certeza que deseja excluir esta anamnese?')"
                                            title="Excluir">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    Nenhuma anamnese encontrada com os filtros aplicados.
                </div>
            @endforelse
        </div>
        <div class="mt-3">
            {{ $anamneses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
