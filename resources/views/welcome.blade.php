@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Bem-vindo!</h2>
    <a href="{{ route('login') }}">Login</a>
    <br>
        <a href="{{ route('register', ['role' => 'teacher']) }}">Registrar Professor</a><br>
        <a href="{{ route('register', ['role' => 'student']) }}">Registrar Aluno</a><br>
        <a href="{{ route('register', ['role' => 'patient']) }}">Registrar Paciente</a>
</div>
@endsection
