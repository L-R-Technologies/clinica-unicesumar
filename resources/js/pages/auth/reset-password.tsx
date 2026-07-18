import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PageProps } from '@/types';

interface ResetPasswordProps extends Record<string, unknown> {
    email: string;
    token: string;
}

export default function ResetPassword() {
    const { email, token } = usePage<PageProps<ResetPasswordProps>>().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        post(route('password.update'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    return (
        <AuthLayout
            title="Redefinir senha"
            description="Escolha uma nova senha para sua conta."
        >
            <form onSubmit={submit} className="space-y-4">
                <div className="space-y-2">
                    <Label htmlFor="email">E-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    {errors.email && (
                        <p className="text-sm text-destructive">
                            {errors.email}
                        </p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="password">Nova senha</Label>
                    <Input
                        id="password"
                        type="password"
                        autoComplete="new-password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                    {errors.password && (
                        <p className="text-sm text-destructive">
                            {errors.password}
                        </p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="password_confirmation">
                        Confirmar nova senha
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(e) =>
                            setData('password_confirmation', e.target.value)
                        }
                        required
                    />
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    Redefinir senha
                </Button>
            </form>
        </AuthLayout>
    );
}
