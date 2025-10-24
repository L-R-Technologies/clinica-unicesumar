<div>
    <div class="container">
        <h2>Amostras</h2>
    </div>
    <div class="container py-4">

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('samples.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Amostra</span>
            </a>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar por código ou tipo</label>
                        <input type="text" id="search" class="form-control"
                            placeholder="Digite o código ou tipo..." wire:model.live.debounce.300ms="search">
                    </div>
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label">Status</label>
                        <select id="filterStatus" class="form-select" wire:model.live="statusFilter">
                            <option value="">Todos</option>
                            <option value="under_review">Em Análise</option>
                            <option value="stored">Armazenada</option>
                            <option value="discarded">Descartada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterDate" class="form-label">Data da Coleta</label>
                        <input type="date" id="filterDate" class="form-control" wire:model.live="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                            <i class="fa-solid fa-times"></i> Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                    <div class="col-md-2">Código Único</div>
                    <div class="col-md-2">Paciente</div>
                    <div class="col-md-2">Registrado por</div>
                    <div class="col-md-1">Tipo</div>
                    <div class="col-md-2">Data</div>
                    <div class="col-md-1">Status</div>
                    <div class="col-md-2 text-end">Ações</div>
                </div>

                @forelse ($samples as $sample)
                    <div class="border rounded mb-2">
                        <div class="row g-3 px-3 py-2 align-items-center">
                            <div class="col-md-2" data-label="Código Único">
                                <span class="font-monospace">{{ $sample->code }}</span>
                            </div>
                            <div class="col-md-2" data-label="Paciente">
                                {{ $sample->patient->user->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-2" data-label="Registrado por">
                                {{ $sample->user->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-1" data-label="Tipo">
                                {{ $sample->sampleType->name ?? 'N/A' }}
                            </div>
                            <div class="col-md-2" data-label="Data da Coleta">
                                {{ \Carbon\Carbon::parse($sample->date)->format('d/m/Y') }}
                            </div>
                            <div class="col-md-1" data-label="Status">
                                <span
                                    class="badge {{ match ($sample->status) {'stored' => 'text-bg-success','under_review' => 'text-bg-warning','discarded' => 'text-bg-danger',default => 'text-bg-secondary'} }}">
                                    {{ match ($sample->status) {'under review' => 'Em Análise','stored' => 'Armazenada','discarded' => 'Descartada',default => ucfirst($sample->status)} }}
                                </span>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('samples.show', $sample->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Visualizar">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('samples.edit', $sample->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Deletar"
                                        wire:click="delete({{ $sample->id }})"
                                        wire:confirm="Tem certeza que deseja deletar esta amostra?">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        Nenhuma amostra encontrada com os filtros aplicados.
                    </div>
                @endforelse
            </div>

            @if ($samples->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $samples->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
