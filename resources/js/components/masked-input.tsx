import { forwardRef } from 'react';
import { Input } from '@/components/ui/input';
import { formatCep, formatCpf, formatPhone } from '@/lib/masks';

type MaskType = 'cpf' | 'phone' | 'cep';

const FORMATTERS: Record<MaskType, (value: string) => string> = {
    cpf: formatCpf,
    phone: formatPhone,
    cep: formatCep,
};

interface MaskedInputProps extends Omit<
    React.ComponentProps<typeof Input>,
    'onChange' | 'value'
> {
    mask: MaskType;
    value: string;
    onValueChange: (value: string) => void;
}

export const MaskedInput = forwardRef<HTMLInputElement, MaskedInputProps>(
    ({ mask, value, onValueChange, ...props }, ref) => {
        return (
            <Input
                ref={ref}
                inputMode="numeric"
                value={value}
                onChange={(e) =>
                    onValueChange(FORMATTERS[mask](e.target.value))
                }
                {...props}
            />
        );
    },
);

MaskedInput.displayName = 'MaskedInput';
