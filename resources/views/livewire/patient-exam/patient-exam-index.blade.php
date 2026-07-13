<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Meus Exames</h2>
                </div>

                <!-- Filtros e Busca -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="search"
                                    wire:model.live.debounce.300ms="search" placeholder="Tipo de exame...">
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
                                <select class="form-select" id="typeFilter" wire:model.live="typeFilter">
                                    <option value="">Todos os tipos</option>
                                    @foreach($examTypes as $examType)
                                        <option value="{{ $examType->id }}">{{ $examType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="dateFrom" class="form-label">Data de</label>
                                <input type="date" class="form-control" id="dateFrom" wire:model.live="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label for="dateTo" class="form-label">Data até</label>
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

                <!-- Lista de Exames -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                            <div class="col-md-3">Tipo</div>
                            <div class="col-md-2">Amostra</div>
                            <div class="col-md-2">Data</div>
                            <div class="col-md-2">Status</div>
                            <div class="col-md-3 text-end">Ações</div>
                        </div>

                        @forelse ($exams as $exam)
                            <div class="border rounded mb-2">
                                <div class="row g-3 px-3 py-2 align-items-center">
                                    <div class="col-md-3" data-label="Tipo">
                                        {{ $exam->examType->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-2" data-label="Amostra">
                                        {{ $exam->sample->code ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-2" data-label="Data">
                                        {{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-2" data-label="Status">
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
                                                'pending_approval' => 'Pendente de Aprovação',
                                                'approved' => 'Aprovado',
                                                'rejected' => 'Rejeitado',
                                                default => ucfirst($exam->status)
                                            } }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <a href="{{ route('patient-exams.pdf', $exam->id) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank"
                                            title="Visualizar / Imprimir PDF">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                Nenhum exame encontrado.
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
