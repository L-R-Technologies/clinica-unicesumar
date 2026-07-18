import { Link, useForm, usePage } from '@inertiajs/react';
import { Eye, EyeOff, RefreshCw } from 'lucide-react';
import { type FormEvent, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { FormField } from '@/components/form-field';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { PageProps, Student, Teacher, User } from '@/types';

const PASSWORD_ALPHABET =
    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
const GENERATED_PASSWORD_LENGTH = 12;

const ROLE_LABELS: Record<string, string> = {
    teacher: 'Professor',
    student: 'Estudante',
    patient: 'Paciente',
};

/** Gera uma senha temporária no cliente usando uma fonte de aleatoriedade segura. */
function generateTemporaryPassword(): string {
    const randomValues = new Uint32Array(GENERATED_PASSWORD_LENGTH);
    crypto.getRandomValues(randomValues);

    let password = '';
    for (let i = 0; i < GENERATED_PASSWORD_LENGTH; i++) {
        password += PASSWORD_ALPHABET[randomValues[i] % PASSWORD_ALPHABET.length];
    }

    return password;
}

interface UserEditProps extends Record<string, unknown> {
    user: User & { teacher?: Teacher | null; student?: Student | null };
}

export default function UserEdit() {
    const { user } = usePage<PageProps<UserEditProps>>().props;
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        password: '',
        registration_number: user.teacher?.registration_number ?? '',
        crbm: '',
        ra: user.student?.ra ?? '',
        course: user.student?.course ?? '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        put(route('user-management.update', user.id));
    }

    return (
        <AppLayout title="Editar Usuário">
            <Card className="max-w-2xl">
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-2">
                            <p className="text-sm text-muted-foreground">
                                Tipo de usuário
                            </p>
                            <Badge variant="secondary">
                                {ROLE_LABELS[user.role] ?? user.role}
                            </Badge>
                        </div>

                        <FormField
                            id="name"
                            label="Nome completo"
                            error={errors.name}
                            required
                        >
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                autoFocus
                            />
                        </FormField>

                        <FormField
                            id="email"
                            label="Email"
                            error={errors.email}
                            required
                        >
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />
                        </FormField>

                        <FormField
                            id="password"
                            label="Nova senha (opcional)"
                            error={errors.password}
                        >
                            <div className="flex gap-2">
                                <Input
                                    id="password"
                                    type={showPassword ? 'text' : 'password'}
                                    value={data.password}
                                    onChange={(e) =>
                                        setData('password', e.target.value)
                                    }
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    aria-label={
                                        showPassword
                                            ? 'Ocultar senha'
                                            : 'Mostrar senha'
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
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() =>
                                        setData(
                                            'password',
                                            generateTemporaryPassword(),
                                        )
                                    }
                                >
                                    <RefreshCw className="size-4" />
                                    Gerar
                                </Button>
                            </div>
                            <p className="text-sm text-muted-foreground">
                                Deixe em branco para manter a senha atual.
                                Mínimo de 8 caracteres para alterar.
                            </p>
                        </FormField>

                        {user.role === 'teacher' && (
                            <>
                                <FormField
                                    id="registration_number"
                                    label="Número de registro"
                                    error={errors.registration_number}
                                    required
                                >
                                    <Input
                                        id="registration_number"
                                        value={data.registration_number}
                                        onChange={(e) =>
                                            setData(
                                                'registration_number',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </FormField>

                                <FormField
                                    id="crbm"
                                    label="CRBM (opcional)"
                                    error={errors.crbm}
                                >
                                    <Input
                                        id="crbm"
                                        value={data.crbm}
                                        onChange={(e) =>
                                            setData('crbm', e.target.value)
                                        }
                                    />
                                </FormField>
                            </>
                        )}

                        {user.role === 'student' && (
                            <>
                                <FormField
                                    id="ra"
                                    label="RA (registro acadêmico)"
                                    error={errors.ra}
                                    required
                                >
                                    <Input
                                        id="ra"
                                        value={data.ra}
                                        onChange={(e) =>
                                            setData('ra', e.target.value)
                                        }
                                    />
                                </FormField>

                                <FormField
                                    id="course"
                                    label="Curso"
                                    error={errors.course}
                                    required
                                >
                                    <Input
                                        id="course"
                                        value={data.course}
                                        onChange={(e) =>
                                            setData('course', e.target.value)
                                        }
                                    />
                                </FormField>
                            </>
                        )}

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('user-management.index')}>
                                    Cancelar
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Salvar alterações
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
