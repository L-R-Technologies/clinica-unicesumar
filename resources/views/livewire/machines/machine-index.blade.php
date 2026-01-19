<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2 class="fw-bold text-dark mb-0">Equipamentos Laboratoriais</h2>
        <a href="{{ route('machines.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nova Máquina
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-8">
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Buscar por nome, modelo ou série...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="active">Ativa</option>
                        <option value="inactive">Inativa</option>
                        <option value="calibrating">Em Manutenção</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$set('search', ''); $set('statusFilter', '');" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i>
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
                        <th class="ps-4">Nome</th>
                        <th>Modelo</th>
                        <th>Nº Série</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $machine)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $machine->name }}</td>
                            <td>{{ $machine->model }}</td>
                            <td><code>{{ $machine->serial_number }}</code></td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-danger',
                                        'calibrating' => 'bg-warning text-dark'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Ativa',
                                        'inactive' => 'Inativa',
                                        'calibrating' => 'Em manutenção'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$machine->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$machine->status] ?? $machine->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Prontuário">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button 
                                        wire:click="deleteMachine({{ $machine->id }})" 
                                        wire:confirm="ATENÇÃO: Isso excluirá o equipamento e TODO o histórico de calibrações. Confirmar?"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Excluir"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Nenhum equipamento encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($machines->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $machines->links() }}
            </div>
        @endif
    </div>
</div>