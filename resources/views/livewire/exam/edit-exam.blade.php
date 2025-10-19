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
                                                {{ $history->date->format('d/m/Y') }}
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
                                    <label for="date" class="form-label">Data e Hora do Exame *</label>
                                    <input type="datetime-local" class="form-control @error('date') is-invalid @enderror"
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
                                $resultsLabels = $this->getResultsLabels();
                            @endphp
                            @if(!empty($resultsLabels))
                                <!-- Campos específicos para o tipo de exame -->
                                <div class="row">
                                    @foreach($resultsLabels as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label for="resultsData.{{ $key }}" class="form-label">{{ $label }}</label>

                                            @if(in_array($key, ['hcg', 'vdrl', 'rapid_test_syphilis', 'nitrite', 'protein', 'ketones', 'glucose', 'bilirubin', 'hemoglobin', 'leukocytes', 'mucus', 'blood', 'mucus_filaments', 'casts', 'crystals', 'helminth_eggs', 'protozoa']))
                                                <!-- Campos de seleção para resultados qualitativos -->
                                                <select class="form-select @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}">
                                                    <option value="">Selecionar</option>
                                                    @if($key === 'hcg')
                                                        <option value="Positivo">Positivo</option>
                                                        <option value="Negativo">Negativo</option>
                                                    @elseif(in_array($key, ['vdrl', 'rapid_test_syphilis']))
                                                        <option value="Reagente">Reagente</option>
                                                        <option value="Não reagente">Não reagente</option>
                                                    @elseif(in_array($key, ['nitrite', 'protein', 'ketones', 'glucose', 'bilirubin', 'hemoglobin', 'leukocytes']))
                                                        <option value="Positivo">Positivo</option>
                                                        <option value="Negativo">Negativo</option>
                                                        <option value="Traços">Traços</option>
                                                    @elseif(in_array($key, ['mucus', 'blood']))
                                                        <option value="Presente">Presente</option>
                                                        <option value="Ausente">Ausente</option>
                                                    @elseif(in_array($key, ['mucus_filaments', 'casts', 'crystals', 'helminth_eggs', 'protozoa']))
                                                        <option value="Presentes">Presentes</option>
                                                        <option value="Ausentes">Ausentes</option>
                                                        <option value="Não encontrados">Não encontrados</option>
                                                    @endif
                                                </select>
                                            @elseif($key === 'abo_group')
                                                <!-- Campo específico para grupo ABO -->
                                                <select class="form-select @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}">
                                                    <option value="">Selecionar</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="AB">AB</option>
                                                    <option value="O">O</option>
                                                </select>
                                            @elseif($key === 'rh_factor')
                                                <!-- Campo específico para fator Rh -->
                                                <select class="form-select @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}">
                                                    <option value="">Selecionar</option>
                                                    <option value="Positivo">Positivo</option>
                                                    <option value="Negativo">Negativo</option>
                                                </select>
                                            @elseif(in_array($key, ['color', 'aspect', 'consistency', 'urobilinogen']))
                                                <!-- Campos de texto com sugestões -->
                                                <input type="text" class="form-control @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}"
                                                    placeholder="@if($key === 'color') Ex: Amarelo claro, Amarelo escuro @elseif($key === 'aspect') Ex: Límpido, Turvo @elseif($key === 'consistency') Ex: Pastosa, Líquida, Endurecida @elseif($key === 'urobilinogen') Ex: Normal, Aumentado @endif">
                                            @elseif(in_array($key, ['observations', 'gram_stain_result', 'growth_result', 'final_result']))
                                                <!-- Campos de texto longo -->
                                                <textarea class="form-control @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}" rows="3"
                                                    placeholder="{{ $label }}"></textarea>
                                            @else
                                                <!-- Campos de texto padrão -->
                                                <input type="text" class="form-control @error('resultsData.' . $key) is-invalid @enderror"
                                                    id="resultsData.{{ $key }}" wire:model="resultsData.{{ $key }}"
                                                    placeholder="{{ $label }}">
                                            @endif

                                            @error('resultsData.' . $key)
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
                    @if(count($resultsLabels) % 2 !== 0)
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
