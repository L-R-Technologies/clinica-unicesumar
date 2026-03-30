@extends('layouts.base')

@section('content')
    @livewire('exam-type.edit-exam-type', ['examType' => $examType])
@endsection
