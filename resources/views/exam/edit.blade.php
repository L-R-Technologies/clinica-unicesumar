@extends('layouts.base')

@section('content')
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

                <div class="card mx-auto" style="max-width: 800px;">
                    <div class="card-body">
                        <form method="POST" action="{{ route('exam.update', $exam->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Informações do Exame -->
                            <h5 class="mb-3">Informações do Exame</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_id" class="form-label">Paciente *</label>
                                    <select class="form-select @error('patient_id') is-invalid @enderror"
                                        id="patient_id" name="patient_id" required onchange="updatePatientHistories()">
                                        <option value="">Selecione um paciente</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}"
                                                {{ old('patient_id', $exam->patient_id) == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_history_id" class="form-label">Histórico do Paciente *</label>
                                    <select class="form-select @error('patient_history_id') is-invalid @enderror"
                                        id="patient_history_id" name="patient_history_id" required>
                                        <option value="">Selecione um histórico</option>
                                        @foreach($patientHistories as $history)
                                            <option value="{{ $history->id }}"
                                                {{ old('patient_history_id', $exam->patient_history_id) == $history->id ? 'selected' : '' }}>
                                                {{ $history->date->format('d/m/Y') }} - {{ Str::limit($history->description, 50) }}
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
                                    <label for="type" class="form-label">Tipo de Exame *</label>
                                    <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        @foreach($examTypes as $examType)
                                            <option value="{{ $examType }}"
                                                {{ old('type', $exam->type) == $examType ? 'selected' : '' }}>
                                                {{ $examType }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sample_id" class="form-label">Amostra *</label>
                                    <select class="form-select @error('sample_id') is-invalid @enderror"
                                        id="sample_id" name="sample_id" required>
                                        <option value="">Selecione uma amostra</option>
                                        @foreach($samples as $sample)
                                            <option value="{{ $sample->id }}"
                                                {{ old('sample_id', $exam->sample_id) == $sample->id ? 'selected' : '' }}>
                                                {{ $sample->type }}
                                                @if($sample->description)
                                                    - {{ $sample->description }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sample_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Data e Hora do Exame *</label>
                                    <input type="datetime-local" class="form-control @error('date') is-invalid @enderror"
                                        id="date" name="date" value="{{ old('date', $exam->date->format('Y-m-d\TH:i')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                        @foreach($statusOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('status', $exam->status) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observation" class="form-label">Observações</label>
                                <textarea class="form-control @error('observation') is-invalid @enderror"
                                    id="observation" name="observation" rows="3"
                                    placeholder="Observações sobre o exame...">{{ old('observation', $exam->observation) }}</textarea>
                                @error('observation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Resultados do Exame (disponível apenas na edição) -->
                            <h5 class="mb-3 mt-4">Resultados do Exame</h5>

                            <div class="mb-3">
                                <label for="results" class="form-label">Resultados (JSON)</label>
                                <textarea class="form-control @error('results') is-invalid @enderror"
                                    id="results" name="results" rows="6"
                                    placeholder='Exemplo: {"hemoglobina": "12.5 g/dL", "leucocitos": "7000/µL"}'>{{ old('results', is_array($exam->results) ? json_encode($exam->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $exam->results) }}</textarea>
                                @error('results')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Digite os resultados em formato JSON. Exemplo: {"parametro1": "valor1", "parametro2": "valor2"}
                                </div>
                            </div>

                            <!-- Justificativa de Rejeição (se status for rejeitado) -->
                            <div id="rejectionJustification" style="{{ old('status', $exam->status) == 'rejected' ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="justification_rejection" class="form-label">Justificativa da Rejeição</label>
                                    <textarea class="form-control @error('justification_rejection') is-invalid @enderror"
                                        id="justification_rejection" name="justification_rejection" rows="3"
                                        placeholder="Descreva os motivos da rejeição...">{{ old('justification_rejection', $exam->justification_rejection) }}</textarea>
                                    @error('justification_rejection')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Criado em:</strong> {{ $exam->created_at->format('d/m/Y H:i') }}
                                    </small><br>
                                    <small class="text-muted">
                                        <strong>Responsável:</strong> {{ $exam->user->name }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Última atualização:</strong> {{ $exam->updated_at->format('d/m/Y H:i') }}
                                    </small><br>
                                    <small class="text-muted">
                                        <strong>Paciente:</strong> {{ $exam->patient->name }}
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// Controlar exibição da justificativa de rejeição
document.getElementById('status').addEventListener('change', function() {
    const rejectionDiv = document.getElementById('rejectionJustification');
    if (this.value === 'rejected') {
        rejectionDiv.style.display = 'block';
    } else {
        rejectionDiv.style.display = 'none';
        document.getElementById('justification_rejection').value = '';
    }
});

// Atualizar históricos quando mudar paciente
function updatePatientHistories() {
    const patientId = document.getElementById('patient_id').value;
    const historySelect = document.getElementById('patient_history_id');

    if (!patientId) {
        historySelect.innerHTML = '<option value="">Selecione um paciente primeiro</option>';
        historySelect.disabled = true;
        return;
    }

    historySelect.disabled = true;
    historySelect.innerHTML = '<option value="">Carregando...</option>';

    fetch(`{{ route('exam.patient-histories') }}?patient_id=${patientId}`)
        .then(response => response.json())
        .then(data => {
            historySelect.innerHTML = '<option value="">Selecione um histórico</option>';
            data.forEach(history => {
                const option = document.createElement('option');
                option.value = history.id;
                option.textContent = `${new Date(history.date).toLocaleDateString('pt-BR')} - ${history.description.substring(0, 50)}`;
                historySelect.appendChild(option);
            });
            historySelect.disabled = false;
        })
        .catch(error => {
            console.error('Erro:', error);
            historySelect.innerHTML = '<option value="">Erro ao carregar históricos</option>';
            historySelect.disabled = false;
        });
}
</script>
@endsection
