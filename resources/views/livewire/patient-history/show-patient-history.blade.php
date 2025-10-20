<div>
    <div class="container d-flex justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detalhes da Anamnese</h2>
                <div>
                    <a href="{{ route('patient-histories.edit', $patientHistory->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('patient-histories.index') }}" class="btn btn-primary ms-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Informações Básicas --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Informações Básicas</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Paciente:</strong> {{ $patientHistory->patient->user->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Profissional:</strong> {{ $patientHistory->user->name ?? 'N/A' }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Data da coleta:</strong> {{ $patientHistory->recorded_at->format('d/m/Y') }}
                </div>
                <div class="mb-2">
                    <strong>Criado em:</strong> {{ $patientHistory->created_at->format('d/m/Y H:i') }}
                </div>
                @if($patientHistory->updated_at != $patientHistory->created_at)
                    <div>
                        <strong>Última atualização:</strong> {{ $patientHistory->updated_at->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>

            {{-- Jejum e Álcool --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Jejum e Álcool</h5>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Jejum:</strong> {{ $patientHistory->fasting ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Horas de Jejum:</strong> {{ $patientHistory->fasting_hours ?? '-' }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Álcool (últimas 24h):</strong> {{ $patientHistory->alcohol_last_24h ? 'Sim' : 'Não' }}
                </div>
            </div>

            {{-- Medicação e Suplementos --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Medicação e Suplementos</h5>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Uso de medicamentos:</strong> {{ $patientHistory->on_medication ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Medicamentos:</strong> {{ $patientHistory->medications ?? '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Uso de suplementos:</strong> {{ $patientHistory->on_supplements ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Suplementos:</strong> {{ $patientHistory->supplements ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Condições de Saúde --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Condições de Saúde</h5>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Doença crônica:</strong> {{ $patientHistory->chronic_disease ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Detalhes:</strong> {{ $patientHistory->chronic_disease_details ?? '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Infecções recentes:</strong> {{ $patientHistory->infectious_disease_history ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Detalhes:</strong> {{ $patientHistory->infectious_disease_details ?? '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Cirurgia recente:</strong> {{ $patientHistory->recent_surgery ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Detalhes:</strong> {{ $patientHistory->surgery_details ?? '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Alergias:</strong> {{ $patientHistory->allergies ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Detalhes:</strong> {{ $patientHistory->allergy_details ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Hábitos --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Hábitos</h5>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Fumante:</strong> {{ $patientHistory->smokes ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Cigarros por dia:</strong> {{ $patientHistory->cigarettes_per_day ?? '-' }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Fisicamente ativo:</strong> {{ $patientHistory->physically_active ? 'Sim' : 'Não' }}
                </div>
            </div>

            {{-- Informações Específicas --}}
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3">Informações Específicas</h5>
                <div class="mb-2">
                    <strong>Período menstrual:</strong> {{ $patientHistory->menstrual_period ?? '-' }}
                </div>
                <div class="mb-2">
                    <strong>Febre/sintomas gripais recentes:</strong> {{ $patientHistory->recent_fever_or_flu ? 'Sim' : 'Não' }}
                </div>
            </div>

            {{-- Observações --}}
            @if($patientHistory->observation)
                <div class="card shadow-sm p-4 mb-4">
                    <h5 class="mb-3">Observações</h5>
                    <p class="mb-0">{{ $patientHistory->observation }}</p>
                </div>
            @endif

            {{-- Ações --}}
            <div class="d-flex justify-content-center mt-4 mb-4">
                <a href="{{ route('patient-histories.edit', $patientHistory->id) }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button wire:click="delete" wire:confirm="Tem certeza que deseja excluir esta anamnese?"
                    class="btn btn-danger ms-2">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>
