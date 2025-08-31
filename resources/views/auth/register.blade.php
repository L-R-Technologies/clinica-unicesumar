@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Register</h2>
    <a href="{{ route('welcome') }}">Voltar</a>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                   name="name" value="{{ old('name') }}" autofocus>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}">
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password">
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" class="form-control"
                   name="password_confirmation">
        </div>

        @yield('role_fields')

        <button type="submit" class="btn btn-primary" id="registerButton">
            Register
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const lgpdCheckbox = document.getElementById('lgpd_consent');
    const lgpdWarning = document.getElementById('lgpd_warning');
    const registerButton = document.getElementById('registerButton');
    
    // Função para verificar se é um formulário de paciente
    function isPatientForm() {
        const roleInput = document.querySelector('input[name="role"]');
        return roleInput && roleInput.value === 'patient';
    }
    
    // Função para controlar a exibição do aviso
    function toggleWarning() {
        if (isPatientForm() && lgpdCheckbox && lgpdWarning) {
            if (lgpdCheckbox.checked) {
                lgpdWarning.style.display = 'none';
            } else {
                lgpdWarning.style.display = 'block';
            }
        }
    }
    
    // Ocultar aviso no carregamento se checkbox estiver marcado
    if (isPatientForm() && lgpdCheckbox && lgpdCheckbox.checked) {
        toggleWarning();
    }
    
    // Controlar aviso quando checkbox mudar
    if (lgpdCheckbox) {
        lgpdCheckbox.addEventListener('change', toggleWarning);
    }
    
    // Validação no envio do formulário
    form.addEventListener('submit', function(e) {
        if (isPatientForm() && lgpdCheckbox && !lgpdCheckbox.checked) {
            e.preventDefault();
            lgpdWarning.style.display = 'block';
            lgpdCheckbox.focus();
            // Scroll suave até o checkbox
            lgpdCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection
