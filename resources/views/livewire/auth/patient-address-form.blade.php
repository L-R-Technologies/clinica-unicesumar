<div>
    <input type="hidden" name="zip_code" x-data x-bind:value="$wire.zip_code">
    <input type="hidden" name="street" x-data x-bind:value="$wire.street">
    <input type="hidden" name="number" x-data x-bind:value="$wire.number">
    <input type="hidden" name="complement" x-data x-bind:value="$wire.complement">
    <input type="hidden" name="neighborhood" x-data x-bind:value="$wire.neighborhood">
    <input type="hidden" name="city" x-data x-bind:value="$wire.city">
    <input type="hidden" name="state" x-data x-bind:value="$wire.state">
    <input type="hidden" name="country" x-data x-bind:value="$wire.country">

    <div class="mb-3">
        <label for="zip_code" class="form-label">CEP</label>
        <input id="zip_code" type="text" maxlength="9"
            class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('zip_code')) is-invalid @endif" wire:model.defer="zip_code"
            placeholder="00000-000" value="{{ $zip_code }}" x-mask="99999-999" wire:blur="fetchAddress">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('zip_code'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('zip_code') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="street" class="form-label">Rua</label>
        <input id="street" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('street')) is-invalid @endif"
            wire:model.defer="street">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('street'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('street') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="number" class="form-label">Número</label>
        <input id="number" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('number')) is-invalid @endif"
            wire:model.defer="number">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('number'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('number') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="complement" class="form-label">Complemento</label>
        <input id="complement" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('complement')) is-invalid @endif"
            wire:model.defer="complement">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('complement'))
            <div class="invalid-feedback">
                {{ session('errors')->getBag('updateProfileInformation')->first('complement') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="neighborhood" class="form-label">Bairro</label>
        <input id="neighborhood" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('neighborhood')) is-invalid @endif"
            wire:model.defer="neighborhood">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('neighborhood'))
            <div class="invalid-feedback">
                {{ session('errors')->getBag('updateProfileInformation')->first('neighborhood') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="city" class="form-label">Cidade</label>
        <input id="city" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('city')) is-invalid @endif"
            wire:model.defer="city">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('city'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('city') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="state" class="form-label">Estado</label>
        <input id="state" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('state')) is-invalid @endif"
            wire:model.defer="state">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('state'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('state') }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="country" class="form-label">País</label>
        <input id="country" type="text" class="form-control @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('country')) is-invalid @endif"
            wire:model.defer="country">
        @if (session('errors') && session('errors')->getBag('updateProfileInformation')->has('country'))
            <div class="invalid-feedback">{{ session('errors')->getBag('updateProfileInformation')->first('country') }}
            </div>
        @endif
    </div>
</div>
