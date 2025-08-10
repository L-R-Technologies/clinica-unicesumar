@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Login</h2>
    <form action="{{ route('login.store') }}" method="POST" name="login">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" type="password" class="form-control" name="password" required>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary">Entrar</button>

        <a class="btn btn-link" href="{{ route('password.request') }}">
            Esqueceu a senha?
        </a>
    </form>
</div>
@endsection