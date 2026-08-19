import { Link, router, useForm, usePage } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { AddressFields } from '@/components/address-fields';
import { FormField } from '@/components/form-field';
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { fetchAddressByCep, formatCep, stripDigits } from '@/lib/masks';
import type {
    Address,
    PageProps,
    Patient,
    Student,
    Teacher,
    User,
} from '@/types';

const CONFIRMATION_KEYWORD = 'CONFIRMAR';

interface ProfileEditProps extends Record<string, unknown> {
    profileUser: User;
    patient: Patient | null;
    address: Address | null;
    teacher: Teacher | null;
    student: Student | null;
}

export default function ProfileEdit() {
    const { profileUser, patient, address, teacher, student } =
        usePage<PageProps<ProfileEditProps>>().props;

    const isPatient = profileUser.role === 'patient';
    const isTeacher = profileUser.role === 'teacher';
    const isStudent = profileUser.role === 'student';

    // O Fortify (UpdateUserProfileInformation) valida/atualiza TODOS os campos do
    // perfil (paciente, professor ou aluno) ao salvar. Reenviamos os valores
    // atuais junto de name/email para que a edição não falhe na validação nem
    // sobrescreva outros dados.
    const basicForm = useForm({
        name: profileUser.name,
        email: profileUser.email,
        registration_number: teacher?.registration_number ?? '',
        professional_license: teacher?.professional_license ?? '',
        ra: student?.ra ?? '',
        course: student?.course ?? '',
        birthday: patient?.birthday ?? '',
        ethnicity: patient?.ethnicity ?? '',
        sex: patient?.sex ?? '',
        cpf: patient?.cpf ?? '',
        rg: patient?.rg ?? '',
        phone: patient?.phone ?? '',
        street: address?.street ?? '',
        number: address?.number ?? '',
        complement: address?.complement ?? '',
        neighborhood: address?.neighborhood ?? '',
        city: address?.city ?? '',
        state: address?.state ?? '',
        country: address?.country ?? '',
        zip_code: address?.zip_code ?? '',
    });

    const addressForm = useForm({
        street: address?.street ?? '',
        number: address?.number ?? '',
        complement: address?.complement ?? '',
        neighborhood: address?.neighborhood ?? '',
        city: address?.city ?? '',
        state: address?.state ?? '',
        country: address?.country ?? '',
        zip_code: address?.zip_code ? formatCep(address.zip_code) : '',
    });

    const [anonymizeOpen, setAnonymizeOpen] = useState(false);
    const [anonymizeStage, setAnonymizeStage] = useState<1 | 2>(1);
    const [confirmationText, setConfirmationText] = useState('');
    const [anonymizing, setAnonymizing] = useState(false);

    function submitBasic(event: FormEvent): void {
        event.preventDefault();
        basicForm.put(route('user-profile-information.update'), {
            errorBag: 'updateProfileInformation',
            preserveScroll: true,
        });
    }

    async function handleCepBlur(): Promise<void> {
        const result = await fetchAddressByCep(addressForm.data.zip_code);
        if (!result) {
            return;
        }

        addressForm.setData((previous) => ({
            ...previous,
            street: result.street,
            neighborhood: result.neighborhood,
            city: result.city,
            state: result.state,
            country: 'Brasil',
        }));
    }

    function submitAddress(event: FormEvent): void {
        event.preventDefault();
        addressForm.put(route('user.address.update', profileUser.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Mantém o form de dados básicos em sincronia, pois o Fortify
                // reenvia o endereço ao atualizar name/email.
                basicForm.setData((previous) => ({
                    ...previous,
                    street: addressForm.data.street,
                    number: addressForm.data.number,
                    complement: addressForm.data.complement,
                    neighborhood: addressForm.data.neighborhood,
                    city: addressForm.data.city,
                    state: addressForm.data.state,
                    country: addressForm.data.country,
                    zip_code: stripDigits(addressForm.data.zip_code),
                }));
            },
        });
    }

    function resetAnonymizeDialog(): void {
        setAnonymizeStage(1);
        setConfirmationText('');
    }

    function handleAnonymize(): void {
        router.post(
            route('user.anonymize'),
            { confirmation: confirmationText },
            {
                preserveScroll: true,
                onStart: () => setAnonymizing(true),
                onFinish: () => setAnonymizing(false),
            },
        );
    }

    return (
        <AppLayout title="Editar Perfil">
            <div className="flex flex-col gap-6">
                <Card className="mx-auto w-full max-w-2xl">
                    <CardHeader>
                        <CardTitle>Dados básicos</CardTitle>
                        <CardDescription>
                            Atualize seus dados de acesso e cadastro.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submitBasic} className="space-y-4">
                            <FormField
                                id="name"
                                label="Nome"
                                error={basicForm.errors.name}
                                required
                            >
                                <Input
                                    id="name"
                                    maxLength={255}
                                    value={basicForm.data.name}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            'name',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id="email"
                                label="E-mail"
                                error={basicForm.errors.email}
                                required
                            >
                                <Input
                                    id="email"
                                    type="email"
                                    maxLength={255}
                                    value={basicForm.data.email}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            'email',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            {isTeacher && (
                                <>
                                    <FormField
                                        id="registration_number"
                                        label="Número de registro"
                                        error={
                                            basicForm.errors
                                                .registration_number
                                        }
                                        required
                                    >
                                        <Input
                                            id="registration_number"
                                            maxLength={10}
                                            value={
                                                basicForm.data
                                                    .registration_number
                                            }
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    'registration_number',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="professional_license"
                                        label="CRBM (opcional)"
                                        error={
                                            basicForm.errors
                                                .professional_license
                                        }
                                    >
                                        <Input
                                            id="professional_license"
                                            maxLength={10}
                                            value={
                                                basicForm.data
                                                    .professional_license
                                            }
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    'professional_license',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>
                                </>
                            )}

                            {isStudent && (
                                <>
                                    <FormField
                                        id="ra"
                                        label="RA (registro acadêmico)"
                                        error={basicForm.errors.ra}
                                        required
                                    >
                                        <Input
                                            id="ra"
                                            maxLength={9}
                                            value={basicForm.data.ra}
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    'ra',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="course"
                                        label="Curso"
                                        error={basicForm.errors.course}
                                        required
                                    >
                                        <Input
                                            id="course"
                                            maxLength={255}
                                            value={basicForm.data.course}
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    'course',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>
                                </>
                            )}

                            <div className="flex justify-between gap-2">
                                <Button variant="outline" asChild>
                                    <Link
                                        href={route(
                                            'user.password-edit',
                                            profileUser.id,
                                        )}
                                    >
                                        Alterar senha
                                    </Link>
                                </Button>
                                <Button
                                    type="submit"
                                    disabled={basicForm.processing}
                                >
                                    Salvar
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                {isPatient && (
                    <Card className="mx-auto w-full max-w-2xl">
                        <CardHeader>
                            <CardTitle>Endereço</CardTitle>
                            <CardDescription>
                                Informe o CEP para preenchimento automático.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form
                                onSubmit={submitAddress}
                                className="space-y-4"
                            >
                                <AddressFields
                                    values={{
                                        zip_code: addressForm.data.zip_code,
                                        street: addressForm.data.street,
                                        number: addressForm.data.number,
                                        complement: addressForm.data.complement,
                                        neighborhood:
                                            addressForm.data.neighborhood,
                                        city: addressForm.data.city,
                                        state: addressForm.data.state,
                                        country: addressForm.data.country,
                                    }}
                                    errors={addressForm.errors}
                                    onChange={(field, value) =>
                                        addressForm.setData(field, value)
                                    }
                                    onCepBlur={handleCepBlur}
                                />

                                <div className="flex justify-end">
                                    <Button
                                        type="submit"
                                        disabled={addressForm.processing}
                                    >
                                        Salvar endereço
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                )}

                {isPatient && (
                    <Card className="mx-auto w-full max-w-2xl border-destructive/50">
                        <CardHeader>
                            <CardTitle className="text-destructive">
                                Revogar acesso e anonimizar dados
                            </CardTitle>
                            <CardDescription>
                                Conforme a LGPD, você pode solicitar a exclusão
                                dos seus dados pessoais. Esta ação é
                                irreversível e encerrará sua sessão.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <AlertDialog
                                open={anonymizeOpen}
                                onOpenChange={(open) => {
                                    setAnonymizeOpen(open);
                                    if (!open) {
                                        resetAnonymizeDialog();
                                    }
                                }}
                            >
                                <AlertDialogTrigger asChild>
                                    <Button variant="destructive">
                                        Anonimizar meus dados
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    {anonymizeStage === 1 ? (
                                        <>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>
                                                    Tem certeza absoluta?
                                                </AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    Todos os seus dados pessoais
                                                    serão anonimizados de forma
                                                    permanente e você perderá o
                                                    acesso à sua conta. Esta
                                                    ação não pode ser desfeita.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>
                                                    Cancelar
                                                </AlertDialogCancel>
                                                <Button
                                                    variant="destructive"
                                                    onClick={() =>
                                                        setAnonymizeStage(2)
                                                    }
                                                >
                                                    Continuar
                                                </Button>
                                            </AlertDialogFooter>
                                        </>
                                    ) : (
                                        <>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>
                                                    Confirmação final
                                                </AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    Digite{' '}
                                                    <strong>
                                                        {CONFIRMATION_KEYWORD}
                                                    </strong>{' '}
                                                    para prosseguir com a
                                                    exclusão dos seus dados.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <div className="space-y-2">
                                                <Label htmlFor="confirmation">
                                                    Confirmação
                                                </Label>
                                                <Input
                                                    id="confirmation"
                                                    value={confirmationText}
                                                    onChange={(e) =>
                                                        setConfirmationText(
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder={
                                                        CONFIRMATION_KEYWORD
                                                    }
                                                    autoComplete="off"
                                                />
                                            </div>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>
                                                    Cancelar
                                                </AlertDialogCancel>
                                                <Button
                                                    className={cn(
                                                        buttonVariants({
                                                            variant:
                                                                'destructive',
                                                        }),
                                                    )}
                                                    disabled={
                                                        confirmationText !==
                                                            CONFIRMATION_KEYWORD ||
                                                        anonymizing
                                                    }
                                                    onClick={handleAnonymize}
                                                >
                                                    Apagar meus dados
                                                </Button>
                                            </AlertDialogFooter>
                                        </>
                                    )}
                                </AlertDialogContent>
                            </AlertDialog>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
