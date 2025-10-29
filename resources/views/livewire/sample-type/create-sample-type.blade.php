<div>
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="d-flex align-items-center mb-4">
                    <div>
                        <a href="{{ route('sample-type.index-view') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="flex-grow-1 text-center position-relative">
                        <h2 class="mb-0">Novo Tipo de Amostra</h2>
                    </div>
                    <div style="width: 75px;"></div> {{-- Espaçador --}}
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

                <div class="card mx-auto" style="max-width: 700px;">
                    <div class="card-body">
                        <form wire:submit.prevent="save">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="Digite o nome do tipo de amostra" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                                          wire:model="description" rows="3"
                                          placeholder="Descreva o tipo de amostra (opcional)"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success" style="background-color: #1abc9c; border-color: #1abc9c;">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></div>
                                    <i class="fas fa-save"></i> Salvar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>