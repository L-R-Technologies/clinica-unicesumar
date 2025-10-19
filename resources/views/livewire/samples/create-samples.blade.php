<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('samples.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <h2 class="mb-0">Criar Nova Amostra</h2>
                    <div></div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <h5 class="mb-3">Dados da Amostra</h5>

                            <div class="mb-3">
                                <label for="patient_id" class="form-label">Paciente</label>
                                <select id="patient_id" class="form-select @error('patient_id') is-invalid @enderror"
                                        wire:model.defer="patient_id">
                                    <option value="">Selecione um paciente</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sample_type_id" class="form-label">Tipo de Amostra</label>
                                <select id="sample_type_id" class="form-select @error('sample_type_id') is-invalid @enderror"
                                        wire:model.defer="sample_type_id">
                                    <option value="">Selecione o tipo de amostra</option>
                                    @foreach ($sampleTypes as $sampleType)
                                        <option value="{{ $sampleType->id }}">{{ $sampleType->name }}</option>
                                    @endforeach
                                </select>
                                @error('sample_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Data da Coleta</label>
                                <input type="date" id="date" class="form-control @error('date') is-invalid @enderror"
                                    wire:model.defer="date">
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" class="form-select @error('status') is-invalid @enderror"
                                        wire:model.defer="status">
                                    <option value="under_review">Em Análise</option>
                                    <option value="stored">Armazenada</option>
                                    <option value="discarded">Descartada</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Localização (Opcional)</label>
                                <input type="text" id="location" class="form-control @error('location') is-invalid @enderror"
                                       wire:model.defer="location" placeholder="Ex: Geladeira 1, Gaveta B">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
