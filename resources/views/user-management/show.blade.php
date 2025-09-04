@extends('layouts.base')

@section('content')

    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-auto d-flex align-items-center" style="height: 100%;">
                <a href="{{ route('user-management.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="col text-center">
                <h2 class="mb-0">Detalhes do Usuário</h2>
            </div>
            <div class="col-auto"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-user"></i> Informações do Usuário</h5>
                        <div class="mb-3">
                            <strong>Nome:</strong><br>
                            <span>{{ $user->name }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong><br>
                            <span>{{ $user->email }}</span>
                            @if ($user->email_verified_at)
                                <span class="badge bg-success ms-2">Verificado</span>
                            @else
                                <span class="badge bg-warning ms-2">Não Verificado</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Tipo:</strong><br>
                            @if ($user->role === 'teacher')
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
                        @if ($user->teacher)
                            <div class="mb-3">
                                <strong>Número de Registro:</strong><br>
                                <span>{{ $user->teacher->registration_number }}</span>
                                @if ($user->teacher->crbm)
                                    <br><strong>CRBM:</strong><br>
                                    <span>{{ $user->teacher->crbm }}</span>
                                @endif
                            </div>
                        @endif
                        @if ($user->student)
                            <div class="mb-3">
                                <strong>RA:</strong><br>
                                <span>{{ $user->student->ra }}</span><br>
                                <strong>Curso:</strong><br>
                                <span>{{ $user->student->course }}</span>
                            </div>
                        @endif
                        <h5 class="mb-3 mt-4"><i class="fas fa-info-circle"></i> Informações do Sistema</h5>
                        <div class="mb-2">
                            <strong>Criado em:</strong><br>
                            <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                        <div class="mb-2">
                            <strong>Última atualização:</strong><br>
                            <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                        @if ($user->email_verified_at)
                            <div class="mb-2">
                                <strong>Email verificado em:</strong><br>
                                <small class="text-muted">{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</small>
                            </div>
                        @endif
                        <div class="d-flex justify-content-center align-items-center mt-4">
                            <a href="{{ route('user-management.edit', $user->id) }}" class="btn btn-success">Editar</a>
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
