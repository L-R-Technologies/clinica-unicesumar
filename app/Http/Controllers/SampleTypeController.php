<?php

namespace App\Http\Controllers;

use App\Models\SampleType;

class SampleTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('sample-type.create-livewire');
    }

    public function edit(SampleType $sampleType)
    {
        return view('sample-type.edit-livewire', compact('sampleType'));
    }
}
