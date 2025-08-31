<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Usuários</h2>
                    <div>
                        <a href="{{ route('user-management.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Usuário
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
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
                                <label for="search" class="form-label">Buscar por nome ou email</label>
                                <input type="text" class="form-control" id="search" wire:model.live.debounce.300ms="search"
                                       placeholder="Digite o nome ou email...">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Filtrar por tipo</label>
                                <select class="form-select" id="role" wire:model.live="roleFilter">
                                    <option value="">Todos os tipos</option>
                                    <option value="teacher">Professores</option>
                                    <option value="student">Estudantes</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" wire:model.live="statusFilter">
                                    <option value="">Todos</option>
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                                    <i class="fas fa-times"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Usuários -->
                <div class="card">
                    <div class="card-body">
                        <div wire:loading class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>

                        <div wire:loading.remove>
                            @if($users->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($users as $user)
                                        <div class="list-group-item py-3 px-2 border-0 shadow-sm mb-2 rounded bg-white">
                                            <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                                                <div class="flex-grow-1 d-flex flex-wrap align-items-center gap-3 min-width-0">
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                                        <span class="fw-semibold small text-secondary">Nome</span>
                                                        <span class="mb-0 text-truncate" style="max-width: 180px;" title="{{ $user->name }}">{{ $user->name }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 160px;">
                                                        <span class="fw-semibold small text-secondary">E-mail</span>
                                                        <span class="text-muted small text-truncate d-block" style="max-width: 180px;" title="{{ $user->email }}">{{ $user->email }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Tipo</span>
                                                        @if($user->role === 'teacher')
                                                            <span class="badge bg-primary">Professor</span>
                                                        @elseif($user->role === 'student')
                                                            <span class="badge bg-success">Estudante</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 100px;">
                                                        <span class="fw-semibold small text-secondary">Status</span>
                                                        @if($user->active)
                                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Ativo</span>
                                                        @else
                                                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Inativo</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column align-items-start" style="min-width: 120px;">
                                                        <span class="fw-semibold small text-secondary">Criado em</span>
                                                        <span class="text-muted small">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center ms-auto mt-2 mt-md-0">
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                title="{{ $user->active ? 'Desativar usuário' : 'Ativar usuário' }}"
                                                                wire:click="toggleUserStatus({{ $user->id }})"
                                                                wire:loading.attr="disabled">
                                                            <i class="fas {{ $user->active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                        </button>
                                                        <a href="{{ route('user-management.show', $user->id) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('user-management.edit', $user->id) }}"
                                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum usuário encontrado</h5>
                                    <p class="text-muted">
                                        @if($search || $roleFilter)
                                            Tente ajustar os filtros de busca.
                                        @else
                                            Comece criando o primeiro usuário.
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
