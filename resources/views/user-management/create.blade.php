@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('user-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <h2 class="mb-0">Criar Novo Usuário</h2>
                <div></div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('user-management.store') }}">
                        @csrf

                        <!-- Seleção do Tipo de Usuário -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Tipo de Usuário</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="user_type"
                                                   id="user_type_teacher" value="teacher"
                                                   {{ old('user_type', 'teacher') === 'teacher' ? 'checked' : '' }}
                                                   onchange="toggleUserTypeFields()">
                                            <label class="form-check-label" for="user_type_teacher">
                                                <i class="fas fa-chalkboard-teacher"></i> Professor
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="user_type"
                                                   id="user_type_student" value="student"
                                                   {{ old('user_type') === 'student' ? 'checked' : '' }}
                                                   onchange="toggleUserTypeFields()">
                                            <label class="form-check-label" for="user_type_student">
                                                <i class="fas fa-user-graduate"></i> Estudante
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Dados Básicos -->
                        <h5 class="mb-3">Dados Básicos</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                                        <i class="fas fa-random"></i> Gerar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Mínimo de 8 caracteres</div>
                            </div>
                        </div>

                        <hr>

                        <!-- Dados Específicos do Professor -->
                        <div id="teacher_fields" style="display: none;">
                            <h5 class="mb-3">Dados do Professor</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registration_number" class="form-label">Número de Registro</label>
                                    <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                           id="registration_number" name="registration_number" value="{{ old('registration_number') }}">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="crbm" class="form-label">CRBM (Opcional)</label>
                                    <input type="text" class="form-control @error('crbm') is-invalid @enderror"
                                           id="crbm" name="crbm" value="{{ old('crbm') }}">
                                    @error('crbm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dados Específicos do Estudante -->
                        <div id="student_fields" style="display: none;">
                            <h5 class="mb-3">Dados do Estudante</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ra" class="form-label">RA (Registro Acadêmico)</label>
                                    <input type="text" class="form-control @error('ra') is-invalid @enderror"
                                           id="ra" name="ra" value="{{ old('ra') }}">
                                    @error('ra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Curso</label>
                                    <input type="text" class="form-control @error('course') is-invalid @enderror"
                                           id="course" name="course" value="{{ old('course') }}">
                                    @error('course')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user-management.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Criar Usuário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUserTypeFields() {
    const teacherFields = document.getElementById('teacher_fields');
    const studentFields = document.getElementById('student_fields');
    const userType = document.querySelector('input[name="user_type"]:checked').value;

    if (userType === 'teacher') {
        teacherFields.style.display = 'block';
        studentFields.style.display = 'none';

        // Tornar campos obrigatórios
        document.getElementById('registration_number').required = true;
        document.getElementById('ra').required = false;
        document.getElementById('course').required = false;
    } else if (userType === 'student') {
        teacherFields.style.display = 'none';
        studentFields.style.display = 'block';

        // Tornar campos obrigatórios
        document.getElementById('registration_number').required = false;
        document.getElementById('ra').required = true;
        document.getElementById('course').required = true;
    }
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

async function generatePassword() {
    try {
        const response = await fetch('{{ route("user-management.generate-password") }}');
        const data = await response.json();
        document.getElementById('password').value = data.password;
    } catch (error) {
        console.error('Erro ao gerar senha:', error);
    }
}

// Inicializar campos ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    toggleUserTypeFields();
});
</script>
@endsection
