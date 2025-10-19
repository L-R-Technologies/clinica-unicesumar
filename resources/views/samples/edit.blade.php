@extends('layouts.base')

@section('content')
    @livewire('samples.edit-sample', ['sample' => $sample])
@endsection
