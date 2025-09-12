<div>
    {{-- 1. Container principal ajustado para "container-fluid" com espaçamento (padding) diferente --}}
    <div class="container-fluid px-5 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- 2. Título agora com a classe "text-primary" --}}
            <h1 class="h2 text-primary fw-bold">Gerenciamento de Amostras</h1>
            {{-- 3. Botão com ícone e texto para consistência --}}
            <button class="btn btn-primary d-flex align-items-center gap-2" wire:click="create">
                <i class="fa-solid fa-plus"></i> Nova Amostra
            </button>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 4. Card com a classe "border-0" para remover a borda --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                {{-- 5. Busca alinhada à direita e com largura máxima definida --}}
                <div class="d-flex justify-content-end mb-3">
                    <input type="text" class="form-control" placeholder="Buscar..." wire:model.live.debounce.300ms="search" style="max-width: 300px;">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Código Único</th>
                                <th scope="col">Paciente</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Data da Coleta</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($samples as $sample)
                                <tr>
                                    <td class="font-monospace">{{ $sample->code }}</td>
                                    <td>{{ $sample->patient->user->name ?? 'Paciente não encontrado' }}</td>
                                    <td>{{ $sample->type }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sample->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge
                                            @if($sample->status == 'stored') text-bg-success @endif
                                            @if($sample->status == 'under_review') text-bg-warning @endif
                                            @if($sample->status == 'discarded') text-bg-danger @endif
                                        ">
                                            @if($sample->status == 'under_review')
                                                Em Análise
                                            @elseif($sample->status == 'stored')
                                                Armazenada
                                            @elseif($sample->status == 'discarded')
                                                Descartada
                                            @else
                                                {{ ucfirst($sample->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        {{-- 6. Botões de ação com ícones e espaçamento (me-2) --}}
                                        <button class="btn btn-sm btn-outline-primary me-2" wire:click="edit({{ $sample->id }})">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $sample->id }})" wire:confirm="Tem certeza que deseja deletar esta amostra?">
                                            <i class="fa-solid fa-trash-can me-1"></i> Deletar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Nenhuma amostra encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 7. Paginação movida para o rodapé do card (card-footer) para consistência --}}
            @if ($samples->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $samples->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- O código do Modal permanece o mesmo --}}
    @if ($showModal)
        <div class="modal show" tabindex="-1" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditing ? 'Editar Amostra' : 'Criar Nova Amostra' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="patient_id" class="form-label">Paciente</label>
                                <select id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" wire:model.defer="patient_id">
                                    <option value="">Selecione um paciente</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Tipo de Amostra</label>
                                <input type="text" id="type" class="form-control @error('type') is-invalid @enderror" wire:model.defer="type" placeholder="Ex: Sangue, Urina">
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="form-label">Data da Coleta</label>
                                <input type="date" id="date" class="form-control @error('date') is-invalid @enderror" wire:model.defer="date">
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                 <label for="status" class="form-label">Status</label>
                                 <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model.defer="status">
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
                        <button type="button" class="btn btn-primary" wire:click="save">Salvar Amostra</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    @endif
</div>