import { Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, RefreshCw } from 'lucide-react';
import { type FormEvent, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const PASSWORD_ALPHABET =
    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
const GENERATED_PASSWORD_LENGTH = 12;

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

type UserType = 'teacher' | 'student';

export default function UserCreate() {
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        user_type: 'teacher' as UserType,
        name: '',
        email: '',
        password: '',
        registration_number: '',
        crbm: '',
        ra: '',
        course: '',
    });

    function changeUserType(value: UserType): void {
        setData((current) => ({
            ...current,
            user_type: value,
            registration_number: '',
            crbm: '',
            ra: '',
            course: '',
        }));
    }

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('user-management.store'));
    }

    return (
        <AppLayout title="Novo Usuário"
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
                        Salvar
                    </Button>
                </>
            }>
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form id="resource-form" onSubmit={submit} className="space-y-4">
                        <FormField
                            id="user_type"
                            label="Tipo de usuário"
                            error={errors.user_type}
                            required
                        >
                            <Select
                                value={data.user_type}
                                onValueChange={(value) =>
                                    changeUserType(value as UserType)
                                }
                            >
                                <SelectTrigger id="user_type">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="teacher">
                                        Professor
                                    </SelectItem>
                                    <SelectItem value="student">
                                        Estudante
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </FormField>

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
                            label="Senha"
                            error={errors.password}
                            required
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
                                Mínimo de 8 caracteres.
                            </p>
                        </FormField>

                        {data.user_type === 'teacher' && (
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

                        {data.user_type === 'student' && (
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

                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
