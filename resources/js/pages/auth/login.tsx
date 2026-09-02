import { Link, useForm, usePage } from '@inertiajs/react';
import { Eye, EyeOff } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PageProps } from '@/types';

interface LoginProps extends Record<string, unknown> {
    status: string | null;
}

export default function Login() {
    const { status } = usePage<PageProps<LoginProps>>().props;
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
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
                    <div className="flex gap-2">
                        <Input
                            id="password"
                            type={showPassword ? 'text' : 'password'}
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(e) =>
                                setData('password', e.target.value)
                            }
                            required
                        />
                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            aria-label={
                                showPassword ? 'Ocultar senha' : 'Mostrar senha'
                            }
                            onClick={() =>
                                setShowPassword((current) => !current)
                            }
                        >
                            {showPassword ? (
                                <EyeOff className="size-4" />
                            ) : (
                                <Eye className="size-4" />
                            )}
                        </Button>
                    </div>
                    {errors.password && (
                        <p className="text-sm text-destructive">
                            {errors.password}
                        </p>
                    )}
                </div>

                <div className="flex justify-end">
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
