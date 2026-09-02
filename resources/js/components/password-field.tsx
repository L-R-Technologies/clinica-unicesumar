import { Eye, EyeOff, RefreshCw } from 'lucide-react';
import { useState, type ReactElement } from 'react';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const PASSWORD_ALPHABET =
    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
const GENERATED_PASSWORD_LENGTH = 12;
const DEFAULT_HINT = 'Mínimo de 8 caracteres.';

export function generateTemporaryPassword(): string {
    const randomValues = new Uint32Array(GENERATED_PASSWORD_LENGTH);
    crypto.getRandomValues(randomValues);

    let password = '';
    for (let i = 0; i < GENERATED_PASSWORD_LENGTH; i++) {
        password +=
            PASSWORD_ALPHABET[randomValues[i] % PASSWORD_ALPHABET.length];
    }

    return password;
}

interface PasswordFieldProps {
    id?: string;
    label?: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    required?: boolean;
    hint?: string;
}

/**
 * Campo de senha com botões de exibir/ocultar e de gerar senha temporária,
 * usado nos cadastros feitos pela equipe (usuários internos e pacientes).
 */
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
