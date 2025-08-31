@extends('layouts.base')

@section('content')

<div class="container position-relative" style="min-height: 90vh;">
    <a href="{{ route('welcome') }}" class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow" style="z-index:10;">
        <i class="fa fa-arrow-left"></i> <span>Voltar</span>
    </a>
    <div class="d-flex align-items-center justify-content-center" style="min-height: 90vh;">
        <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
            <h2 class="fw-bold text-center mb-4">Registro</h2>
            @livewire('auth.register-wizard')
        </div>
    </div>
</div>

<style>
.step-indicator {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #e0e7ef;
    border: 2px solid #a3b8d8;
    display: inline-block;
    transition: background 0.2s, border 0.2s;
}
.step-indicator.active {
    background: #1976d2;
    border-color: #1976d2;
}
.step-line {
    width: 32px;
    height: 2px;
    background: #a3b8d8;
    display: inline-block;
}
</style>

@endsection
