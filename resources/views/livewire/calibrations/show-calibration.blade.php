<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('machines.show', $calibration->machine_id) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Prontuário
                </a>
                <h2 class="mb-0">Detalhes da Calibração</h2>
                <div></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">Registro #{{ $calibration->id }}</h5>
                        <span class="badge {{ $calibration->status === 'approved' ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ $calibration->status === 'approved' ? 'Aprovada' : 'Rejeitada' }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p><strong>Máquina:</strong> {{ $calibration->machine->name }}</p>
                    <p><strong>Data:</strong> {{ $calibration->calibration_date->format('d/m/Y') }}</p>
                    <p><strong>Valor:</strong> {{ $calibration->value }}</p>
                    <p><strong>Técnico:</strong> {{ $calibration->user->name }}</p>
                    <p><strong>Observações:</strong> {{ $calibration->observation ?? 'Nenhuma' }}</p>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('calibrations.edit', $calibration->id) }}" class="btn btn-outline-secondary">Editar</a>
                        <button wire:click="delete" wire:confirm="Deseja excluir?" class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>