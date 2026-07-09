<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <a href="{{ route('machines.index') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <h2 class="mb-0 fw-bold">Novo Equipamento</h2>
                <div></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form wire:submit.prevent="save">
                        @error('form') <div class="alert alert-danger">{{ $message }}</div> @enderror

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Equipamento</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ex: Analisador Bioquímico">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Modelo</label>
                                <input type="text" wire:model="model" class="form-control @error('model') is-invalid @enderror" placeholder="Ex: BX-45">
                                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nº de Série</label>
                                <input type="text" wire:model="serial_number" class="form-control @error('serial_number') is-invalid @enderror" placeholder="Ex: SN123456">
                                @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Localização</label>
                            <input type="text" wire:model="location" class="form-control @error('location') is-invalid @enderror" placeholder="Ex: Laboratório A">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row bg-light p-3 rounded mb-3 mx-0 border">
                            <p class="fw-bold mb-2 text-primary"><i class="fas fa-ruler-combined me-1"></i> Limites de Calibração</p>
                            <div class="col-6">
                                <label class="form-label small">Valor Mínimo</label>
                                <input type="number" step="0.01" wire:model="calibration_range_min" class="form-control @error('calibration_range_min') is-invalid @enderror">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Valor Máximo</label>
                                <input type="number" step="0.01" wire:model="calibration_range_max" class="form-control @error('calibration_range_max') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Inicial</label>
                            <select wire:model="status" class="form-select">
                                <option value="active">Ativo (Pronto para uso)</option>
                                <option value="inactive">Inativo (Fora de operação)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> Cadastrar Máquina
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>