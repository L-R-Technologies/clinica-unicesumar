<div class="mb-3">
    <label for="zip_code" class="form-label">CEP</label>
    <input id="zip_code" type="text" maxlength="9" class="form-control @if($errors->updateProfileInformation->has('zip_code')) is-invalid @endif" name="zip_code" value="{{ old('zip_code', optional(optional($user->patient)->address)->zip_code ?? '') }}"
        x-data="{ init() { let v = $el.value.replace(/\D/g, ''); v = v.replace(/(\d{5})(\d{1,3})$/, '$1-$2'); $el.value = v; } }"
        x-init="init()"
        x-on:input="
            let v = $el.value.replace(/\D/g, '');
            v = v.replace(/(\d{5})(\d{1,3})$/, '$1-$2');
            $el.value = v;
        "
    >
    @if($errors->updateProfileInformation->has('zip_code'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('zip_code') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="street" class="form-label">Rua</label>
    <input id="street" type="text" class="form-control @if($errors->updateProfileInformation->has('street')) is-invalid @endif" name="street" value="{{ old('street', optional(optional($user->patient)->address)->street ?? '') }}">
    @if($errors->updateProfileInformation->has('street'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('street') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="number" class="form-label">Número</label>
    <input id="number" type="text" class="form-control @if($errors->updateProfileInformation->has('number')) is-invalid @endif" name="number" value="{{ old('number', optional(optional($user->patient)->address)->number ?? '') }}">
    @if($errors->updateProfileInformation->has('number'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('number') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="complement" class="form-label">Complemento</label>
    <input id="complement" type="text" class="form-control @if($errors->updateProfileInformation->has('complement')) is-invalid @endif" name="complement" value="{{ old('complement', optional(optional($user->patient)->address)->complement ?? '') }}">
    @if($errors->updateProfileInformation->has('complement'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('complement') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="neighborhood" class="form-label">Bairro</label>
    <input id="neighborhood" type="text" class="form-control @if($errors->updateProfileInformation->has('neighborhood')) is-invalid @endif" name="neighborhood" value="{{ old('neighborhood', optional(optional($user->patient)->address)->neighborhood ?? '') }}">
    @if($errors->updateProfileInformation->has('neighborhood'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('neighborhood') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="city" class="form-label">Cidade</label>
    <input id="city" type="text" class="form-control @if($errors->updateProfileInformation->has('city')) is-invalid @endif" name="city" value="{{ old('city', optional(optional($user->patient)->address)->city ?? '') }}">
    @if($errors->updateProfileInformation->has('city'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('city') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="state" class="form-label">Estado</label>
    <input id="state" type="text" class="form-control @if($errors->updateProfileInformation->has('state')) is-invalid @endif" name="state" value="{{ old('state', optional(optional($user->patient)->address)->state ?? '') }}">
    @if($errors->updateProfileInformation->has('state'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('state') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="country" class="form-label">País</label>
    <input id="country" type="text" class="form-control @if($errors->updateProfileInformation->has('country')) is-invalid @endif" name="country" value="{{ old('country', optional(optional($user->patient)->address)->country ?? '') }}">
    @if($errors->updateProfileInformation->has('country'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('country') }}</div>
    @endif
</div>
