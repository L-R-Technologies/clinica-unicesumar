@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Home</h2>

    <a href="{{ route('user.edit', ['user' => auth()->user()->id]) }}">Editar Perfil</a>
    <br>
    <a href="{{ route('user.password-edit', ['user' => auth()->user()->id]) }}">Editar Senha</a>

    @if(auth()->check())
        <p>Bem-vindo, {{ auth()->user()->name }}!</p>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
    @endif
</div>
@endsection
