import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PageProps } from '@/types';

interface ForgotPasswordProps extends Record<string, unknown> {
    status: string | null;
}

export default function ForgotPassword() {
    const { status } = usePage<PageProps<ForgotPasswordProps>>().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        post(route('password.email'));
    }

    return (
        <AuthLayout
            title="Recuperar senha"
            description="Enviaremos um link para redefinir sua senha."
        >
            {status && (
                <p className="mb-4 text-sm font-medium text-emerald-600">
                    {status}
                </p>
            )}

            <form onSubmit={submit} className="space-y-4">
                <div className="space-y-2">
                    <Label htmlFor="email">E-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        autoFocus
                        required
                    />
                    {errors.email && (
                        <p className="text-sm text-destructive">
                            {errors.email}
                        </p>
                    )}
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    Enviar link
                </Button>

                <p className="text-center text-sm text-muted-foreground">
                    <Link
                        href={route('login')}
                        className="text-primary hover:underline"
                    >
                        Voltar para o login
                    </Link>
                </p>
            </form>
        </AuthLayout>
    );
}
