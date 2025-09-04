@extends('layouts.base')

@section('content')
    <div class="container position-relative" style="min-height: 80vh;">
        <a href="{{ route('login') }}"
            class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow"
            style="z-index:10;">
            <i class="fa fa-arrow-left"></i> <span>Voltar</span>
        </a>
        <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
                <div class="text-center mb-4">
                    <i class="fa fa-unlock-alt fa-3x text-primary mb-2"></i>
                    <h2 class="fw-bold">Recuperar Senha</h2>
                </div>
                @if (session('status'))
                    <div class="alert alert-success text-center" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enviar link de redefinição</button>
                </form>
            </div>
        </div>
    </div>
@endsection
