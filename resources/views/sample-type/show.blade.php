@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <a href="{{ route('sample-type.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="col text-center">
            <h2 class="mb-0">Detalhes do Tipo de Amostra</h2>
        </div>
        <div class="col-auto"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-body">

                    <!-- Informações do Tipo de Amostra -->
                    <h5 class="mb-4"><i class="fas fa-vial"></i> Informações</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nome:</strong><br>
                            <span>{{ $sampleType->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Descrição:</strong><br>
                            <span>{{ $sampleType->description ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    <h5 class="mb-3 mt-4"><i class="fas fa-info-circle"></i> Informações do Sistema</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Criado em:</strong><br>
                            <small class="text-muted">{{ $sampleType->created_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Última atualização:</strong><br>
                            <small class="text-muted">{{ $sampleType->updated_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                        <a href="{{ route('sample-type.edit', $sampleType->id) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger"
                            onclick="confirmDelete({{ $sampleType->id }}, '{{ $sampleType->name }}')">
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
                <p>Tem certeza que deseja excluir o tipo de amostra <strong id="sampleTypeName"></strong>?</p>
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
function confirmDelete(sampleTypeId, sampleTypeName) {
    document.getElementById('sampleTypeName').textContent = sampleTypeName;
    document.getElementById('deleteForm').action = `/sample-type/${sampleTypeId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
