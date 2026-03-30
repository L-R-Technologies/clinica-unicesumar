<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('exam.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <h2 class="mb-0">Editar Exame</h2>
                    <div></div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @error('form')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @enderror

                <div class="card mx-auto" style="max-width: 800px;">
                    <div class="card-body">
                        <form wire:submit.prevent="save">

                            <!-- Informações do Exame -->
                            <h5 class="mb-3">Informações do Exame</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_id" class="form-label">Paciente *</label>
                                    <select class="form-select @error('patient_id') is-invalid @enderror"
                                        id="patient_id" wire:model.live="patient_id" required>
                                        <option value="">Selecione um paciente</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_history_id" class="form-label">Anamnese do Paciente *</label>
                                    <select class="form-select @error('patient_history_id') is-invalid @enderror"
                                        id="patient_history_id" wire:model="patient_history_id" required
                                        {{ empty($patientHistories) ? 'disabled' : '' }}>
                                        <option value="">
                                            {{ empty($patientHistories) ? 'Selecione um paciente primeiro' : 'Selecione uma anamnese' }}
                                        </option>
                                        @foreach($patientHistories as $history)
                                            <option value="{{ $history->id }}">
                                                {{ $history->recorded_at->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_history_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="exam_type_id" class="form-label">Tipo de Exame</label>
                                    <input type="text" class="form-control" id="exam_type_id"
                                           value="{{ $exam->examType->name }}" disabled readonly>
                                    <div class="form-text">O tipo do exame não pode ser alterado após a criação.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sample_id" class="form-label">Amostra *</label>
                                    <select class="form-select @error('sample_id') is-invalid @enderror"
                                        id="sample_id" wire:model="sample_id" required
                                        {{ empty($samples) ? 'disabled' : '' }}>
                                        <option value="">
                                            {{ empty($samples) ? 'Selecione um paciente primeiro' : 'Selecione uma amostra' }}
                                        </option>
                                        @foreach($samples as $sample)
                                            <option value="{{ $sample->id }}">
                                                {{ $sample->code }} - {{ $sample->sampleType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sample_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="date" class="form-label">Data do Exame *</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="date" wire:model="date" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observation" class="form-label">Observações</label>
                                <textarea class="form-control @error('observation') is-invalid @enderror"
                                    id="observation" wire:model="observation" rows="3"
                                    placeholder="Observações sobre o exame..."></textarea>
                                @error('observation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="mb-2">
                                    Status:
                                    <span class="badge fs-6
                                        @if($exam->status === 'pending') bg-warning text-dark
                                        @elseif($exam->status === 'pending_approval') bg-info
                                        @elseif($exam->status === 'approved') bg-success
                                        @elseif($exam->status === 'rejected') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        @switch($exam->status)
                                            @case('pending')
                                                Pendente
                                                @break
                                            @case('pending_approval')
                                                Pendente de Aprovação
                                                @break
                                            @case('approved')
                                                Aprovado
                                                @break
                                            @case('rejected')
                                                Rejeitado
                                                @break
                                            @default
                                                {{ ucfirst($exam->status) }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Resultados do Exame</h5>

                            @php
                                $resultsFields = $this->getResultsFields();
                            @endphp
                            @if($resultsFields->isNotEmpty())
                                <div class="row">
                                    @foreach($resultsFields as $field)
                                        <div class="col-md-6 mb-3">
                                            <label for="resultsData.{{ $field->name }}" class="form-label">
                                                {{ $field->label }}
                                                @if($field->unit)
                                                    <small class="text-muted">({{ $field->unit }})</small>
                                                @endif
                                            </label>

                                            @switch($field->field_type)
                                                @case('boolean')
                                                    <select class="form-select @error('resultsData.' . $field->name) is-invalid @enderror"
                                                        id="resultsData.{{ $field->name }}" wire:model="resultsData.{{ $field->name }}">
                                                        <option value="">Selecionar</option>
                                                        @if(str_contains(strtolower($field->name), 'hcg'))
                                                            <option value="Positivo">Positivo</option>
                                                            <option value="Negativo">Negativo</option>
                                                        @elseif(str_contains(strtolower($field->name), 'vdrl') || str_contains(strtolower($field->name), 'syphilis'))
                                                            <option value="Reagente">Reagente</option>
                                                            <option value="Não reagente">Não reagente</option>
                                                        @elseif(str_contains(strtolower($field->name), 'abo'))
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        @elseif(str_contains(strtolower($field->name), 'rh'))
                                                            <option value="Positivo">Positivo</option>
                                                            <option value="Negativo">Negativo</option>
                                                        @else
                                                            <option value="Positivo">Positivo</option>
                                                            <option value="Negativo">Negativo</option>
                                                            <option value="Presente">Presente</option>
                                                            <option value="Ausente">Ausente</option>
                                                        @endif
                                                    </select>
                                                    @break

                                                @case('int')
                                                    <input type="number" class="form-control @error('resultsData.' . $field->name) is-invalid @enderror"
                                                        id="resultsData.{{ $field->name }}" wire:model="resultsData.{{ $field->name }}"
                                                        placeholder="{{ $field->label }}">
                                                    @break

                                                @case('float')
                                                    <input type="number" step="0.01" class="form-control @error('resultsData.' . $field->name) is-invalid @enderror"
                                                        id="resultsData.{{ $field->name }}" wire:model="resultsData.{{ $field->name }}"
                                                        placeholder="{{ $field->label }}">
                                                    @break

                                                @default
                                                    <!-- Campo de texto padrão para 'string' e outros -->
                                                    <input type="text" class="form-control @error('resultsData.' . $field->name) is-invalid @enderror"
                                                        id="resultsData.{{ $field->name }}" wire:model="resultsData.{{ $field->name }}"
                                                        placeholder="{{ $field->label }}">
                                            @endswitch

                                            @error('resultsData.' . $field->name)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if($loop->iteration % 2 === 0 || $loop->last)
                                </div>
                                @if(!$loop->last && $loop->iteration % 2 === 0)
                                    <div class="row">
                                @endif
                            @endif
                        @endforeach
                    @if($resultsFields->count() % 2 !== 0)
                        </div>
                    @endif
                            @else
                                <div class="mb-3">
                                    <label for="results" class="form-label">Resultados (JSON)</label>
                                    <textarea class="form-control @error('results') is-invalid @enderror"
                                        id="results" wire:model="results" rows="6"
                                        placeholder='Exemplo: {"parametro": "valor", "parametro2": "valor2"}'></textarea>
                                    @error('results')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Digite os resultados em formato JSON. Exemplo: {"parametro1": "valor1", "parametro2": "valor2"}
                                    </div>
                                </div>
                            @endif

                            <!-- Justificativa de Rejeição (mostrar apenas se status for rejeitado) -->
                            @if($exam->status === 'rejected')
                                <div class="mb-3 mt-4">
                                    <label for="justification_rejection" class="form-label">Justificativa da Rejeição</label>
                                    <textarea class="form-control @error('justification_rejection') is-invalid @enderror"
                                        id="justification_rejection" wire:model="justification_rejection" rows="3"
                                        placeholder="Descreva os motivos da rejeição..."></textarea>
                                    @error('justification_rejection')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <!-- Informações de Status -->
                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Criado em:</strong> {{ $exam->created_at->format('d/m/Y H:i') }}
                                    </small><br>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Última atualização:</strong> {{ $exam->updated_at->format('d/m/Y H:i') }}
                                    </small><br>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"
                                        role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
