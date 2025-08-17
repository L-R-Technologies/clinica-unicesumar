@extends('auth.register')
@section('role_fields')
    {{-- Campos específicos para student --}}
    <div class="mb-3">
        <label for="ra">RA</label>
        <input type="text" name="ra" id="ra" class="form-control @error('ra') is-invalid @enderror">
        @error('ra')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="course">Curso</label>
        <input type="text" name="course" id="course" class="form-control @error('course') is-invalid @enderror">
        @error('course')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <input type="hidden" name="role" value="student">
@endsection
