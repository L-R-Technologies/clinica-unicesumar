@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <a href="{{ route('exam-type.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="col text-center">
            <h2 class="mb-0">Detalhes do Tipo de Exame</h2>
        </div>
        <div class="col-auto"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-body">

                    <!-- Informações do Tipo de Exame -->
                    <h5 class="mb-4"><i class="fas fa-flask"></i> Informações</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nome:</strong><br>
                            <span>{{ $examType->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Descrição:</strong><br>
                            <span>{{ $examType->description ?? '-' }}</span>
                        </div>
                    </div>

                    @if($examType->exam_type_id)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tipo Pai:</strong><br>
                                <span>{{ $examType->exam_type_id }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Campos Personalizados -->
                    @if($examType->fields->isNotEmpty())
                        <h5 class="mb-3 mt-4"><i class="fas fa-list"></i> Campos Personalizados</h5>
                        <ul class="list-group mb-4">
                            @foreach($examType->fields as $field)
                                    <li class="list-group-item">
                                        <strong>{{ $field->label }}</strong> ({{ $field->name }})<br>
                                        Tipo: {{ [
                                            'string' => 'Texto',
                                            'int' => 'Inteiro',
                                            'float' => 'Decimal',
                                            'boolean' => 'Booleano',
                                        ][$field->field_type] ?? $field->field_type }}
                                        @if($field->unit)
                                            - Unidade: {{ $field->unit }}
                                        @endif
                                    </li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Informações do Sistema -->
                    <h5 class="mb-3 mt-4"><i class="fas fa-info-circle"></i> Informações do Sistema</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Criado em:</strong><br>
                            <small class="text-muted">{{ $examType->created_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Última atualização:</strong><br>
                            <small class="text-muted">{{ $examType->updated_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                        <a href="{{ route('exam-type.edit', $examType->id) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger"
                            onclick="confirmDelete({{ $examType->id }}, '{{ $examType->name }}')">
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
                <p>Tem certeza que deseja excluir o tipo de exame <strong id="examType"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer justify-content-center">
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
function confirmDelete(examTypeId, examTypeName) {
    document.getElementById('examType').textContent = examTypeName;
    document.getElementById('deleteForm').action = `/exam-type/${examTypeId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
