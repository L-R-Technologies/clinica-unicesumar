<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <h2 class="mb-0 fw-bold text-dark">Registrar Calibração</h2>
                <div></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-microscope fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $machine->name }}</h6>
                                <small>Faixa de Tolerância: <strong>{{ $machine->calibration_range_min }} - {{ $machine->calibration_range_max }}</strong></small>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        @if ($errors->has('form'))
                            <div class="alert alert-danger shadow-sm mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $errors->first('form') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Data da Calibração</label>
                            <input type="date" wire:model="calibration_date" class="form-control @error('calibration_date') is-invalid @enderror">
                            @error('calibration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Valor Medido</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-gauge-high text-primary"></i></span>
                                <input type="number" step="0.01" wire:model="value" class="form-control @error('value') is-invalid @enderror" placeholder="0.00">
                                <span class="input-group-text">Unid.</span>
                            </div>
                            @error('value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Observações (Opcional)</label>
                            <textarea wire:model="observation" class="form-control" rows="3" placeholder="Descreva detalhes técnicos se necessário..."></textarea>
                            @error('observation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success py-2 fw-bold shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fas fa-check-circle me-1"></i> Finalizar Registro
                                </span>
                                
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Processando e Validando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    O status (Aprovada/Reprovada) será calculado automaticamente pelo sistema com base nos limites do equipamento.
                </p>
            </div>
        </div>
    </div>
</div>