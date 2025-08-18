@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Alterar Senha</h2>
    <a href="{{ route('home') }}">Voltar</a>
    @if (session('status'))
            @if (session('status') === 'password-updated')
                <div class="alert alert-success" role="alert">
                    Senha alterada com sucesso!
                </div>
            @else
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
    @endif

    <form method="POST" action="{{ route('user-password.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="current_password" class="form-label">Senha Atual</label>
            <input type="password" class="form-control"
                id="current_password" name="current_password" autocomplete="current-password">
            @if($errors->updatePassword->has('current_password'))
                <div class="text-danger">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nova Senha</label>
            <input type="password" class="form-control"
                id="password" name="password" autocomplete="new-password">
            @if($errors->updatePassword->has('password'))
                <div class="text-danger">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Alterar Senha</button>
    </form>
</div>
@endsection
