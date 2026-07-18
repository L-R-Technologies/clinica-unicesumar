import { Link, router, useForm, usePage } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { FormField } from '@/components/form-field';
import { MaskedInput } from '@/components/masked-input';
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
import type { Address, PageProps, Patient, User } from '@/types';

const CONFIRMATION_KEYWORD = 'CONFIRMAR';

interface ProfileEditProps extends Record<string, unknown> {
    profileUser: User;
    patient: Patient | null;
    address: Address | null;
}

export default function ProfileEdit() {
    const { profileUser, patient, address } = usePage<
        PageProps<ProfileEditProps>
    >().props;

    const isPatient = profileUser.role === 'patient';

    // O Fortify (UpdateUserProfileInformation) valida/atualiza TODOS os campos do
    // paciente ao salvar o perfil. Reenviamos os valores atuais junto de name/email
    // para que a edição não falhe na validação nem sobrescreva outros dados.
    const basicForm = useForm({
        name: profileUser.name,
        email: profileUser.email,
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
                            Atualize seu nome e e-mail de acesso.
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
                                    value={basicForm.data.name}
                                    onChange={(e) =>
                                        basicForm.setData('name', e.target.value)
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
                                    value={basicForm.data.email}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            'email',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

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
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <FormField
                                        id="zip_code"
                                        label="CEP"
                                        error={addressForm.errors.zip_code}
                                        required
                                    >
                                        <MaskedInput
                                            id="zip_code"
                                            mask="cep"
                                            value={addressForm.data.zip_code}
                                            onValueChange={(value) =>
                                                addressForm.setData(
                                                    'zip_code',
                                                    value,
                                                )
                                            }
                                            onBlur={handleCepBlur}
                                        />
                                    </FormField>

                                    <FormField
                                        id="street"
                                        label="Rua"
                                        error={addressForm.errors.street}
                                        required
                                    >
                                        <Input
                                            id="street"
                                            value={addressForm.data.street}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'street',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="number"
                                        label="Número"
                                        error={addressForm.errors.number}
                                        required
                                    >
                                        <Input
                                            id="number"
                                            value={addressForm.data.number}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'number',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="complement"
                                        label="Complemento"
                                        error={addressForm.errors.complement}
                                    >
                                        <Input
                                            id="complement"
                                            value={addressForm.data.complement}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'complement',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="neighborhood"
                                        label="Bairro"
                                        error={addressForm.errors.neighborhood}
                                        required
                                    >
                                        <Input
                                            id="neighborhood"
                                            value={addressForm.data.neighborhood}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'neighborhood',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="city"
                                        label="Cidade"
                                        error={addressForm.errors.city}
                                        required
                                    >
                                        <Input
                                            id="city"
                                            value={addressForm.data.city}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'city',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="state"
                                        label="Estado"
                                        error={addressForm.errors.state}
                                        required
                                    >
                                        <Input
                                            id="state"
                                            value={addressForm.data.state}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'state',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="country"
                                        label="País"
                                        error={addressForm.errors.country}
                                        required
                                    >
                                        <Input
                                            id="country"
                                            value={addressForm.data.country}
                                            onChange={(e) =>
                                                addressForm.setData(
                                                    'country',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </FormField>
                                </div>

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
                                dos seus dados pessoais. Esta ação é irreversível
                                e encerrará sua sessão.
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
                                                    acesso à sua conta. Esta ação
                                                    não pode ser desfeita.
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
                                                    para prosseguir com a exclusão
                                                    dos seus dados.
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
