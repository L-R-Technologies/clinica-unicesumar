@extends('layouts.base')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold display-5">Dashboard</h1>
            <p class="lead">Bem-vindo{{ auth()->check() ? ', ' . auth()->user()->name : '' }}!</p>
        </div>
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-12 col-md-4">
                <div class="card text-center shadow h-100">
                    <div class="card-body">
                        <i class="fa fa-users fa-2x text-primary mb-2"></i>
                        <h5 class="card-title">Usuários Ativos</h5>
                        <p class="display-6 fw-bold mb-0">128</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-center shadow h-100">
                    <div class="card-body">
                        <i class="fa fa-user-md fa-2x text-success mb-2"></i>
                        <h5 class="card-title">Consultas Hoje</h5>
                        <p class="display-6 fw-bold mb-0">17</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-center shadow h-100">
                    <div class="card-body">
                        <i class="fa fa-heartbeat fa-2x text-danger mb-2"></i>
                        <h5 class="card-title">Pacientes Novos</h5>
                        <p class="display-6 fw-bold mb-0">5</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
