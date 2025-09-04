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
                    <i class="fa fa-user-circle fa-3x text-primary mb-2"></i>
                    <h2 class="fw-bold">Login</h2>
                </div>
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('login.store') }}" method="POST" name="login">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Lembrar de mim</label>
                    </div>

                    <div class="d-grid gap-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>
                    <div class="text-center mb-2">
                        <a class="btn btn-link p-0" href="{{ route('password.request') }}">
                            Esqueceu a senha?
                        </a>
                    </div>
                    <div class="text-center mt-3">
                        <span>Não tem uma conta?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Cadastre-se aqui</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
