<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <a href="{{ route('machines.index') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
        <h2 class="fw-bold text-dark mb-0">Prontuário do Equipamento</h2>
        
        <div class="d-flex gap-2">
            <div class="btn-group shadow-sm">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-file-export me-1"></i> Exportar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" wire:click.prevent="exportCsv">CSV (Excel)</a></li>
                    <li><a class="dropdown-item" href="#" wire:click.prevent="exportPdf">PDF (Documento)</a></li>
                </ul>
            </div>
            
            <a href="{{ route('calibrations.create', $machine->id) }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus me-1"></i> Nova Calibração
            </a>
        </div>
    </div>

    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="text-primary fw-bold mb-4"><i class="fas fa-info-circle me-2"></i>Informações</h5>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold d-block text-uppercase">Equipamento</label>
                        <span class="fs-5 fw-bold">{{ $machine->name }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold d-block text-uppercase">Série</label>
                        <code>{{ $machine->serial_number }}</code>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold d-block text-uppercase">Status</label>
                        <span class="badge {{ $machine->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ $machine->status === 'active' ? 'Ativo' : 'Inativa' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="text-primary fw-bold mb-4"><i class="fas fa-history me-2"></i>Histórico de Calibrações</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($machine->calibrations as $cal)
                                    <tr>
                                        <td>{{ $cal->calibration_date->format('d/m/Y') }}</td>
                                        <td class="fw-bold">{{ number_format($cal->value, 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $cal->status === 'approved' ? 'text-bg-success' : 'text-bg-danger' }}">
                                                {{ $cal->status === 'approved' ? 'Aprovada' : 'Rejeitada' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('calibrations.show', $cal->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Sem registros.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>