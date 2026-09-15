import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { PasswordField } from '@/components/password-field';
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
import type { UserType } from '@/types';

type UserCreateForm = {
    user_type: UserType;
    name: string;
    email: string;
    password: string;
    registration_number: string;
    professional_license: string;
    ra: string;
    course: string;
    semester: string;
};

export default function UserCreate() {
    const { data, setData, post, processing, errors } = useForm<UserCreateForm>(
        {
            user_type: 'teacher',
            name: '',
            email: '',
            password: '',
            registration_number: '',
            professional_license: '',
            ra: '',
            course: '',
            semester: '',
        },
    );

    function changeUserType(value: string): void {
        if (value !== 'teacher' && value !== 'student') {
            return;
        }

        setData((current) => ({
            ...current,
            user_type: value,
            registration_number: '',
            professional_license: '',
            ra: '',
            course: '',
            semester: '',
        }));
    }

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('user-management.store'));
    }

    return (
        <AppLayout
            title="Novo Usuário"
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
            }
        >
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form
                        id="resource-form"
                        onSubmit={submit}
                        className="space-y-4"
                    >
                        <FormField
                            id="user_type"
                            label="Tipo de usuário"
                            error={errors.user_type}
                            required
                        >
                            <Select
                                value={data.user_type}
                                onValueChange={changeUserType}
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
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />
                        </FormField>

                        <PasswordField
                            value={data.password}
                            onChange={(value) => setData('password', value)}
                            error={errors.password}
                            required
                        />

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
                                    id="professional_license"
                                    label="CRBM (opcional)"
                                    error={errors.professional_license}
                                >
                                    <Input
                                        id="professional_license"
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
