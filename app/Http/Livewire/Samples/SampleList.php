<?php

namespace App\Http\Livewire\Samples;

use App\Models\Patient;
use App\Models\Sample;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination; // Importar o Auth para pegar o usuário logado

#[Layout('layouts.base')]
class SampleList extends Component
{
    use WithPagination;

    // MUDANÇA CRUCIAL 1: Usamos propriedades individuais para cada campo do formulário.
    // Isso é mais verboso, mas 100% confiável para a vinculação de dados.
    public ?int $sampleId = null;

    public ?int $patient_id = null;

    public string $type = '';

    public string $date = '';

    public string $status = '';

    public bool $showModal = false;

    public bool $isEditing = false;

    public string $search = '';

    public string $statusFilter = '';

    public string $dateFilter = '';

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        // As regras agora apontam para as propriedades individuais.
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'type' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:under_review,stored,discarded'],
        ];
    }

    // MUDANÇA 2: Função para limpar/resetar as propriedades do formulário.
    public function resetForm()
    {
        $this->reset(['sampleId', 'patient_id', 'type', 'date', 'status']);
    }

    public function create()
    {
        $this->isEditing = false;
        $this->resetForm();
        $this->date = now()->format('Y-m-d'); // Valor padrão para a data
        $this->status = 'under_review'; // Valor padrão para o status
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function edit(Sample $sample)
    {
        $this->isEditing = true;
        $this->resetErrorBag();

        // MUDANÇA 3: Preenchemos cada propriedade individualmente.
        // Isso resolve o problema dos dados não aparecerem no formulário de edição.
        $this->sampleId = $sample->id;
        $this->patient_id = $sample->patient_id;
        $this->type = $sample->type;
        $this->date = $sample->date->format('Y-m-d');
        $this->status = $sample->status;

        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules();
        if ($this->isEditing) {
            unset($rules['patient_id']); // Não valida 'patient_id' na edição
        }
        $validatedData = $this->validate($rules);

        // MUDANÇA 4: Adicionamos o user_id do usuário logado ao criar.
        // Isso resolve o problema de não salvar.
        if ($this->isEditing) {
            $sample = Sample::findOrFail($this->sampleId);
            $sample->update($validatedData);
        } else {
            $validatedData['user_id'] = Auth::id(); // Adiciona o ID do usuário logado

            // Lógica para o código único
            $lastSample = Sample::latest('id')->first();
            $nextId = $lastSample ? $lastSample->id + 1 : 1;
            $validatedData['code'] = 'SMP-'.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);

            Sample::create($validatedData);
        }

        $this->closeModal();
        session()->flash('success', 'Amostra salva com sucesso!');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function delete(Sample $sample)
    {
        $sample->delete();
        session()->flash('success', 'Amostra deletada com sucesso!');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'dateFilter']);
    }

    public function render()
    {
        // O método render continua igual, sem alterações
        $query = Sample::with(['patient.user', 'user']);
        if ($this->search) { /* ... */
        }
        if ($this->statusFilter) { /* ... */
        }
        if ($this->dateFilter) { /* ... */
        }
        $samples = $query->orderBy('date', 'desc')->paginate(10);
        $patients = Patient::with('user')->get()->sortBy('user.name');

        return view('livewire.samples.sample-list', compact('samples', 'patients'));
    }
}
