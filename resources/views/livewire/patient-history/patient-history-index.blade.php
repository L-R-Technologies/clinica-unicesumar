<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Anamneses</h2>
                    <div>
                        <a href="{{ route('patient-histories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova Anamnese
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

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="patientFilter" class="form-label">Pesquisar por paciente</label>
                                <input type="text" class="form-control" id="patientFilter"
                                    wire:model.live.debounce.300ms="patientFilter"
                                    placeholder="Digite o nome do paciente...">
                            </div>
                            <div class="col-md-3">
                                <label for="professionalFilter" class="form-label">Pesquisar por profissional</label>
                                <input type="text" class="form-control" id="professionalFilter"
                                    wire:model.live.debounce.300ms="professionalFilter"
                                    placeholder="Digite o nome do profissional...">
                            </div>
                            <div class="col-md-3">
                                <label for="dateFilter" class="form-label">Filtrar por data</label>
                                <input type="date" class="form-control" id="dateFilter"
                                    wire:model.live="dateFilter">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100"
                                    wire:click="clearFilters">
                                    <i class="fas fa-times"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Anamneses -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                            <div class="col-md-4">Paciente</div>
                            <div class="col-md-4">Profissional</div>
                            <div class="col-md-2">Data</div>
                            <div class="col-md-2 text-end">Ações</div>
                        </div>

                        @forelse ($anamneses as $anamnese)
                            <div class="border rounded mb-2">
                                <div class="row g-3 px-3 py-2 align-items-center">
                                    <div class="col-md-4" data-label="Paciente">
                                        {{ $anamnese->patient->user->name ?? 'N/A' }}
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
                                            <button class="btn btn-sm btn-outline-danger" title="Excluir"
                                                wire:click="delete({{ $anamnese->id }})"
                                                wire:confirm="Tem certeza que deseja excluir esta anamnese?">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
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

                    @if ($anamneses->hasPages())
                        <div class="card-footer bg-transparent border-0">
                            {{ $anamneses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
