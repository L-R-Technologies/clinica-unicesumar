import { useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { OtpInput } from '@/components/otp-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function TwoFactorChallenge() {
    const [isUsingRecoveryCode, setIsUsingRecoveryCode] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        code: '',
        recovery_code: '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('two-factor.login.store'));
    }

    function toggleRecoveryCode(): void {
        reset();
        setIsUsingRecoveryCode((previous) => !previous);
    }

    return (
        <AuthLayout
            title="Verificação em duas etapas"
            description={
                isUsingRecoveryCode
                    ? 'Informe um dos seus códigos de recuperação.'
                    : 'Informe o código gerado pelo seu aplicativo autenticador.'
            }
        >
            <form onSubmit={submit} className="space-y-4">
                {isUsingRecoveryCode ? (
                    <div className="space-y-2">
                        <Label htmlFor="recovery_code">
                            Código de recuperação
                        </Label>
                        <Input
                            id="recovery_code"
                            type="text"
                            autoComplete="one-time-code"
                            value={data.recovery_code}
                            onChange={(e) =>
                                setData('recovery_code', e.target.value)
                            }
                            autoFocus
                            required
                        />
                        {errors.recovery_code && (
                            <p className="text-sm text-destructive">
                                {errors.recovery_code}
                            </p>
                        )}
                    </div>
                ) : (
                    <div className="space-y-2">
                        <Label htmlFor="code" className="justify-center">
                            Código de autenticação
                        </Label>
                        <OtpInput
                            id="code"
                            value={data.code}
                            onChange={(code) => setData('code', code)}
                            autoFocus
                        />
                        {errors.code && (
                            <p className="text-center text-sm text-destructive">
                                {errors.code}
                            </p>
                        )}
                    </div>
                )}

                <Button type="submit" className="w-full" disabled={processing}>
                    Verificar
                </Button>

                <p className="text-center text-sm text-muted-foreground">
                    <button
                        type="button"
                        onClick={toggleRecoveryCode}
                        className="text-primary hover:underline"
                    >
                        {isUsingRecoveryCode
                            ? 'Usar código do aplicativo autenticador'
                            : 'Usar um código de recuperação'}
                    </button>
                </p>
            </form>
        </AuthLayout>
    );
}
