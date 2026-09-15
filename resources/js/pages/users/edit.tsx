import { useForm, usePage } from '@inertiajs/react';
import { Eye, EyeOff, RefreshCw } from 'lucide-react';
import { type FormEvent, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { generateTemporaryPassword } from '@/lib/password';
import type { PageProps, Student, Teacher, User } from '@/types';

const ROLE_LABELS: Record<string, string> = {
    teacher: 'Professor',
    student: 'Estudante',
    patient: 'Paciente',
};

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
        professional_license: user.teacher?.professional_license ?? '',
        ra: user.student?.ra ?? '',
        course: user.student?.course ?? '',
        semester: user.student?.semester ?? '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        put(route('user-management.update', user.id));
    }

    return (
        <AppLayout
            title="Editar Usuário"
            actions={
                <>
                    <BackButton
                        href={route('user-management.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        Salvar alterações
                    </Button>
                </>
            }
        >
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form
                        id="resource-form"
                        onSubmit={submit}
                        className="space-y-4"
                    >
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
                                maxLength={255}
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
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
                                maxLength={255}
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
                                    minLength={8}
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
                                Deixe em branco para manter a senha atual. Para
                                alterar: mínimo de 8 caracteres, com letras
                                maiúsculas, minúsculas e números.
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
                                        maxLength={10}
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
                                    id="professional_license"
                                    label="CRBM (opcional)"
                                    error={errors.professional_license}
                                >
                                    <Input
                                        id="professional_license"
                                        maxLength={10}
                                        value={data.professional_license}
                                        onChange={(e) =>
                                            setData(
                                                'professional_license',
                                                e.target.value,
                                            )
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
                                        maxLength={9}
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
                                        maxLength={255}
                                        value={data.course}
                                        onChange={(e) =>
                                            setData('course', e.target.value)
                                        }
                                    />
                                </FormField>

                                <FormField
                                    id="semester"
                                    label="Semestre"
                                    error={errors.semester}
                                    required
                                >
                                    <Input
                                        id="semester"
                                        type="number"
                                        min={1}
                                        max={20}
                                        value={data.semester}
                                        onChange={(e) =>
                                            setData('semester', e.target.value)
                                        }
                                    />
                                </FormField>
                            </>
                        )}
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
