<div>
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

                @error('form')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @enderror

                <div class="card">
                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <!-- Seleção do Tipo de Usuário -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">Tipo de Usuário</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" wire:model.live="userType"
                                                       id="user_type_teacher" value="teacher">
                                                <label class="form-check-label" for="user_type_teacher">
                                                    <i class="fas fa-chalkboard-teacher"></i> Professor
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" wire:model.live="userType"
                                                       id="user_type_student" value="student">
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
                                           id="name" wire:model="name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" wire:model="email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <div class="input-group">
                                        <input type="{{ $showPassword ? 'text' : 'password' }}"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password" wire:model="password" required>
                                        <button type="button" class="btn btn-outline-secondary" wire:click="generatePassword">
                                            <i class="fas fa-random"></i> Gerar
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" wire:click="togglePassword">
                                            <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
                            @if($userType === 'teacher')
                                <div>
                                    <h5 class="mb-3">Dados do Professor</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="registration_number" class="form-label">Número de Registro</label>
                                            <input type="text" class="form-control @error('registrationNumber') is-invalid @enderror"
                                                   id="registration_number" wire:model="registrationNumber" required>
                                            @error('registrationNumber')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="crbm" class="form-label">CRBM (Opcional)</label>
                                            <input type="text" class="form-control @error('crbm') is-invalid @enderror"
                                                   id="crbm" wire:model="crbm">
                                            @error('crbm')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Dados Específicos do Estudante -->
                            @if($userType === 'student')
                                <div>
                                    <h5 class="mb-3">Dados do Estudante</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="ra" class="form-label">RA (Registro Acadêmico)</label>
                                            <input type="text" class="form-control @error('ra') is-invalid @enderror"
                                                   id="ra" wire:model="ra" required>
                                            @error('ra')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="course" class="form-label">Curso</label>
                                            <input type="text" class="form-control @error('course') is-invalid @enderror"
                                                   id="course" wire:model="course" required>
                                            @error('course')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('user-management.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <i class="fas fa-save"></i> Criar Usuário
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
