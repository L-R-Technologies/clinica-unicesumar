@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('user-management.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <h2 class="mb-0">Editar Usuário</h2>
                    <div></div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <form method="POST" action="{{ route('user-management.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Tipo de Usuário (somente leitura) -->
                            <div class="mb-4">
                                <label class="form-label">Tipo de Usuário</label>
                                <div class="alert alert-info">
                                    @if ($user->role === 'teacher')
                                        <i class="fas fa-chalkboard-teacher"></i> Professor
                                    @elseif($user->role === 'student')
                                        <i class="fas fa-user-graduate"></i> Estudante
                                    @else
                                        <i class="fas fa-user"></i> {{ ucfirst($user->role) }}
                                    @endif
                                </div>
                            </div>

                            <!-- Dados Básicos -->
                            <h5 class="mb-3">Dados Básicos</h5>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha (Opcional)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
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
                                <div class="form-text">Deixe em branco para manter a senha atual. Mínimo de 8 caracteres
                                    para alterar.</div>
                            </div>

                            @if ($user->role === 'teacher')
                                <!-- Dados Específicos do Professor -->
                                <div class="mt-4">
                                    <h5 class="mb-3">Dados do Professor</h5>
                                    <div class="mb-3">
                                        <label for="registration_number" class="form-label">Número de Registro</label>
                                        <input type="text"
                                            class="form-control @error('registration_number') is-invalid @enderror"
                                            id="registration_number" name="registration_number"
                                            value="{{ old('registration_number', $user->teacher->registration_number ?? '') }}"
                                            required>
                                        @error('registration_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="crbm" class="form-label">CRBM (Opcional)</label>
                                        <input type="text" class="form-control @error('crbm') is-invalid @enderror"
                                            id="crbm" name="crbm"
                                            value="{{ old('crbm', $user->teacher->crbm ?? '') }}">
                                        @error('crbm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @elseif($user->role === 'student')
                                <!-- Dados Específicos do Estudante -->
                                <div class="mt-4">
                                    <h5 class="mb-3">Dados do Estudante</h5>
                                    <div class="mb-3">
                                        <label for="ra" class="form-label">RA (Registro Acadêmico)</label>
                                        <input type="text" class="form-control @error('ra') is-invalid @enderror"
                                            id="ra" name="ra" value="{{ old('ra', $user->student->ra ?? '') }}"
                                            required>
                                        @error('ra')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Curso</label>
                                        <input type="text" class="form-control @error('course') is-invalid @enderror"
                                            id="course" name="course"
                                            value="{{ old('course', $user->student->course ?? '') }}" required>
                                        @error('course')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Última atualização:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" class="btn btn-success">
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                const response = await fetch('{{ route('user-management.generate-password') }}');
                const data = await response.json();
                document.getElementById('password').value = data.password;
            } catch (error) {
                console.error('Erro ao gerar senha:', error);
            }
        }
    </script>
@endsection
