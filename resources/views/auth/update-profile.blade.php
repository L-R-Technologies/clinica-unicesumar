@extends('layouts.base')

@section('content')
<div class="container position-relative" style="min-height: 80vh;">
    <a href="{{ route('home') }}" class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow" style="z-index:10;">
        <i class="fa fa-arrow-left"></i> <span>Voltar</span>
    </a>
    <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="max-width: 900px; width: 100%;">
            <div class="text-center mb-4">
                <i class="fa fa-user-circle fa-3x text-primary mb-2"></i>
                <h2 class="fw-bold">Editar Perfil</h2>
            </div>
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


                {{-- Campos específicos por role --}}
                @if(auth()->user()->hasRole('student'))
                    @include('auth.partials.profile-name-email')
                    <div class="mb-3">
                        <label for="ra">RA</label>
                        <input id="ra" type="text" class="form-control @if ($errors->updateProfileInformation->has('ra')) is-invalid @endif" name="ra" value="{{ old('ra', optional($user->student)->ra) }}">
                        @if ($errors->updateProfileInformation->has('ra'))
                            <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('ra') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="course">Curso</label>
                        <input id="course" type="text" class="form-control @if ($errors->updateProfileInformation->has('course')) is-invalid @endif" name="course" value="{{ old('course', optional($user->student)->course) }}">
                        @if ($errors->updateProfileInformation->has('course'))
                            <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('course') }}</div>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasRole('teacher'))
                    @include('auth.partials.profile-name-email')
                    <div class="mb-3">
                        <label for="registration_number">Matrícula</label>
                        <input id="registration_number" type="text" class="form-control @if ($errors->updateProfileInformation->has('registration_number')) is-invalid @endif" name="registration_number" value="{{ old('registration_number', optional($user->teacher)->registration_number) }}">
                        @if ($errors->updateProfileInformation->has('registration_number'))
                            <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('registration_number') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="crbm">CRBM</label>
                        <input id="crbm" type="text" class="form-control @if ($errors->updateProfileInformation->has('crbm')) is-invalid @endif" name="crbm" value="{{ old('crbm', optional($user->teacher)->crbm) }}">
                        @if ($errors->updateProfileInformation->has('crbm'))
                            <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('crbm') }}</div>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasRole('patient'))
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Informações Gerais</h5>
                                @include('auth.partials.profile-name-email')
                                <div class="mb-3">
                                    <label for="birth_date">Data de Nascimento</label>
                                    <input id="birth_date" type="date" class="form-control @if ($errors->updateProfileInformation->has('birth_date')) is-invalid @endif" name="birth_date" value="{{ old('birth_date', optional($user->patient)->birth_date) }}">
                                    @if ($errors->updateProfileInformation->has('birth_date'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('birth_date') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="ethnicity">Etnia</label>
                                    <input id="ethnicity" type="text" class="form-control @if ($errors->updateProfileInformation->has('ethnicity')) is-invalid @endif" name="ethnicity" value="{{ old('ethnicity', optional($user->patient)->ethnicity) }}">
                                    @if ($errors->updateProfileInformation->has('ethnicity'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('ethnicity') }}</div>
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
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('sex') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="cpf">CPF</label>
                                    <input id="cpf" type="text" class="form-control @if ($errors->updateProfileInformation->has('cpf')) is-invalid @endif" name="cpf" value="{{ old('cpf', optional($user->patient)->cpf) }}">
                                    @if ($errors->updateProfileInformation->has('cpf'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('cpf') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="rg">RG</label>
                                    <input id="rg" type="text" class="form-control @if ($errors->updateProfileInformation->has('rg')) is-invalid @endif" name="rg" value="{{ old('rg', optional($user->patient)->rg) }}">
                                    @if ($errors->updateProfileInformation->has('rg'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('rg') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="phone">Telefone</label>
                                    <input id="phone" type="text" class="form-control @if ($errors->updateProfileInformation->has('phone')) is-invalid @endif" name="phone" value="{{ old('phone', optional($user->patient)->phone) }}">
                                    @if ($errors->updateProfileInformation->has('phone'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Endereço</h5>
                                <div class="mb-3">
                                    <label for="street">Rua</label>
                                    <input id="street" type="text" class="form-control @if ($errors->updateProfileInformation->has('street')) is-invalid @endif" name="street" value="{{ old('street', optional(optional($user->patient)->address)->street) }}">
                                    @if ($errors->updateProfileInformation->has('street'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('street') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="number">Número</label>
                                    <input id="number" type="text" class="form-control @if ($errors->updateProfileInformation->has('number')) is-invalid @endif" name="number" value="{{ old('number', optional(optional($user->patient)->address)->number) }}">
                                    @if ($errors->updateProfileInformation->has('number'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('number') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="complement">Complemento</label>
                                    <input id="complement" type="text" class="form-control @if ($errors->updateProfileInformation->has('complement')) is-invalid @endif" name="complement" value="{{ old('complement', optional(optional($user->patient)->address)->complement) }}">
                                    @if ($errors->updateProfileInformation->has('complement'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('complement') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="neighborhood">Bairro</label>
                                    <input id="neighborhood" type="text" class="form-control @if ($errors->updateProfileInformation->has('neighborhood')) is-invalid @endif" name="neighborhood" value="{{ old('neighborhood', optional(optional($user->patient)->address)->neighborhood) }}">
                                    @if ($errors->updateProfileInformation->has('neighborhood'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('neighborhood') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="city">Cidade</label>
                                    <input id="city" type="text" class="form-control @if ($errors->updateProfileInformation->has('city')) is-invalid @endif" name="city" value="{{ old('city', optional(optional($user->patient)->address)->city) }}">
                                    @if ($errors->updateProfileInformation->has('city'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('city') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="state">Estado</label>
                                    <input id="state" type="text" class="form-control @if ($errors->updateProfileInformation->has('state')) is-invalid @endif" name="state" value="{{ old('state', optional(optional($user->patient)->address)->state) }}">
                                    @if ($errors->updateProfileInformation->has('state'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('state') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="country">País</label>
                                    <input id="country" type="text" class="form-control @if ($errors->updateProfileInformation->has('country')) is-invalid @endif" name="country" value="{{ old('country', optional(optional($user->patient)->address)->country) }}">
                                    @if ($errors->updateProfileInformation->has('country'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('country') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="zip_code">CEP</label>
                                    <input id="zip_code" type="text" class="form-control @if ($errors->updateProfileInformation->has('zip_code')) is-invalid @endif" name="zip_code" value="{{ old('zip_code', optional(optional($user->patient)->address)->zip_code) }}">
                                    @if ($errors->updateProfileInformation->has('zip_code'))
                                        <div class="invalid-feedback">{{ $errors->updateProfileInformation->first('zip_code') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="d-grid gap-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-lg">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
