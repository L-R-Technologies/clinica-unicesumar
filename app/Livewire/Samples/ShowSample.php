<?php

namespace App\Livewire\Samples;

use App\Models\Sample;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class ShowSample extends Component
{
    public Sample $sample;

    public function mount(Sample $sample)
    {
        $this->sample = $sample;
    }

    public function render()
    {
        return view('livewire.samples.show-sample');
    }
}
