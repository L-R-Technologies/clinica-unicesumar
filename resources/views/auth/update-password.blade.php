@extends('layouts.base')

@section('content')
    <div class="container position-relative" style="min-height: 80vh;">
        <a href="{{ route('home') }}"
            class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow"
            style="z-index:10;">
            <i class="fa fa-arrow-left"></i> <span>Voltar</span>
        </a>
        <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
                <div class="text-center mb-4">
                    <i class="fa fa-key fa-3x text-primary mb-2"></i>
                    <h2 class="fw-bold">Alterar Senha</h2>
                </div>
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
                        <input type="password" class="form-control @if ($errors->updatePassword->has('current_password')) is-invalid @endif"
                            id="current_password" name="current_password" autocomplete="current-password">
                        @if ($errors->updatePassword->has('current_password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control @if ($errors->updatePassword->has('password')) is-invalid @endif"
                            id="password" name="password" autocomplete="new-password">
                        @if ($errors->updatePassword->has('password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password">
                    </div>
                    <div class="d-grid gap-2 mb-2">
                        <button type="submit" class="btn btn-success btn-lg">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
