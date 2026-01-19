<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar ao Prontuário
                </a>
                <h2 class="mb-0 fw-bold">Editar Equipamento</h2>
                <div></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form wire:submit.prevent="save">
                        @error('form') <div class="alert alert-danger">{{ $message }}</div> @enderror

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do Equipamento</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Modelo</label>
                                <input type="text" wire:model="model" class="form-control @error('model') is-invalid @enderror">
                                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nº de Série</label>
                                <input type="text" wire:model="serial_number" class="form-control @error('serial_number') is-invalid @enderror">
                                @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Localização</label>
                            <input type="text" wire:model="location" class="form-control @error('location') is-invalid @enderror">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row bg-light p-3 rounded mb-3 mx-0 border">
                            <p class="fw-bold mb-2 text-primary">
                                <i class="fas fa-ruler-combined me-1"></i> Ajuste de Limites Técnicos
                            </p>
                            <div class="col-6">
                                <label class="form-label small">Valor Mínimo</label>
                                <input type="number" step="0.01" wire:model="calibration_range_min" class="form-control @error('calibration_range_min') is-invalid @enderror">
                                @error('calibration_range_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Valor Máximo</label>
                                <input type="number" step="0.01" wire:model="calibration_range_max" class="form-control @error('calibration_range_max') is-invalid @enderror">
                                @error('calibration_range_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status de Operação</label>
                            <select wire:model="status" class="form-select">
                                <option value="active">Ativo</option>
                                <option value="calibrating">Em Manutenção / Calibração</option>
                                <option value="inactive">Inativo (Desativado)</option>
                            </select>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success py-2 fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Última atualização: {{ $machine->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>