<div class="mb-3">
    <label for="name" class="form-label">Nome</label>
    <input id="name" type="text" class="form-control @if ($errors->updateProfileInformation->has('name')) is-invalid @endif"
        name="name" value="{{ old('name', $user->name) }}" autofocus>
    @if ($errors->updateProfileInformation->has('name'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('name') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="email" class="form-label">E-mail</label>
    <input id="email" type="email" class="form-control @if ($errors->updateProfileInformation->has('email')) is-invalid @endif"
        name="email" value="{{ old('email', $user->email) }}">
    @if ($errors->updateProfileInformation->has('email'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('email') }}</div>
    @endif
</div>
