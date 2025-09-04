@extends('layouts.base')

@section('content')
    <div class="container position-relative" style="min-height: 80vh;">
        <a href="{{ route('welcome') }}"
            class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow"
            style="z-index:10;">
            <i class="fa fa-arrow-left"></i> <span>Voltar</span>
        </a>
        <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
                <div class="text-center mb-4">
                    <i class="fa fa-key fa-3x text-primary mb-2"></i>
                    <h2 class="fw-bold">Redefinir Senha</h2>
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', request('email')) }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirme a Senha</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                            required>
                    </div>
                    <div class="d-grid gap-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-lg">Redefinir Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
