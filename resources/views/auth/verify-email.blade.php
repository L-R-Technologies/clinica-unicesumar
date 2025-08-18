@extends('layouts.base')

@section('content')
    <div class="row">
        <a href="{{ route('login') }}">Voltar</a>
        <p>Clique no link que recebeu em seu email.</p>
        <small>Caso não tenha recebido o email, clique no link abaixo.</small>

        @if (session('status'))
            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    Um novo link de verificação foi enviado para seu email!
                </div>
            @else
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        @endif

        <form action="{{ route('verification.send') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-primary">Reenviar email de verificação</button>
        </form>
    </div>
@endsection
