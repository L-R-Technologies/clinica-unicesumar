@extends('layouts.base')

@section('content')
    @livewire('patient-history.show-patient-history', ['patientHistory' => $anamnese])
@endsection

