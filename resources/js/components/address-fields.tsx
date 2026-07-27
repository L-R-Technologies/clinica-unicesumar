import type { ReactElement } from 'react';
import { FormField } from '@/components/form-field';
import { MaskedInput } from '@/components/masked-input';
import { Input } from '@/components/ui/input';

export interface AddressFieldValues {
    zip_code: string;
    street: string;
    number: string;
    complement: string;
    neighborhood: string;
    city: string;
    state: string;
    country: string;
}

export type AddressFieldName = keyof AddressFieldValues;

interface AddressFieldsProps {
    values: AddressFieldValues;
    errors: Partial<Record<AddressFieldName, string>>;
    onChange: (field: AddressFieldName, value: string) => void;
    /** Disparado ao sair do campo de CEP (usado para o preenchimento automático). */
    onCepBlur: () => void;
}

/**
 * Campos de endereço reutilizáveis (CEP, rua, número, complemento, bairro,
 * cidade, estado e país). O CEP usa máscara e aciona onCepBlur para permitir o
 * preenchimento automático via consulta de CEP no componente pai.
 */
export function AddressFields({
    values,
    errors,
    onChange,
    onCepBlur,
}: AddressFieldsProps): ReactElement {
    return (
        <div className="grid gap-4 sm:grid-cols-2">
            <FormField
                id="zip_code"
                label="CEP"
                error={errors.zip_code}
                required
            >
                <MaskedInput
                    id="zip_code"
                    mask="cep"
                    value={values.zip_code}
                    onValueChange={(value) => onChange('zip_code', value)}
                    onBlur={onCepBlur}
                    placeholder="00000-000"
                />
            </FormField>

            <FormField id="street" label="Rua" error={errors.street} required>
                <Input
                    id="street"
                    value={values.street}
                    onChange={(e) => onChange('street', e.target.value)}
                />
            </FormField>

            <FormField
                id="number"
                label="Número"
                error={errors.number}
                required
            >
                <Input
                    id="number"
                    value={values.number}
                    onChange={(e) => onChange('number', e.target.value)}
                />
            </FormField>

            <FormField
                id="complement"
                label="Complemento"
                error={errors.complement}
            >
                <Input
                    id="complement"
                    value={values.complement}
                    onChange={(e) => onChange('complement', e.target.value)}
                />
            </FormField>

            <FormField
                id="neighborhood"
                label="Bairro"
                error={errors.neighborhood}
                required
            >
                <Input
                    id="neighborhood"
                    value={values.neighborhood}
                    onChange={(e) => onChange('neighborhood', e.target.value)}
                />
            </FormField>

            <FormField id="city" label="Cidade" error={errors.city} required>
                <Input
                    id="city"
                    value={values.city}
                    onChange={(e) => onChange('city', e.target.value)}
                />
            </FormField>

            <FormField id="state" label="Estado" error={errors.state} required>
                <Input
                    id="state"
                    value={values.state}
                    onChange={(e) => onChange('state', e.target.value)}
                />
            </FormField>

            <FormField
                id="country"
                label="País"
                error={errors.country}
                required
            >
                <Input
                    id="country"
                    value={values.country}
                    onChange={(e) => onChange('country', e.target.value)}
                />
            </FormField>
        </div>
    );
}
