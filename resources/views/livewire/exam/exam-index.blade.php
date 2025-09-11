<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Exames</h2>
                    <div>
                        <a href="{{ route('exam.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Exame
                        </a>
                    </div>
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
                            <div class="col-md-2">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="search"
                                    wire:model.live.debounce.300ms="search" placeholder="Tipo, paciente ou responsável...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" wire:model.live="statusFilter">
                                    <option value="">Todos</option>
                                    @foreach($statusOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="typeFilter" class="form-label">Tipo</label>
                                <input type="text" class="form-control" id="typeFilter"
                                    wire:model.live.debounce.300ms="typeFilter" placeholder="Tipo do exame...">
                            </div>
                            <div class="col-md-2">
                                <label for="dateFrom" class="form-label">Data de</label>
                                <input type="date" class="form-control" id="dateFrom"
                                    wire:model.live="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label for="dateTo" class="form-label">Data até</label>
                                <input type="date" class="form-control" id="dateTo"
                                    wire:model.live="dateTo">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100"
                                    wire:click="clearFilters">
                                    <i class="fas fa-times"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Exames -->
                <div class="card">
                    <div class="card-body">
                        <div wire:loading class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>

                        <div wire:loading.remove>
                            @if ($exams->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($exams as $exam)
                                        <div class="list-group-item py-3 px-2 border-0 shadow-sm mb-2 rounded bg-white">
                                            <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                                                <div class="flex-grow-1 d-flex flex-wrap align-items-center gap-3 min-width-0">
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                                        <span class="fw-semibold small text-secondary">Tipo</span>
                                                        <span class="mb-0 text-truncate" style="max-width: 180px;"
                                                              title="{{ $exam->type }}">{{ $exam->type }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 140px;">
                                                        <span class="fw-semibold small text-secondary">Paciente</span>
                                                        <span class="text-muted small text-truncate d-block"
                                                              style="max-width: 180px;"
                                                              title="{{ $exam->patient->user->name }}">{{ $exam->patient->user->name }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 140px;">
                                                        <span class="fw-semibold small text-secondary">Responsável</span>
                                                        <span class="text-muted small text-truncate d-block"
                                                              style="max-width: 180px;"
                                                              title="{{ $exam->user->name }}">{{ $exam->user->name }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Data</span>
                                                        <span class="text-muted small">{{ $exam->date->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                                        <span class="fw-semibold small text-secondary">Status</span>
                                                        @switch($exam->status)
                                                            @case('pending')
                                                                <span class="badge bg-warning text-dark">Pendente</span>
                                                                @break
                                                            @case('pending_approval')
                                                                <span class="badge bg-info">Pendente Aprovação</span>
                                                                @break
                                                            @case('approved')
                                                                <span class="badge bg-success">Aprovado</span>
                                                                @break
                                                            @case('rejected')
                                                                <span class="badge bg-danger">Rejeitado</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ ucfirst($exam->status) }}</span>
                                                        @endswitch
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Amostra</span>
                                                        <span class="text-muted small text-truncate d-block"
                                                              style="max-width: 120px;"
                                                              title="{{ $exam->sample->type }}">{{ $exam->sample->type }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center ms-auto mt-2 mt-md-0">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('exam.show', $exam->id) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('exam.edit', $exam->id) }}"
                                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                title="Excluir"
                                                                onclick="confirmDelete({{ $exam->id }})"
                                                                wire:loading.attr="disabled">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Paginação -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $exams->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum exame encontrado</h5>
                                    <p class="text-muted">
                                        @if ($search || $statusFilter || $typeFilter || $dateFrom || $dateTo)
                                            Tente ajustar os filtros de busca.
                                        @else
                                            Comece criando o primeiro exame.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(examId) {
    if (confirm('Tem certeza de que deseja excluir este exame? Esta ação não pode ser desfeita.')) {
        @this.call('deleteExam', examId);
    }
}
</script>
