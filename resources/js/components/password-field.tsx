import { Eye, EyeOff, RefreshCw } from 'lucide-react';
import { useState, type ReactElement } from 'react';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { generateTemporaryPassword } from '@/lib/password';

const DEFAULT_HINT =
    'Mínimo de 8 caracteres, com letras maiúsculas, minúsculas e números.';

interface PasswordFieldProps {
    id?: string;
    label?: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    required?: boolean;
    hint?: string;
}

export function PasswordField({
    id = 'password',
    label = 'Senha',
    value,
    onChange,
    error,
    required,
    hint = DEFAULT_HINT,
}: PasswordFieldProps): ReactElement {
    const [showPassword, setShowPassword] = useState(false);

    return (
        <FormField id={id} label={label} error={error} required={required}>
            <div className="flex gap-2">
                <Input
                    id={id}
                    type={showPassword ? 'text' : 'password'}
                    minLength={8}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    autoComplete="new-password"
                />
                <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    aria-label={
                        showPassword ? 'Ocultar senha' : 'Mostrar senha'
                    }
                    onClick={() => setShowPassword((current) => !current)}
                >
                    {showPassword ? (
                        <EyeOff className="size-4" />
                    ) : (
                        <Eye className="size-4" />
                    )}
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => onChange(generateTemporaryPassword())}
                >
                    <RefreshCw className="size-4" />
                    Gerar
                </Button>
            </div>
            {hint && <p className="text-sm text-muted-foreground">{hint}</p>}
        </FormField>
    );
}
