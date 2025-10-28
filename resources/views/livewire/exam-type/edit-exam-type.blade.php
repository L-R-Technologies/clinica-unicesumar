<div>
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('exam-type.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <h2 class="mb-0">Editar Tipo de Exame</h2>
                    <div></div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card mx-auto" style="max-width: 600px;">
                    <div class="card-body">
                        <form wire:submit.prevent="save">

                            <h5 class="mb-3">Informações do Tipo de Exame</h5>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       wire:model="name"
                                       placeholder="Digite o nome do tipo de exame"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          wire:model="description"
                                          rows="3"
                                          placeholder="Descreva o tipo de exame (opcional)"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Informações de status --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Criado em:</strong> {{ $examType->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Atualizado em:</strong> {{ $examType->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
