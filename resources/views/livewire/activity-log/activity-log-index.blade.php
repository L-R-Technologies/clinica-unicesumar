<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-history"></i> Histórico de Atividades</h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filtros e Busca -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="search"
                                    wire:model.live.debounce.300ms="search" placeholder="Descrição, usuário...">
                            </div>
                            <div class="col-md-2">
                                <label for="logName" class="form-label">Tipo</label>
                                <select class="form-select" id="logName" wire:model.live="logNameFilter">
                                    <option value="">Todos</option>
                                    @foreach ($logNames as $logName)
                                        <option value="{{ $logName }}">
                                            {{ ucfirst(str_replace('_', ' ', $logName)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="event" class="form-label">Evento</label>
                                <select class="form-select" id="event" wire:model.live="eventFilter">
                                    <option value="">Todos</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="dateFrom" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="dateFrom" wire:model.live="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label for="dateTo" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="dateTo" wire:model.live="dateTo">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters"
                                    title="Limpar filtros">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Logs -->
                <div class="card">
                    <div class="card-body">
                        <div wire:loading class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>

                        <div wire:loading.remove>
                            @if ($logs->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($logs as $log)
                                        <div class="list-group-item py-3 px-2 border-0 shadow-sm mb-2 rounded bg-white">
                                            <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                                                <div
                                                    class="flex-grow-1 d-flex flex-wrap align-items-center gap-3 min-width-0">
                                                    <div class="d-flex flex-column align-items-start"
                                                        style="min-width: 120px;">
                                                        <span class="fw-semibold small text-secondary">Data/Hora</span>
                                                        <span class="mb-0">{{ $log->created_at->format('d/m/Y') }}</span>
                                                        <span
                                                            class="text-muted small">{{ $log->created_at->format('H:i:s') }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start"
                                                        style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Tipo</span>
                                                        <span class="badge bg-secondary">
                                                            {{ ucfirst(str_replace('_', ' ', $log->log_name)) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start"
                                                        style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Evento</span>
                                                        <span
                                                            class="badge
                                                            {{ match ($log->event) {
                                                                'created' => 'bg-success',
                                                                'updated' => 'bg-info',
                                                                'deleted' => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            } }}">
                                                            {{ match ($log->event) {
                                                                'created' => 'Criado',
                                                                'updated' => 'Atualizado',
                                                                'deleted' => 'Excluído',
                                                                default => ucfirst($log->event),
                                                            } }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start"
                                                        style="min-width: 150px;">
                                                        <span class="fw-semibold small text-secondary">Usuário</span>
                                                        @if ($log->causer)
                                                            <span class="mb-0 text-truncate" style="max-width: 150px;"
                                                                title="{{ $log->causer->name }}">{{ $log->causer->name }}</span>
                                                            <span class="text-muted small text-truncate"
                                                                style="max-width: 150px;"
                                                                title="{{ $log->causer->email }}">{{ $log->causer->email }}</span>
                                                        @else
                                                            <span class="text-muted">Sistema</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start flex-grow-1"
                                                        style="min-width: 150px;">
                                                        <span class="fw-semibold small text-secondary">Descrição</span>
                                                        @if ($log->description)
                                                            <span class="text-truncate" style="max-width: 250px;"
                                                                title="{{ $log->description }}">{{ $log->description }}</span>
                                                        @else
                                                            <span class="text-muted">{{ class_basename($log->subject_type) }}
                                                                #{{ $log->subject_id }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center ms-auto mt-2 mt-md-0">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('activity-logs.show', $log->id) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Paginação -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $logs->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum registro encontrado</h5>
                                    <p class="text-muted">Ajuste os filtros para encontrar os logs desejados.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
