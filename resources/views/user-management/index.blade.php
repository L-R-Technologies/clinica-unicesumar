@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <h2 class="mb-0">Gerenciamento de Usuários</h2>
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filtros e Busca -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('user-management.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar por nome ou email</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ $search ?? '' }}" placeholder="Digite o nome ou email...">
                        </div>
                        <div class="col-md-4">
                            <label for="role" class="form-label">Filtrar por tipo</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Todos os tipos</option>
                                <option value="teacher" {{ ($roleFilter ?? '') === 'teacher' ? 'selected' : '' }}>Professores</option>
                                <option value="student" {{ ($roleFilter ?? '') === 'student' ? 'selected' : '' }}>Estudantes</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Usuários -->
            <div class="card">
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Informações Específicas</th>
                                        <th>Criado em</th>
                                        <th width="200">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->role === 'teacher')
                                                    <span class="badge bg-primary">Professor</span>
                                                @elseif($user->role === 'student')
                                                    <span class="badge bg-success">Estudante</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->teacher)
                                                    <small>
                                                        <strong>Registro:</strong> {{ $user->teacher->registration_number }}<br>
                                                        @if($user->teacher->crbm)
                                                            <strong>CRBM:</strong> {{ $user->teacher->crbm }}
                                                        @endif
                                                    </small>
                                                @elseif($user->student)
                                                    <small>
                                                        <strong>RA:</strong> {{ $user->student->ra }}<br>
                                                        <strong>Curso:</strong> {{ $user->student->course }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user-management.show', $user->id) }}"
                                                       class="btn btn-sm btn-outline-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user-management.edit', $user->id) }}"
                                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Excluir"
                                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong id="userName"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/user/management/${userId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
