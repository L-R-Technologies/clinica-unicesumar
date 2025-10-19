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
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                            <div class="col-md-2">Paciente</div>
                            <div class="col-md-2">Responsável</div>
                            <div class="col-md-1">Tipo</div>
                            <div class="col-md-2">Amostra</div>
                            <div class="col-md-2">Data</div>
                            <div class="col-md-1">Status</div>
                            <div class="col-md-2 text-end">Ações</div>
                        </div>

                        @forelse ($exams as $exam)
                            <div class="border rounded mb-2">
                                <div class="row g-3 px-3 py-2 align-items-center">
                                    <div class="col-md-2" data-label="Paciente">
                                        {{ $exam->patient->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-2" data-label="Responsável">
                                        {{ $exam->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-1" data-label="Tipo">
                                        {{ $exam->type }}
                                    </div>
                                    <div class="col-md-2" data-label="Amostra">
                                        {{ $exam->sample->code ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-2" data-label="Data">
                                        {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-1" data-label="Status">
                                        <span class="badge
                                            {{ match ($exam->status) {
                                                'approved' => 'text-bg-success',
                                                'pending' => 'text-bg-warning',
                                                'pending_approval' => 'text-bg-info',
                                                'rejected' => 'text-bg-danger',
                                                default => 'text-bg-secondary'
                                            } }}">
                                            {{ match ($exam->status) {
                                                'pending' => 'Pendente',
                                                'pending_approval' => 'Pendente Aprovação',
                                                'approved' => 'Aprovado',
                                                'rejected' => 'Rejeitado',
                                                default => ucfirst($exam->status)
                                            } }}
                                        </span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('exam.show', $exam->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('exam.edit', $exam->id) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" title="Excluir"
                                                onclick="confirmDelete({{ $exam->id }})"
                                                wire:loading.attr="disabled">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                Nenhum exame encontrado com os filtros aplicados.
                            </div>
                        @endforelse
                    </div>

                    @if ($exams->hasPages())
                        <div class="card-footer bg-transparent border-0">
                            {{ $exams->links() }}
                        </div>
                    @endif
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
