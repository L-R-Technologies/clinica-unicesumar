@extends('auth.update-profile')

@section('role_fields')
    <div class="mb-3">
        <label for="birth_date">Data de Nascimento</label>
        <input id="birth_date" type="date" class="form-control @if ($errors->updateProfileInformation->has('birth_date')) is-invalid @endif"
            name="birth_date" value="{{ old('birth_date', optional($user->patient)->birth_date) }}">
        @if ($errors->updateProfileInformation->has('birth_date'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('birth_date') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="ethnicity">Etnia</label>
        <input id="ethnicity" type="text" class="form-control @if ($errors->updateProfileInformation->has('ethnicity')) is-invalid @endif"
            name="ethnicity" value="{{ old('ethnicity', optional($user->patient)->ethnicity) }}">
        @if ($errors->updateProfileInformation->has('ethnicity'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('ethnicity') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="sex">Sexo</label>
        <select name="sex" id="sex" class="form-control @if ($errors->updateProfileInformation->has('sex')) is-invalid @endif">
            <option value="male" @if (old('sex', optional($user->patient)->sex) == 'male') selected @endif>Masculino</option>
            <option value="female" @if (old('sex', optional($user->patient)->sex) == 'female') selected @endif>Feminino</option>
            <option value="other" @if (old('sex', optional($user->patient)->sex) == 'other') selected @endif>Outro</option>
        </select>
        @if ($errors->updateProfileInformation->has('sex'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('sex') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="cpf">CPF</label>
        <input id="cpf" type="text" class="form-control @if ($errors->updateProfileInformation->has('cpf')) is-invalid @endif"
            name="cpf" value="{{ old('cpf', optional($user->patient)->cpf) }}">
        @if ($errors->updateProfileInformation->has('cpf'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('cpf') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="rg">RG</label>
        <input id="rg" type="text" class="form-control @if ($errors->updateProfileInformation->has('rg')) is-invalid @endif"
            name="rg" value="{{ old('rg', optional($user->patient)->rg) }}">
        @if ($errors->updateProfileInformation->has('rg'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('rg') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="phone">Telefone</label>
        <input id="phone" type="text" class="form-control @if ($errors->updateProfileInformation->has('phone')) is-invalid @endif"
            name="phone" value="{{ old('phone', optional($user->patient)->phone) }}">
        @if ($errors->updateProfileInformation->has('phone'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('phone') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="street">Rua</label>
        <input id="street" type="text" class="form-control @if ($errors->updateProfileInformation->has('street')) is-invalid @endif"
            name="street" value="{{ old('street', optional(optional($user->patient)->address)->street) }}">
        @if ($errors->updateProfileInformation->has('street'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('street') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="number">Número</label>
        <input id="number" type="text" class="form-control @if ($errors->updateProfileInformation->has('number')) is-invalid @endif"
            name="number" value="{{ old('number', optional(optional($user->patient)->address)->number) }}">
        @if ($errors->updateProfileInformation->has('number'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('number') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="complement">Complemento</label>
        <input id="complement" type="text" class="form-control @if ($errors->updateProfileInformation->has('complement')) is-invalid @endif"
            name="complement" value="{{ old('complement', optional(optional($user->patient)->address)->complement) }}">
        @if ($errors->updateProfileInformation->has('complement'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('complement') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="neighborhood">Bairro</label>
        <input id="neighborhood" type="text" class="form-control @if ($errors->updateProfileInformation->has('neighborhood')) is-invalid @endif"
            name="neighborhood"
            value="{{ old('neighborhood', optional(optional($user->patient)->address)->neighborhood) }}">
        @if ($errors->updateProfileInformation->has('neighborhood'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('neighborhood') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="city">Cidade</label>
        <input id="city" type="text" class="form-control @if ($errors->updateProfileInformation->has('city')) is-invalid @endif"
            name="city" value="{{ old('city', optional(optional($user->patient)->address)->city) }}">
        @if ($errors->updateProfileInformation->has('city'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('city') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="state">Estado</label>
        <input id="state" type="text" class="form-control @if ($errors->updateProfileInformation->has('state')) is-invalid @endif"
            name="state" value="{{ old('state', optional(optional($user->patient)->address)->state) }}">
        @if ($errors->updateProfileInformation->has('state'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('state') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="country">País</label>
        <input id="country" type="text" class="form-control @if ($errors->updateProfileInformation->has('country')) is-invalid @endif"
            name="country" value="{{ old('country', optional(optional($user->patient)->address)->country) }}">
        @if ($errors->updateProfileInformation->has('country'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('country') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="zip_code">CEP</label>
        <input id="zip_code" type="text" class="form-control @if ($errors->updateProfileInformation->has('zip_code')) is-invalid @endif"
            name="zip_code" value="{{ old('zip_code', optional(optional($user->patient)->address)->zip_code) }}">
        @if ($errors->updateProfileInformation->has('zip_code'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('zip_code') }}</div>
        @endif
    </div>
@endsection
