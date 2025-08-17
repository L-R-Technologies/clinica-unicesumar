@extends('auth.register')
@section('role_fields')
    {{-- Campos específicos para teacher --}}
    <div class="mb-3">
        <label for="registration_number">Matrícula</label>
    <input type="text" name="registration_number" id="registration_number" class="form-control @error('registration_number') is-invalid @enderror">
        @error('registration_number')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="crbm">CRBM</label>
    <input type="text" name="crbm" id="crbm" class="form-control @error('crbm') is-invalid @enderror">
        @error('crbm')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <input type="hidden" name="role" value="teacher">
@endsection
