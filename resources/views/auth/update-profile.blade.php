@extends('layouts.base')

@section('content')
    <div class="container position-relative" style="min-height: 80vh;">
        <a href="{{ route('home') }}"
            class="btn btn-primary position-absolute top-0 start-0 mt-3 ms-2 d-flex align-items-center gap-1 shadow"
            style="z-index:10;">
            <i class="fa fa-arrow-left"></i> <span>Voltar</span>
        </a>
        <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="card shadow-lg p-4 mb-5" style="max-width: 900px; width: 100%;">
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
                @if ($errors->updateProfileInformation->any())
                    <div class="alert alert-danger" role="alert">
                        Corrija os campos destacados. Lembre-se de conferir todas as abas do formulário.
                    </div>
                @endif
                <form method="POST" action="{{ route('user-profile-information.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Campos específicos por role --}}
                    @if (auth()->user()->hasRole('student'))
                        @include('auth.partials.student-fields')
                    @endif

                    @if (auth()->user()->hasRole('teacher'))
                        @include('auth.partials.teacher-fields')
                    @endif

                    @if (auth()->user()->hasRole('patient'))
                        <ul class="nav nav-tabs mb-3 w-100" id="patientTab" role="tablist">
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link active w-100" id="personal-tab" data-bs-toggle="tab"
                                    data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                                    aria-selected="true">Informações Pessoais</button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="address-tab" data-bs-toggle="tab"
                                    data-bs-target="#address" type="button" role="tab" aria-controls="address"
                                    aria-selected="false">Endereço</button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="revoke-access-tab" data-bs-toggle="tab"
                                    data-bs-target="#revoke-access" type="button" role="tab"
                                    aria-controls="revoke-access" aria-selected="false">Revogar Acesso</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="patientTabContent">
                            <div class="tab-pane fade show active" id="personal" role="tabpanel"
                                aria-labelledby="personal-tab">
                                @include('auth.partials.patient-personal-fields')
                                <div class="d-grid gap-2 mb-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg w-50 mx-auto">Salvar</button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                                @livewire('auth.patient-address-form', ['address' => optional($user->patient)->address])
                                <div class="d-grid gap-2 mb-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg w-50 mx-auto">Salvar</button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="revoke-access" role="tabpanel"
                                aria-labelledby="revoke-access-tab">
                                @livewire('auth.anonymize-user-data')
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
