@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('user-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <h2 class="mb-0">Detalhes do Usuário</h2>
                <div>
                    <a href="{{ route('user-management.edit', $user->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user"></i> Informações Pessoais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Nome:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $user->name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $user->email }}
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success ms-2">Verificado</span>
                                    @else
                                        <span class="badge bg-warning ms-2">Não Verificado</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Tipo:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($user->role === 'teacher')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-chalkboard-teacher"></i> Professor
                                        </span>
                                    @elseif($user->role === 'student')
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-graduate"></i> Estudante
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user"></i> {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->teacher)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chalkboard-teacher"></i> Dados do Professor
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <strong>Número de Registro:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $user->teacher->registration_number }}
                                    </div>
                                </div>
                                @if($user->teacher->crbm)
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <strong>CRBM:</strong>
                                        </div>
                                        <div class="col-sm-9">
                                            {{ $user->teacher->crbm }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($user->student)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-graduate"></i> Dados do Estudante
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <strong>RA:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $user->student->ra }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <strong>Curso:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $user->student->course }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> Informações do Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>ID do Usuário:</strong><br>
                                <small class="text-muted">{{ $user->id }}</small>
                            </div>

                            <div class="mb-3">
                                <strong>Criado em:</strong><br>
                                <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i:s') }}</small>
                            </div>

                            <div class="mb-3">
                                <strong>Última atualização:</strong><br>
                                <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i:s') }}</small>
                            </div>

                            @if($user->email_verified_at)
                                <div class="mb-3">
                                    <strong>Email verificado em:</strong><br>
                                    <small class="text-muted">{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cog"></i> Ações
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('user-management.edit', $user->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Usuário
                                </a>

                                @if($user->id !== auth()->id())
                                    <button type="button" class="btn btn-danger"
                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash"></i> Excluir Usuário
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
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
