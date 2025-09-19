@extends('layouts.base')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Anamneses</h2>
        <a href="{{ route('anamneses.create') }}" class="btn btn-primary">
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
    <form method="GET" action="{{ route('anamneses.index') }}" class="card mb-4">
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
                    <a href="{{ route('anamneses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Lista de Anamneses -->
    <div class="card">
        <div class="card-body">
            @if($anamneses->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($anamneses as $anamnese)
                        <div class="list-group-item py-3 px-2 border-0 shadow-sm mb-2 rounded bg-white">
                            <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                                <div class="flex-grow-1 d-flex flex-wrap align-items-center gap-3 min-width-0">
                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                        <span class="fw-semibold small text-secondary">Paciente</span>
                                        <span class="mb-0 text-truncate" style="max-width: 180px;"
                                              title="{{ $anamnese->patient->name ?? 'N/A' }}">
                                            {{ $anamnese->patient->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                        <span class="fw-semibold small text-secondary">Profissional</span>
                                        <span class="mb-0 text-truncate" style="max-width: 180px;"
                                              title="{{ $anamnese->user->name ?? 'N/A' }}">
                                            {{ $anamnese->user->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                        <span class="fw-semibold small text-secondary">Data</span>
                                        <span class="mb-0 text-truncate">
                                            {{ $anamnese->date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center ms-auto mt-2 mt-md-0">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('anamneses.show', $anamnese->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('anamneses.edit', $anamnese->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('anamneses.destroy', $anamnese->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta anamnese?')"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $anamneses->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-notes-medical fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma anamnese encontrada</h5>
                    <p class="text-muted">
                        @if(request('patient') || request('professional') || request('date'))
                            Tente ajustar os filtros de busca.
                        @else
                            Comece criando a primeira anamnese.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
