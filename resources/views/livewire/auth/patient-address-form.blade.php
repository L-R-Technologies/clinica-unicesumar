<div>
    <div class="mb-3">
        <label for="zip_code" class="form-label">CEP</label>
        <input id="zip_code" type="text" maxlength="9" class="form-control @error('zip_code') is-invalid @enderror"
               wire:model.defer="zip_code"
               placeholder="00000-000"
               value="{{ $zip_code }}">
        @error('zip_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="street" class="form-label">Rua</label>
        <input id="street" type="text" class="form-control @error('street') is-invalid @enderror" wire:model.defer="street">
        @error('street')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="number" class="form-label">Número</label>
        <input id="number" type="text" class="form-control @error('number') is-invalid @enderror" wire:model.defer="number">
        @error('number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="complement" class="form-label">Complemento</label>
        <input id="complement" type="text" class="form-control @error('complement') is-invalid @enderror" wire:model.defer="complement">
        @error('complement')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="neighborhood" class="form-label">Bairro</label>
        <input id="neighborhood" type="text" class="form-control @error('neighborhood') is-invalid @enderror" wire:model.defer="neighborhood">
        @error('neighborhood')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="city" class="form-label">Cidade</label>
        <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" wire:model.defer="city">
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="state" class="form-label">Estado</label>
        <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" wire:model.defer="state">
        @error('state')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="country" class="form-label">País</label>
        <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" wire:model.defer="country">
        @error('country')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const zipCodeInput = document.getElementById('zip_code');
    let lastZipCode = '';

    if (zipCodeInput) {
        // Aplicar máscara inicial se houver valor
        if (zipCodeInput.value) {
            zipCodeInput.value = formatCep(zipCodeInput.value);
            lastZipCode = zipCodeInput.value;
        }

        // Aplicar máscara durante a digitação
        zipCodeInput.addEventListener('input', function(e) {
            e.target.value = formatCep(e.target.value);
        });

        // Buscar endereço quando sair do campo
        zipCodeInput.addEventListener('blur', function(e) {
            const currentValue = e.target.value;
            const cleanCep = currentValue.replace(/\D/g, '');

            // Só buscar se mudou e tem 8 dígitos
            if (currentValue !== lastZipCode && cleanCep.length === 8) {
                lastZipCode = currentValue;

                // Chamar o método Livewire
                Livewire.find(zipCodeInput.closest('[wire\\:id]').getAttribute('wire:id')).call('fetchAddress', currentValue);
            }
        });
    }

    function formatCep(value) {
        // Remove tudo que não é número
        let numbers = value.replace(/\D/g, '');

        // Limita a 8 dígitos
        if (numbers.length > 8) {
            numbers = numbers.substring(0, 8);
        }

        // Aplica a máscara
        if (numbers.length > 5) {
            return numbers.substring(0, 5) + '-' + numbers.substring(5);
        }

        return numbers;
    }
});
</script>
