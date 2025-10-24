@extends('layouts.base')

@section('content')
    @livewire('patient-history.edit-patient-history', ['patientHistory' => $anamnese])
@endsection
