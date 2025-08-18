@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Editar Perfil</h2>
    <a href="{{ route('home') }}">Voltar</a>
    @if (session('status'))
            @if (session('status') === 'profile-information-updated')
                <div class="alert alert-success" role="alert">
                    Perfil atualizado com sucesso!
                </div>
            @else
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
    @endif

    <form method="POST" action="{{ route('user-profile-information.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" autofocus>
            @if($errors->updateProfileInformation->has('name'))
                <div class="text-danger">{{ $errors->updateProfileInformation->first('name') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
            @if($errors->updateProfileInformation->has('email'))
                <div class="text-danger">{{ $errors->updateProfileInformation->first('email') }}</div>
            @endif
        </div>

        @yield('role_fields')

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection
