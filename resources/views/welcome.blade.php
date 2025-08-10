@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Bem-vindo!</h2>
    <a href="{{ route('login') }}">Login</a>
    <br>
    <a href="{{ route('register') }}">Register</a>
</div>
@endsection