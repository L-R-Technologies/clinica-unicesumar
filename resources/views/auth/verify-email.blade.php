@extends('layouts.base', ['forceGuestNavbar' => true])

@section('content')
    <div class="container position-relative" style="min-height: 80vh;">
        <form method="POST" action="{{ route('logout') }}" class="position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow" style="z-index:10;">
            @csrf
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="fa fa-arrow-left"></i> <span>Voltar</span>
            </button>
        </form>
        <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
                <div class="text-center mb-4">
                    <i class="fa fa-envelope fa-3x text-primary mb-2"></i>
                    <h2 class="fw-bold">Verificação de Email</h2>
                </div>
                <p class="mb-2 text-center">Clique no link que recebeu em seu email para confirmar seu cadastro.</p>
                <small class="text-muted d-block text-center mb-3">Caso não tenha recebido o email, clique no botão abaixo para reenviar.</small>

                @if (session('status'))
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-3 text-center" role="alert">
                            Um novo link de verificação foi enviado para seu email!
                        </div>
                    @else
                        <div class="alert alert-success mt-3 text-center" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                @endif

                <form action="{{ route('verification.send') }}" method="post" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">Reenviar email de verificação</button>
                </form>
            </div>
        </div>
    </div>
@endsection
