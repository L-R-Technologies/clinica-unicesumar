@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-auto d-flex align-items-center" style="height: 100%;">
                <a href="{{ route('exam.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="col text-center">
                <h2 class="mb-0">Detalhes do Exame</h2>
            </div>
            <div class="col-auto"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-body">
                        <!-- Informações Básicas do Exame -->
                        <h5 class="mb-4"><i class="fas fa-flask"></i> Informações do Exame</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Tipo:</strong><br>
                                <span>{{ $exam->type }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Data do Exame:</strong><br>
                                <span>{{ $exam->date->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                @switch($exam->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark fs-6">
                                            <i class="fas fa-clock"></i> Pendente
                                        </span>
                                        @break
                                    @case('pending_approval')
                                        <span class="badge bg-info fs-6">
                                            <i class="fas fa-hourglass-half"></i> Pendente de Aprovação
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle"></i> Aprovado
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle"></i> Rejeitado
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6">{{ ucfirst($exam->status) }}</span>
                                @endswitch
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Responsável:</strong><br>
                                <span>{{ $exam->user->name }}</span>
                                @if($exam->user->role === 'teacher')
                                    <small class="badge bg-primary ms-2">Professor</small>
                                @elseif($exam->user->role === 'student')
                                    <small class="badge bg-success ms-2">Estudante</small>
                                @endif
                            </div>
                        </div>

                        <!-- Informações do Paciente -->
                        <h5 class="mb-3 mt-4"><i class="fas fa-user-injured"></i> Informações do Paciente</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nome:</strong><br>
                                <span>{{ $exam->patient->user->name }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Anamnese:</strong><br>
                                <span>{{ $exam->patientHistory->date->format('d/m/Y') }}</span><br>
                                <small class="text-muted">{{ Str::limit($exam->patientHistory->description, 100) }}</small>
                            </div>
                        </div>

                        <!-- Informações da Amostra -->
                        <h5 class="mb-3 mt-4"><i class="fas fa-vial"></i> Informações da Amostra</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Código:</strong><br>
                                <span>{{ $exam->sample->code }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Tipo:</strong><br>
                                <span>{{ $exam->sample->type }}</span>
                            </div>
                        </div>

                        <!-- Observações -->
                        @if($exam->observation)
                        <h5 class="mb-3 mt-4"><i class="fas fa-sticky-note"></i> Observações</h5>
                        <div class="mb-3">
                            <p class="mb-0">{{ $exam->observation }}</p>
                        </div>
                        @endif

                        <!-- Resultados do Exame -->
                        @if($exam->results)
                        <h5 class="mb-3 mt-4"><i class="fas fa-chart-bar"></i> Resultados do Exame</h5>
                        <div class="mb-3">
                            @if(is_array($exam->results))
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Parâmetro</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($exam->results as $key => $value)
                                                <tr>
                                                    <td><strong>{{ ucfirst($key) }}</strong></td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <pre>{{ $exam->results }}</pre>
                                </div>
                            @endif
                        </div>
                        @else
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atenção:</strong> Este exame ainda não possui resultados cadastrados.
                        </div>
                        @endif

                        <!-- Justificativa de Rejeição -->
                        @if($exam->status === 'rejected' && $exam->justification_rejection)
                        <h5 class="mb-3 mt-4"><i class="fas fa-times-circle text-danger"></i> Justificativa da Rejeição</h5>
                        <div class="alert alert-danger">
                            {{ $exam->justification_rejection }}
                        </div>
                        @endif

                        <!-- Informações do Sistema -->
                        <h5 class="mb-3 mt-4"><i class="fas fa-info-circle"></i> Informações do Sistema</h5>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Criado em:</strong><br>
                                <small class="text-muted">{{ $exam->created_at->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Última atualização:</strong><br>
                                <small class="text-muted">{{ $exam->updated_at->format('d/m/Y H:i:s') }}</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                            <a href="{{ route('exam.edit', $exam->id) }}" class="btn btn-success">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $exam->id }}, '{{ $exam->type }}')">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
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
                    <p>Tem certeza que deseja excluir o exame <strong id="examType"></strong>?</p>
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
        function confirmDelete(examId, examType) {
            document.getElementById('examType').textContent = examType;
            document.getElementById('deleteForm').action = `/exam/${examId}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endsection
