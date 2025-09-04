@include('auth.partials.profile-basic-fields')

<div class="mb-3">
    <label for="birth_date" class="form-label">Data de Nascimento</label>
    <input id="birth_date" type="date" class="form-control @if ($errors->updateProfileInformation->has('birth_date')) is-invalid @endif"
        name="birth_date" value="{{ old('birth_date', optional($user->patient)->birth_date ?? '') }}">
    @if ($errors->updateProfileInformation->has('birth_date'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('birth_date') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="ethnicity" class="form-label">Etnia</label>
    <input id="ethnicity" type="text" class="form-control @if ($errors->updateProfileInformation->has('ethnicity')) is-invalid @endif"
        name="ethnicity" value="{{ old('ethnicity', optional($user->patient)->ethnicity ?? '') }}">
    @if ($errors->updateProfileInformation->has('ethnicity'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('ethnicity') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="sex" class="form-label">Sexo</label>
    <select name="sex" id="sex" class="form-control @if ($errors->updateProfileInformation->has('sex')) is-invalid @endif">
        <option value="male" @if (old('sex', optional($user->patient)->sex ?? '') == 'male') selected @endif>Masculino</option>
        <option value="female" @if (old('sex', optional($user->patient)->sex ?? '') == 'female') selected @endif>Feminino</option>
        <option value="other" @if (old('sex', optional($user->patient)->sex ?? '') == 'other') selected @endif>Outro</option>
    </select>
    @if ($errors->updateProfileInformation->has('sex'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('sex') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="cpf" class="form-label">CPF</label>
    <input id="cpf" type="text" maxlength="14"
        class="form-control @if ($errors->updateProfileInformation->has('cpf')) is-invalid @endif" name="cpf"
        value="{{ old('cpf', optional($user->patient)->cpf ?? '') }}" x-data="{ init() { let v = $el.value.replace(/\D/g, '');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                $el.value = v; } }" x-init="init()"
        x-on:input="
                let v = $el.value.replace(/\D/g, '');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                $el.value = v;
            ">
    @if ($errors->updateProfileInformation->has('cpf'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('cpf') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="rg" class="form-label">RG</label>
    <input id="rg" type="text" class="form-control @if ($errors->updateProfileInformation->has('rg')) is-invalid @endif"
        name="rg" value="{{ old('rg', optional($user->patient)->rg ?? '') }}">
    @if ($errors->updateProfileInformation->has('rg'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('rg') }}</div>
    @endif
</div>

<div class="mb-3">
    <label for="phone" class="form-label">Telefone</label>
    <input id="phone" type="text" maxlength="15"
        class="form-control @if ($errors->updateProfileInformation->has('phone')) is-invalid @endif" name="phone"
        value="{{ old('phone', optional($user->patient)->phone ?? '') }}" x-data="{ init() { let v = $el.value.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
                $el.value = v; } }"
        x-init="init()"
        x-on:input="
                let v = $el.value.replace(/\D/g, '');
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
                $el.value = v;
            ">
    @if ($errors->updateProfileInformation->has('phone'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('phone') }}</div>
    @endif
</div>
