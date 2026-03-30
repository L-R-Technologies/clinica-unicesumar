<div>
    {{-- Botão de Exclusão de Dados --}}
    <div class="alert alert-danger" role="alert">
        <h5 class="alert-heading">
            <i class="fa fa-exclamation-triangle"></i> Exclusão de Dados Pessoais
        </h5>
        <p>
            Ao solicitar a exclusão dos seus dados, todas as suas informações pessoais serão
            <strong>permanentemente apagadas</strong> do sistema. Esta ação é <strong>irreversível</strong>.
        </p>
        <hr>
        <p class="mb-2"><strong>O que será apagado:</strong></p>
        <ul class="mb-3">
            <li>Seu nome, e-mail e dados de contato</li>
            <li>Documentos pessoais (CPF, RG)</li>
            <li>Endereço e telefone</li>
        </ul>
        <p class="mb-3">
            <i class="fa fa-ban"></i> <strong>Você não poderá mais acessar sua conta</strong>
        </p>
        <button type="button" class="btn btn-danger" wire:click="openFirstConfirmation">
            <i class="fa fa-user-times"></i> Apagar Meus Dados
        </button>
    </div>

    {{-- Primeira Confirmação Modal --}}
    @if($showFirstConfirmation)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-exclamation-triangle"></i> Atenção: Ação Irreversível
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFirstConfirmation"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">Você está prestes a apagar todos os seus dados pessoais.</p>
                    <p>Esta ação é <strong class="text-danger">IRREVERSÍVEL</strong> e resultará em:</p>
                    <ul>
                        <li>Perda permanente de acesso à sua conta</li>
                        <li>Todos os seus dados pessoais serão apagados</li>
                        <li>Não será possível recuperar os dados apagados</li>
                        <li>Encerramento definitivo do seu cadastro</li>
                    </ul>
                    <p class="text-muted small mt-3">
                        <i class="fa fa-info-circle"></i> Seus registros clínicos serão mantidos sem identificação para fins legais.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFirstConfirmation">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="proceedToSecondConfirmation">
                        <i class="fa fa-arrow-right"></i> Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Segunda Confirmação Modal --}}
    @if($showSecondConfirmation)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-exclamation-circle"></i> Confirmação Final
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeSecondConfirmation"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>ÚLTIMA CHANCE!</strong> Esta ação não pode ser desfeita.
                    </div>

                    @if(session('error'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p class="fw-bold">Para confirmar a exclusão, digite a palavra <span class="text-danger">CONFIRMAR</span> no campo abaixo:</p>

                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control form-control-lg text-center"
                            wire:model.live="confirmationText"
                            placeholder="Digite CONFIRMAR"
                            autocomplete="off"
                        >
                    </div>

                    <p class="text-muted small">
                        <i class="fa fa-shield-alt"></i> Após a confirmação, você será desconectado e seus dados serão apagados imediatamente.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeSecondConfirmation">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-danger"
                        wire:click="anonymizeData"
                        {{ $confirmationText !== 'CONFIRMAR' ? 'disabled' : '' }}
                    >
                        <i class="fa fa-check"></i> Confirmar Exclusão
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
