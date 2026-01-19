<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2 class="fw-bold text-primary mb-0">Gerenciamento de Calibrações</h2>
        <div class="d-flex gap-2">
            <button wire:click="export" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-file-export me-1"></i> Exportar CSV
            </button>
            <a href="{{ route('machines.index') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Nova Calibração
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">Buscar Equipamento</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input wire:model.live="search" type="text" class="form-control border-start-0 bg-light" placeholder="Nome ou modelo da máquina...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Status</label>
                    <select wire:model.live="statusFilter" class="form-select bg-light">
                        <option value="">Todos os Status</option>
                        <option value="approved">Aprovadas</option>
                        <option value="rejected">Rejeitadas</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small">Filtrar por Máquina</label>
                    <select wire:model.live="machineFilter" class="form-select bg-light">
                        <option value="">Todas as Máquinas</option>
                        @foreach($machines as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->serial_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button wire:click="$set('search', ''); $set('statusFilter', ''); $set('machineFilter', '');" class="btn btn-outline-danger w-100">
                        <i class="fas fa-eraser me-1"></i> Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Máquina / Série</th>
                        <th class="py-3">Data Realizada</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calibrations as $cal)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $cal->machine->name }}</div>
                                <small class="text-muted">Série: <code>{{ $cal->machine->serial_number }}</code></small>
                            </td>
                            <td>{{ $cal->calibration_date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $cal->status === 'approved' ? 'text-bg-success' : 'text-bg-danger' }} px-3 py-2 shadow-sm">
                                    {{ $cal->status === 'approved' ? 'Aprovada' : 'Rejeitada' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('calibrations.show', $cal->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('calibrations.edit', $cal->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Nenhuma calibração cadastrada ou encontrada nos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3">
            {{ $calibrations->links() }}
        </div>
    </div>
</div>