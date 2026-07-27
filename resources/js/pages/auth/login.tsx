import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PageProps } from '@/types';

interface LoginProps extends Record<string, unknown> {
    status: string | null;
}

export default function Login() {
    const { status } = usePage<PageProps<LoginProps>>().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    }

    return (
        <AuthLayout
            title="Entrar"
            description="Acesse sua conta da Clínica Unicesumar."
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
                        autoComplete="username"
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

                <div className="space-y-2">
                    <Label htmlFor="password">Senha</Label>
                    <Input
                        id="password"
                        type="password"
                        autoComplete="current-password"
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

                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2 text-sm">
                        <Checkbox
                            checked={data.remember}
                            onCheckedChange={(checked) =>
                                setData('remember', checked === true)
                            }
                        />
                        Lembrar-me
                    </label>
                    <Link
                        href={route('password.request')}
                        className="text-sm text-primary hover:underline"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    Entrar
                </Button>

                <p className="text-center text-sm text-muted-foreground">
                    Não tem conta?{' '}
                    <Link
                        href={route('register')}
                        className="text-primary hover:underline"
                    >
                        Cadastre-se
                    </Link>
                </p>
            </form>
        </AuthLayout>
    );
}
