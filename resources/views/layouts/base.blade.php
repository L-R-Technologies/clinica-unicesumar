<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base Laravel Blade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    .navbar-nav .nav-link {
        transition: color 0.2s, background 0.2s;
        border-radius: 0.375rem;
    }
    .navbar-nav .nav-link:hover, .navbar-nav .nav-link:focus {
        color: #0d6efd !important;
        background: #fff !important;
        text-decoration: none;
    }
    .navbar-nav .nav-link.active {
        color: #fff !important;
        background: #0d6efd !important;
    }
</style>
    @livewireStyles
</head>
<body>
    @if(isset($forceGuestNavbar) && $forceGuestNavbar)
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4" style="min-height: 80px;">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="/home">
                    <img src="{{ asset('imgs/unicesumar-logo.png') }}" alt="Unicesumar Logo" style="height:48px; width:auto; margin-right: 12px;">
                    <span class="fw-bold fs-4">Clínica Unicesumar</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light px-4 fw-bold" href="{{ route('login') }}">Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light text-primary px-4 fw-bold" href="{{ route('register') }}">Registrar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @else
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4" style="min-height: 80px;">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="/home">
                    <img src="{{ asset('imgs/unicesumar-logo.png') }}" alt="Unicesumar Logo" style="height:48px; width:auto; margin-right: 12px;">
                    <span class="fw-bold fs-4">Clínica Unicesumar</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-light px-4 fw-bold" href="{{ route('login') }}">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-light text-primary px-4 fw-bold" href="{{ route('register') }}">Registrar</a>
                            </li>
                        @else
                            @if(auth()->user()->hasRole('student') || auth()->user()->hasRole('teacher'))
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="/home">
                                        <i class="fa-solid fa-house fa-lg"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-users fa-lg"></i>
                                        <span>Pacientes</span>
                                    </a>
                                </li>
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-vial fa-lg"></i>
                                        <span>Amostras</span>
                                    </a>
                                </li>
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-notes-medical fa-lg"></i>
                                        <span>Anamneses</span>
                                    </a>
                                </li>
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-microscope fa-lg"></i>
                                        <span>Exames</span>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasRole('teacher'))
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-desktop fa-lg"></i>
                                        <span>Máquinas</span>
                                    </a>
                                </li>
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="{{ route('user-management.index') }}">
                                        <i class="fa-solid fa-users-gear fa-lg"></i>
                                        <span>Usuários</span>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasRole('patient'))
                                <li class="nav-item d-flex align-items-center">
                                    <a class="nav-link text-light fw-bold px-3 me-2 d-flex align-items-center gap-2 h-100" href="#">
                                        <i class="fa-solid fa-vials fa-lg"></i>
                                        <span>Meus Exames</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item dropdown d-flex align-items-center">
                                <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center px-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 48px;">
                                    <i class="fa fa-user-circle fa-2x"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item fw-bold text-primary d-flex align-items-center gap-2" href="{{ route('user.edit', ['user' => auth()->user()->id]) }}">
                                            Editar Perfil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fw-bold text-primary d-flex align-items-center gap-2" href="{{ route('user.password-edit', ['user' => auth()->user()->id]) }}">
                                            Editar Senha
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="dropdown-item fw-bold text-danger d-flex align-items-center gap-2" type="submit">
                                                Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    @endif
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    @livewireScripts
</body>
</html>
