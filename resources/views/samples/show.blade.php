@extends('layouts.base')

@section('content')
    @livewire('samples.show-sample', ['sample' => $sample])
@endsection
