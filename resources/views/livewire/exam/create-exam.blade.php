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
                    <h2 class="mb-0">Criar Novo Exame</h2>
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

                <div class="card mx-auto" style="max-width: 600px;">
                    <div class="card-body">
                        <form wire:submit.prevent="save">

                            <!-- Dados do Exame -->
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
                                    <label for="exam_type_id" class="form-label">Tipo de Exame *</label>
                                    <select class="form-select @error('exam_type_id') is-invalid @enderror"
                                        id="exam_type_id" wire:model="exam_type_id" required>
                                        <option value="">Selecione o tipo</option>
                                        @foreach($examTypes as $examType)
                                            <option value="{{ $examType->id }}">{{ $examType->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('exam_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                            <div class="mb-3">
                                <label for="date" class="form-label">Data e Hora do Exame *</label>
                                <input type="datetime-local" class="form-control @error('date') is-invalid @enderror"
                                    id="date" wire:model="date" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"
                                        role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <i class="fas fa-save"></i> Salvar Exame
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
