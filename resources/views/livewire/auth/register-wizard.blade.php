<div>
    <!-- Wizard Steps -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="step-indicator mx-1 {{ $step === 1 ? 'active' : '' }}"></div>
        <div class="step-line mx-1"></div>
        <div class="step-indicator mx-1 {{ $step === 2 ? 'active' : '' }}"></div>
        <div class="step-line mx-1"></div>
        <div class="step-indicator mx-1 {{ $step === 3 ? 'active' : '' }}"></div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="register">
        @if ($step === 1)
            <div class="register-step">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        wire:model.defer="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        wire:model.defer="email">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        wire:model.defer="password" placeholder="senha@123456">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmação da senha</label>
                    <input id="password_confirmation" type="password" class="form-control"
                        wire:model.defer="password_confirmation" placeholder="senha@123456">
                </div>
                <div class="d-grid">
                    <button type="button" class="btn btn-primary btn-lg" wire:click="nextStep">Avançar</button>
                </div>
                <div class="text-center mt-3">
                    Já tem uma conta? <a href="{{ route('login') }}">Entre aqui</a>
                </div>
            </div>
        @elseif($step === 2)
            <div class="register-step">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="birthday">Data de nascimento</label>
                        <input type="date" wire:model.defer="birthday" id="birthday"
                            class="form-control @error('birthday') is-invalid @enderror">
                        @error('birthday')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sex">Sexo</label>
                        <select wire:model.defer="sex" id="sex"
                            class="form-control @error('sex') is-invalid @enderror">
                            <option value="">Selecione</option>
                            <option value="male">Masculino</option>
                            <option value="female">Feminino</option>
                            <option value="other">Outro</option>
                        </select>
                        @error('sex')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf">CPF</label>
                        <input type="text" wire:model.defer="cpf" id="cpf"
                            class="form-control @error('cpf') is-invalid @enderror" x-mask="999.999.999-99">
                        @error('cpf')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="rg">RG</label>
                        <input type="text" wire:model.defer="rg" id="rg"
                            class="form-control @error('rg') is-invalid @enderror">
                        @error('rg')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ethnicity">Etnia</label>
                        <input type="text" wire:model.defer="ethnicity" id="ethnicity"
                            class="form-control @error('ethnicity') is-invalid @enderror">
                        @error('ethnicity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone">Telefone</label>
                        <input type="text" wire:model.defer="phone" id="phone"
                            class="form-control @error('phone') is-invalid @enderror" x-mask="(99) 99999-9999">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-primary" wire:click="prevStep">Voltar</button>
                    <button type="button" class="btn btn-primary" wire:click="nextStep">Avançar</button>
                </div>
            </div>
        @elseif($step === 3)
            <div class="register-step">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="zip_code">CEP</label>
                        <input type="text" wire:model.lazy="zip_code" id="zip_code" maxlength="9"
                            class="form-control @error('zip_code') is-invalid @enderror" placeholder="00000-000"
                            x-mask="99999-999">
                        @error('zip_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="street">Rua</label>
                        <input type="text" wire:model.defer="street" id="street"
                            class="form-control @error('street') is-invalid @enderror">
                        @error('street')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="number">Número</label>
                        <input type="text" wire:model.defer="number" id="number"
                            class="form-control @error('number') is-invalid @enderror">
                        @error('number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="neighborhood">Bairro</label>
                        <input type="text" wire:model.defer="neighborhood" id="neighborhood"
                            class="form-control @error('neighborhood') is-invalid @enderror">
                        @error('neighborhood')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="complement">Complemento</label>
                        <input type="text" wire:model.defer="complement" id="complement"
                            class="form-control @error('complement') is-invalid @enderror">
                        @error('complement')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city">Cidade</label>
                        <input type="text" wire:model.defer="city" id="city"
                            class="form-control @error('city') is-invalid @enderror">
                        @error('city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state">Estado</label>
                        <input type="text" wire:model.defer="state" id="state"
                            class="form-control @error('state') is-invalid @enderror">
                        @error('state')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country">País</label>
                        <input type="text" wire:model.defer="country" id="country"
                            class="form-control @error('country') is-invalid @enderror">
                        @error('country')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" wire:model.defer="lgpd_consent" id="lgpd_consent"
                            class="form-check-input @error('lgpd_consent') is-invalid @enderror" value="1">
                        <label for="lgpd_consent" class="form-check-label">
                            <strong>Consentimento LGPD (Lei Geral de Proteção de Dados)</strong><br>
                            <small class="text-muted">
                                Eu concordo com o tratamento dos meus dados pessoais conforme descrito na nossa
                                <a href="{{ route('privacy-policy') }}" target="_blank">Política de Privacidade</a>.
                                Entendo que meus dados serão utilizados exclusivamente para prestação de serviços de
                                saúde
                                e que posso revogar este consentimento a qualquer momento.
                            </small>
                        </label>
                        @error('lgpd_consent')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-primary" wire:click="prevStep">Voltar</button>
                    <button type="submit" class="btn btn-success" id="registerButton">Salvar</button>
                </div>
            </div>
        @endif
    </form>
</div>
