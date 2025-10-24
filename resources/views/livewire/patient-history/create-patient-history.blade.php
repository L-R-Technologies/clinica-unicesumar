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
                            <select wire:model="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
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
                    <div class="mb-3">
                        <label class="form-label">Você está em jejum?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('fasting') is-invalid @enderror" type="radio" wire:model.live="fasting" id="fasting_yes" value="1">
                                <label class="form-check-label" for="fasting_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('fasting') is-invalid @enderror" type="radio" wire:model.live="fasting" id="fasting_no" value="0">
                                <label class="form-check-label" for="fasting_no">Não</label>
                            </div>
                        </div>
                        @error('fasting')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="number" wire:model="fasting_hours" class="form-control @error('fasting_hours') is-invalid @enderror" placeholder="Há quantas horas? *" {{ $fasting ? '' : 'disabled' }}>
                            @error('fasting_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Álcool nas últimas 24h --}}
                    <div class="mb-3">
                        <label class="form-label">Consumiu álcool nas últimas 24 horas?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('alcohol_last_24h') is-invalid @enderror" type="radio" wire:model="alcohol_last_24h" id="alcohol_yes" value="1">
                                <label class="form-check-label" for="alcohol_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('alcohol_last_24h') is-invalid @enderror" type="radio" wire:model="alcohol_last_24h" id="alcohol_no" value="0">
                                <label class="form-check-label" for="alcohol_no">Não</label>
                            </div>
                        </div>
                        @error('alcohol_last_24h')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Medicação --}}
                    <div class="mb-3">
                        <label class="form-label">Faz uso de medicamentos?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('on_medication') is-invalid @enderror" type="radio" wire:model.live="on_medication" id="medication_yes" value="1">
                                <label class="form-check-label" for="medication_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('on_medication') is-invalid @enderror" type="radio" wire:model.live="on_medication" id="medication_no" value="0">
                                <label class="form-check-label" for="medication_no">Não</label>
                            </div>
                        </div>
                        @error('on_medication')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="medications" class="form-control @error('medications') is-invalid @enderror" placeholder="Quais medicamentos? *" {{ $on_medication ? '' : 'disabled' }}>
                            @error('medications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Suplementos --}}
                    <div class="mb-3">
                        <label class="form-label">Faz uso de suplementos alimentares?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('on_supplements') is-invalid @enderror" type="radio" wire:model.live="on_supplements" id="supplements_yes" value="1">
                                <label class="form-check-label" for="supplements_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('on_supplements') is-invalid @enderror" type="radio" wire:model.live="on_supplements" id="supplements_no" value="0">
                                <label class="form-check-label" for="supplements_no">Não</label>
                            </div>
                        </div>
                        @error('on_supplements')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="supplements" class="form-control @error('supplements') is-invalid @enderror" placeholder="Quais suplementos? *" {{ $on_supplements ? '' : 'disabled' }}>
                            @error('supplements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Doença crônica --}}
                    <div class="mb-3">
                        <label class="form-label">Possui alguma doença crônica?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('chronic_disease') is-invalid @enderror" type="radio" wire:model.live="chronic_disease" id="chronic_yes" value="1">
                                <label class="form-check-label" for="chronic_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('chronic_disease') is-invalid @enderror" type="radio" wire:model.live="chronic_disease" id="chronic_no" value="0">
                                <label class="form-check-label" for="chronic_no">Não</label>
                            </div>
                        </div>
                        @error('chronic_disease')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="chronic_disease_details" class="form-control @error('chronic_disease_details') is-invalid @enderror" placeholder="Detalhes *" {{ $chronic_disease ? '' : 'disabled' }}>
                            @error('chronic_disease_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Infecções recentes --}}
                    <div class="mb-3">
                        <label class="form-label">Possui histórico de doenças infecciosas recentes?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('infectious_disease_history') is-invalid @enderror" type="radio" wire:model.live="infectious_disease_history" id="infectious_yes" value="1">
                                <label class="form-check-label" for="infectious_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('infectious_disease_history') is-invalid @enderror" type="radio" wire:model.live="infectious_disease_history" id="infectious_no" value="0">
                                <label class="form-check-label" for="infectious_no">Não</label>
                            </div>
                        </div>
                        @error('infectious_disease_history')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="infectious_disease_details" class="form-control @error('infectious_disease_details') is-invalid @enderror" placeholder="Detalhes *" {{ $infectious_disease_history ? '' : 'disabled' }}>
                            @error('infectious_disease_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Cirurgia recente --}}
                    <div class="mb-3">
                        <label class="form-label">Realizou algum procedimento cirúrgico recentemente?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('recent_surgery') is-invalid @enderror" type="radio" wire:model.live="recent_surgery" id="surgery_yes" value="1">
                                <label class="form-check-label" for="surgery_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('recent_surgery') is-invalid @enderror" type="radio" wire:model.live="recent_surgery" id="surgery_no" value="0">
                                <label class="form-check-label" for="surgery_no">Não</label>
                            </div>
                        </div>
                        @error('recent_surgery')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="surgery_details" class="form-control @error('surgery_details') is-invalid @enderror" placeholder="Detalhes *" {{ $recent_surgery ? '' : 'disabled' }}>
                            @error('surgery_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Alergias --}}
                    <div class="mb-3">
                        <label class="form-label">Possui histórico de alergias?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('allergies') is-invalid @enderror" type="radio" wire:model.live="allergies" id="allergies_yes" value="1">
                                <label class="form-check-label" for="allergies_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('allergies') is-invalid @enderror" type="radio" wire:model.live="allergies" id="allergies_no" value="0">
                                <label class="form-check-label" for="allergies_no">Não</label>
                            </div>
                        </div>
                        @error('allergies')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="text" wire:model="allergy_details" class="form-control @error('allergy_details') is-invalid @enderror" placeholder="Detalhes *" {{ $allergies ? '' : 'disabled' }}>
                            @error('allergy_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Dados Adicionais</h5>

                    {{-- Tabagismo --}}
                    <div class="mb-3">
                        <label class="form-label">Você fuma?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('smokes') is-invalid @enderror" type="radio" wire:model.live="smokes" id="smokes_yes" value="1">
                                <label class="form-check-label" for="smokes_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('smokes') is-invalid @enderror" type="radio" wire:model.live="smokes" id="smokes_no" value="0">
                                <label class="form-check-label" for="smokes_no">Não</label>
                            </div>
                        </div>
                        @error('smokes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <input type="number" wire:model="cigarettes_per_day" class="form-control @error('cigarettes_per_day') is-invalid @enderror" placeholder="Quantos cigarros por dia? *" {{ $smokes ? '' : 'disabled' }}>
                            @error('cigarettes_per_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Atividade física --}}
                    <div class="mb-3">
                        <label class="form-label">Pratica atividades físicas regularmente?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('physically_active') is-invalid @enderror" type="radio" wire:model="physically_active" id="physically_active_yes" value="1">
                                <label class="form-check-label" for="physically_active_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('physically_active') is-invalid @enderror" type="radio" wire:model="physically_active" id="physically_active_no" value="0">
                                <label class="form-check-label" for="physically_active_no">Não</label>
                            </div>
                        </div>
                        @error('physically_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Período menstrual --}}
                    <div class="mb-3">
                        <label class="form-label">Está em período menstrual?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('menstrual_period') is-invalid @enderror" type="radio" wire:model="menstrual_period" id="menstrual_yes" value="yes">
                                <label class="form-check-label" for="menstrual_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('menstrual_period') is-invalid @enderror" type="radio" wire:model="menstrual_period" id="menstrual_no" value="no">
                                <label class="form-check-label" for="menstrual_no">Não</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('menstrual_period') is-invalid @enderror" type="radio" wire:model="menstrual_period" id="menstrual_na" value="n/a">
                                <label class="form-check-label" for="menstrual_na">Não se aplica</label>
                            </div>
                        </div>
                        @error('menstrual_period')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gestante  --}}
                    <div class="mb-3">
                        <label class="form-label">Está grávida ou amamentando?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('pregnant_or_lactating') is-invalid @enderror" type="radio" wire:model="pregnant_or_lactating" id="pregnant_yes" value="1">
                                <label class="form-check-label" for="pregnant_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('pregnant_or_lactating') is-invalid @enderror" type="radio" wire:model="pregnant_or_lactating" id="pregnant_no" value="0">
                                <label class="form-check-label" for="pregnant_no">Não</label>
                            </div>
                        </div>
                        @error('pregnant_or_lactating')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Febre / sintomas gripais --}}
                    <div class="mb-3">
                        <label class="form-label">Teve episódios de febre ou sintomas gripais nos últimos dias?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('recent_fever_or_flu') is-invalid @enderror" type="radio" wire:model="recent_fever_or_flu" id="fever_yes" value="1">
                                <label class="form-check-label" for="fever_yes">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('recent_fever_or_flu') is-invalid @enderror" type="radio" wire:model="recent_fever_or_flu" id="fever_no" value="0">
                                <label class="form-check-label" for="fever_no">Não</label>
                            </div>
                        </div>
                        @error('recent_fever_or_flu')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Observação --}}
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea wire:model="observation" class="form-control @error('observation') is-invalid @enderror" rows="3"></textarea>
                        @error('observation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
