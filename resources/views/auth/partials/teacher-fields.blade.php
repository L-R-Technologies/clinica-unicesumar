@include('auth.partials.profile-basic-fields')
<div class="mb-3">
    <label for="registration_number">Matrícula</label>
    <input id="registration_number" type="text" class="form-control @if ($errors->updateProfileInformation->has('registration_number')) is-invalid @endif"
        name="registration_number"
        value="{{ old('registration_number', optional($user->teacher)->registration_number) }}">
    @if ($errors->updateProfileInformation->has('registration_number'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('registration_number') }}</div>
    @endif
</div>
<div class="mb-3">
    <label for="crbm">CRBM</label>
    <input id="crbm" type="text" class="form-control @if ($errors->updateProfileInformation->has('crbm')) is-invalid @endif"
        name="crbm" value="{{ old('crbm', optional($user->teacher)->crbm) }}">
    @if ($errors->updateProfileInformation->has('crbm'))
        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('crbm') }}</div>
    @endif
</div>
