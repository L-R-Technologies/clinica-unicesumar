<div>
    <div class="container">
        <div class="card mx-auto" style="max-width: 800px;">
            <div class="card-body">
                <h2 class="text-center mb-4">Nova Anamnese</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    {{-- Paciente e Profissional --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Paciente *</label>
                            <select wire:model="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                                <option value="">Selecione o paciente</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data da coleta *</label>
                            <input type="date" wire:model="recorded_at" class="form-control @error('recorded_at') is-invalid @enderror" required>
                            @error('recorded_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Dados Clínicos</h5>

                    {{-- Jejum --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="fasting" wire:model.live="fasting">
                        <label class="form-check-label" for="fasting">Você está em jejum?</label>
                    </div>
                    @if($fasting)
                        <div class="ms-4 mb-3">
                            <input type="number" wire:model="fasting_hours" class="form-control" placeholder="Há quantas horas?">
                        </div>
                    @endif

                    {{-- Álcool nas últimas 24h --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="alcohol_last_24h" wire:model="alcohol_last_24h">
                        <label class="form-check-label" for="alcohol_last_24h">Consumiu álcool nas últimas 24 horas?</label>
                    </div>

                    {{-- Medicação --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="on_medication" wire:model.live="on_medication">
                        <label class="form-check-label" for="on_medication">Faz uso de medicamentos?</label>
                    </div>
                    @if($on_medication)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="medications" class="form-control" placeholder="Quais medicamentos?">
                        </div>
                    @endif

                    {{-- Suplementos --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="on_supplements" wire:model.live="on_supplements">
                        <label class="form-check-label" for="on_supplements">Faz uso de suplementos alimentares?</label>
                    </div>
                    @if($on_supplements)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="supplements" class="form-control" placeholder="Quais suplementos?">
                        </div>
                    @endif

                    {{-- Doença crônica --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="chronic_disease" wire:model.live="chronic_disease">
                        <label class="form-check-label" for="chronic_disease">Possui alguma doença crônica?</label>
                    </div>
                    @if($chronic_disease)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="chronic_disease_details" class="form-control" placeholder="Detalhes">
                        </div>
                    @endif

                    {{-- Infecções recentes --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="infectious_disease_history" wire:model.live="infectious_disease_history">
                        <label class="form-check-label" for="infectious_disease_history">Possui histórico de doenças infecciosas recentes?</label>
                    </div>
                    @if($infectious_disease_history)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="infectious_disease_details" class="form-control" placeholder="Detalhes">
                        </div>
                    @endif

                    {{-- Cirurgia recente --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="recent_surgery" wire:model.live="recent_surgery">
                        <label class="form-check-label" for="recent_surgery">Realizou algum procedimento cirúrgico recentemente?</label>
                    </div>
                    @if($recent_surgery)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="surgery_details" class="form-control" placeholder="Detalhes">
                        </div>
                    @endif

                    {{-- Alergias --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="allergies" wire:model.live="allergies">
                        <label class="form-check-label" for="allergies">Possui histórico de alergias?</label>
                    </div>
                    @if($allergies)
                        <div class="ms-4 mb-3">
                            <input type="text" wire:model="allergy_details" class="form-control" placeholder="Detalhes">
                        </div>
                    @endif

                    <h5 class="mt-4 mb-3">Dados Adicionais</h5>

                    {{-- Tabagismo --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="smokes" wire:model.live="smokes">
                        <label class="form-check-label" for="smokes">Você fuma?</label>
                    </div>
                    @if($smokes)
                        <div class="ms-4 mb-3">
                            <input type="number" wire:model="cigarettes_per_day" class="form-control" placeholder="Quantos cigarros por dia?">
                        </div>
                    @endif

                    {{-- Atividade física --}}
                    <div class="mb-3">
                        <label class="form-label">Pratica atividades físicas regularmente?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="physically_active" id="physically_active_yes" value="1">
                                <label class="form-check-label" for="physically_active_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="physically_active" id="physically_active_no" value="0">
                                <label class="form-check-label" for="physically_active_no">Não</label>
                            </div>
                        </div>
                    </div>

                    {{-- Período menstrual --}}
                    <div class="mb-3">
                        <label class="form-label">Está em período menstrual?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="menstrual_period" id="menstrual_yes" value="Sim">
                                <label class="form-check-label" for="menstrual_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="menstrual_period" id="menstrual_no" value="Não">
                                <label class="form-check-label" for="menstrual_no">Não</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="menstrual_period" id="menstrual_na" value="Não se aplica">
                                <label class="form-check-label" for="menstrual_na">Não se aplica</label>
                            </div>
                        </div>
                    </div>

                    {{-- Febre / sintomas gripais --}}
                    <div class="mb-3">
                        <label class="form-label">Teve episódios de febre ou sintomas gripais nos últimos dias?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="recent_fever_or_flu" id="fever_yes" value="1">
                                <label class="form-check-label" for="fever_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model="recent_fever_or_flu" id="fever_no" value="0">
                                <label class="form-check-label" for="fever_no">Não</label>
                            </div>
                        </div>
                    </div>

                    {{-- Observação --}}
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea wire:model="observation" class="form-control" rows="3"></textarea>
                    </div>

                    {{-- Botões --}}
                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading.remove>Salvar</span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Salvando...
                            </span>
                        </button>
                        <a href="{{ route('patient-histories.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
