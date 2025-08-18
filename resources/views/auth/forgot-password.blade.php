@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Recuperar Senha</h2>
    <a href="{{ route('login') }}">Voltar</a>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary">Enviar link de redefinição</button>
    </form>
</div>
@endsection
