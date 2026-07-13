<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('calibrations.show', $calibration->id) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <h2 class="mb-0">Editar Calibração</h2>
                <div></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data da Calibração</label>
                            <input type="date" wire:model="calibration_date" class="form-control @error('calibration_date') is-invalid @enderror">
                            @error('calibration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Valor Medido</label>
                            <input type="number" step="0.01" wire:model="value" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select wire:model="status" class="form-select">
                                <option value="approved">Aprovada</option>
                                <option value="rejected">Rejeitada</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações Técnicas</label>
                            <textarea wire:model="observation" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-save me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>