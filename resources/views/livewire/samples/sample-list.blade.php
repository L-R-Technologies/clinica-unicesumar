<div>
    <div class="container py-4">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary d-flex align-items-center gap-2" wire:click="create">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Amostra</span>
            </button>
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
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar por código ou tipo</label>
                        <input type="text" id="search" class="form-control" placeholder="Digite o código ou tipo..."
                               wire:model.live.debounce.300ms="search">
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
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                            <i class="fa-solid fa-times"></i> Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Cabeçalho da Lista (com a nova coluna 'Localização') --}}
                <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                    <div class="col-md-2">Código Único</div>
                    <div class="col-md-2">Paciente</div>
                    <div class="col-md-2">Registrado por</div>
                    <div class="col-md-2">Localização</div>
                    <div class="col-md-1">Tipo</div>
                    <div class="col-md-1">Data</div>
                    <div class="col-md-1">Status</div>
                    <div class="col-md-1 text-end">Ações</div>
                </div>

                {{-- Laço de Repetição (com a nova coluna 'Localização') --}}
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
                            <div class="col-md-2" data-label="Localização">
                                {{ $sample->location ?? 'N/A' }}
                            </div>
                            <div class="col-md-1" data-label="Tipo">
                                {{ $sample->type }}
                            </div>
                            <div class="col-md-1" data-label="Data da Coleta">
                                {{ \Carbon\Carbon::parse($sample->date)->format('d/m/Y') }}
                            </div>
                            <div class="col-md-1" data-label="Status">
                                <span class="badge {{ match($sample->status) { 'stored' => 'text-bg-success', 'under_review' => 'text-bg-warning', 'discarded' => 'text-bg-danger', default => 'text-bg-secondary' } }}">
                                    {{ match($sample->status) { 'under_review' => 'Em Análise', 'stored' => 'Armazenada', 'discarded' => 'Descartada', default => ucfirst($sample->status) } }}
                                </span>
                            </div>
                            <div class="col-md-1 text-end">
                                <button class="btn btn-sm btn-outline-primary" title="Editar" wire:click="edit({{ $sample->id }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Deletar" wire:click="delete({{ $sample->id }})" wire:confirm="Tem certeza que deseja deletar esta amostra?"><i class="fa-solid fa-trash-can"></i></button>
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

    {{-- Modal para Criar/Editar Amostra --}}
    @if ($showModal)
        <div class="modal fade show" tabindex="-1" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit.prevent="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $isEditing ? 'Editar Amostra' : 'Nova Amostra' }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="patient_id" class="form-label">Paciente</label>
                                    <select id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" 
                                            wire:model.defer="patient_id" {{ $isEditing ? 'disabled' : '' }}>
                                        <option value="">Selecione um paciente</option>
                                        @foreach ($patients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Tipo de Amostra</label>
                                    <input type="text" id="type" class="form-control @error('type') is-invalid @enderror" 
                                           wire:model.defer="type" placeholder="Ex: Sangue, Urina">
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Data da Coleta</label>
                                    <input type="date" id="date" class="form-control @error('date') is-invalid @enderror" 
                                           wire:model.defer="date">
                                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                     <label for="status" class="form-label">Status</label>
                                     <select id="status" class="form-select @error('status') is-invalid @enderror" 
                                             wire:model.defer="status">
                                         <option value="">Selecione um status</option>
                                         <option value="under_review">Em Análise</option>
                                         <option value="stored">Armazenada</option>
                                         <option value="discarded">Descartada</option>
                                     </select>
                                     @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save">Salvar</span>
                                <span wire:loading wire:target="save">Salvando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>