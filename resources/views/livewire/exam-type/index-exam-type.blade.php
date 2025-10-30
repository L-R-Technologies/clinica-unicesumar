<div>
    <div class="container">
        <div class="row">
            <div class="col-12">

                {{-- Cabeçalho --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Tipos de Exame</h2>
                    <div>
                        <a href="{{ route('exam-type.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Tipo de Exame
                        </a>
                    </div>
                </div>

                {{-- Mensagens --}}
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

                {{-- Filtros --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" id="search" class="form-control"
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Nome ou descrição...">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                                    <i class="fas fa-times"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Listagem --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        {{-- Cabeçalho desktop --}}
                        <div class="row g-3 px-3 py-2 text-muted fw-bold d-none d-md-flex">
                            <div class="col-md-4">Nome</div>
                            <div class="col-md-4">Descrição</div>
                            <div class="col-md-4 text-end">Ações</div>
                        </div>

                        {{-- Linhas --}}
                        @forelse ($examTypes as $type)
                            <div class="border rounded mb-2">
                                <div class="row g-3 px-3 py-2 align-items-center">
                                    <div class="col-md-4" data-label="Nome">{{ $type->name }}</div>
                                    <div class="col-md-4" data-label="Descrição">{{ $type->description ?? 'N/A' }}</div>
                                    <div class="col-md-4 text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('exam-type.show', $type->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('exam-type.edit', $type->id) }}"
                                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $type->id }})"
                                                    wire:loading.attr="disabled"
                                                    title="Excluir">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                Nenhum tipo de exame encontrado.
                            </div>
                        @endforelse
                    </div>

                    {{-- Paginação --}}
                    @if ($examTypes->hasPages())
                        <div class="card-footer bg-transparent border-0">
                            {{ $examTypes->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(typeId) {
    if (confirm('Tem certeza de que deseja excluir este tipo de exame?')) {
        @this.call('deleteExamType', typeId);
    }
}

// Delegação de evento para funcionar após atualizações do Livewire
document.addEventListener('click', function(e) {
    const toggle = e.target.closest('.toggle-fields');
    if (!toggle) return;

    e.preventDefault();
    const id = toggle.dataset.id;
    const el = document.getElementById(`fields-${id}`);
    if (el) el.classList.toggle('d-none');
});
</script>
