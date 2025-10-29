<div>
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="d-flex align-items-center mb-4">
                    <div>
                        <a href="{{ route('exam-type.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="flex-grow-1 text-center position-relative">
                        <h2 class="mb-0">Criar Novo Tipo de Exame</h2>
                    </div>
                    <div style="width: 75px;"></div>
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

                            <h5 class="mb-3">Informações do Tipo de Exame</h5>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="Digite o nome do tipo de exame" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          wire:model="description" rows="3"
                                          placeholder="Descreva o tipo de exame (opcional)"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Campos Personalizados --}}
                            <h5 class="mb-3">Campos Personalizados</h5>

                            @foreach ($fields as $index => $field)
                                <div class="border rounded p-3 mb-3">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Nome *</label>
                                            <input type="text" class="form-control"
                                                   wire:model="fields.{{ $index }}.name"
                                                   placeholder="nome_interno">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Rótulo *</label>
                                            <input type="text" class="form-control"
                                                   wire:model="fields.{{ $index }}.label"
                                                   placeholder="Nome visível">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Tipo</label>
                                            <select class="form-select"
                                                    wire:model="fields.{{ $index }}.field_type">
                                                <option value="text">Texto</option>
                                                <option value="number">Número</option>
                                                <option value="date">Data</option>
                                                <option value="select">Lista</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Unidade</label>
                                            <input type="text" class="form-control"
                                                   wire:model="fields.{{ $index }}.unit"
                                                   placeholder="ex: mg/dL">
                                        </div>

                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeField({{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mb-4 text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addField">
                                    <i class="fas fa-plus"></i> Adicionar Campo
                                </button>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></div>
                                    <i class="fas fa-save"></i> Salvar Tipo de Exame
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
