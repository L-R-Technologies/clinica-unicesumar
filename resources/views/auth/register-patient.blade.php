@extends('auth.register')
@section('role_fields')
    {{-- Campos específicos para patient --}}
    <div class="mb-3">
        <label for="birth_date">Data de Nascimento</label>
    <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" >
        @error('birth_date')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="ethnicity">Etnia</label>
    <input type="text" name="ethnicity" id="ethnicity" class="form-control @error('ethnicity') is-invalid @enderror" value="{{ old('ethnicity') }}" >
        @error('ethnicity')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="sex">Sexo</label>
    <select name="sex" id="sex" class="form-control @error('sex') is-invalid @enderror" >
        @error('sex')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
            <option value="male" @if(old('sex')=='male') selected @endif>Masculino</option>
            <option value="female" @if(old('sex')=='female') selected @endif>Feminino</option>
            <option value="other" @if(old('sex')=='other') selected @endif>Outro</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="cpf">CPF</label>
    <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" >
        @error('cpf')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="rg">RG</label>
    <input type="text" name="rg" id="rg" class="form-control @error('rg') is-invalid @enderror" value="{{ old('rg') }}" >
        @error('rg')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="phone">Telefone</label>
    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" >
        @error('phone')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="street">Rua</label>
    <input type="text" name="street" id="street" class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}" >
        @error('street')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="number">Número</label>
    <input type="text" name="number" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}" >
        @error('number')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="complement">Complemento</label>
    <input type="text" name="complement" id="complement" class="form-control @error('complement') is-invalid @enderror" value="{{ old('complement') }}">
        @error('complement')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="neighborhood">Bairro</label>
    <input type="text" name="neighborhood" id="neighborhood" class="form-control @error('neighborhood') is-invalid @enderror" value="{{ old('neighborhood') }}" >
        @error('neighborhood')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="city">Cidade</label>
    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" >
        @error('city')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="state">Estado</label>
    <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" >
        @error('state')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="country">País</label>
    <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}" >
        @error('country')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="zip_code">CEP</label>
    <input type="text" name="zip_code" id="zip_code" class="form-control @error('zip_code') is-invalid @enderror" value="{{ old('zip_code') }}" >
        @error('zip_code')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <input type="hidden" name="role" value="patient">
@endsection
