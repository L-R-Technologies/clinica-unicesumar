<div>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('samples.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <h2 class="mb-0">Detalhes da Amostra</h2>
            <div></div>
        </div>

        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                
                <h5 class="mb-3"><i class="fa-solid fa-flask text-primary me-2"></i> Informações da Amostra</h5>
                
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Código Único</strong>
                        <span class="font-monospace">{{ $sample->code }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Paciente</strong>
                        <span>{{ $sample->patient->user->name ?? 'N/A' }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Registrado por</strong>
                        <span>{{ $sample->user->name ?? 'N/A' }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Tipo de Amostra</strong>
                        <span>{{ $sample->type }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Data da Coleta</strong>
                        <span>{{ \Carbon\Carbon::parse($sample->date)->format('d/m/Y') }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Status</strong>
                        <span class="badge {{ match($sample->status) { 'stored' => 'text-bg-success', 'under_review' => 'text-bg-warning', 'discarded' => 'text-bg-danger', default => 'text-bg-secondary' } }}">
                            {{ match($sample->status) { 'under_review' => 'Em Análise', 'stored' => 'Armazenada', 'discarded' => 'Descartada', default => ucfirst($sample->status) } }}
                        </span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Localização</strong>
                        <span>{{ $sample->location ?? 'Não informada' }}</span>
                    </li>
                </ul>

                <hr class="my-4">

                <h5 class="mb-3"><i class="fa-solid fa-clock text-primary me-2"></i> Informações do Sistema</h5>
                
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Criado em</strong>
                        <span>{{ $sample->created_at->format('d/m/Y H:i:s') }}</span>
                    </li>
                    <li class="mb-3">
                        <strong class="text-secondary d-block">Última atualização</strong>
                        <span>{{ $sample->updated_at->format('d/m/Y H:i:s') }}</span>
                    </li>
                </ul>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('samples.edit', $sample->id) }}" class="btn btn-success">Editar</a>
                </div>
            </div>
        </div>
    </div>
</div>