@extends('auth.update-profile')

@section('role_fields')
    <div class="mb-3">
        <label for="ra">RA</label>
        <input id="ra" type="text" class="form-control @if ($errors->updateProfileInformation->has('ra')) is-invalid @endif"
            name="ra" value="{{ old('ra', optional($user->student)->ra) }}">
        @if ($errors->updateProfileInformation->has('ra'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('ra') }}</div>
        @endif
    </div>
    <div class="mb-3">
        <label for="course">Curso</label>
        <input id="course" type="text" class="form-control @if ($errors->updateProfileInformation->has('course')) is-invalid @endif"
            name="course" value="{{ old('course', optional($user->student)->course) }}">
        @if ($errors->updateProfileInformation->has('course'))
            <div class="text-danger">{{ $errors->updateProfileInformation->first('course') }}</div>
        @endif
    </div>
@endsection
