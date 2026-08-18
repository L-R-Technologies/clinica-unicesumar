import { Link, router, useForm, usePage } from '@inertiajs/react';
import { ShieldCheck, ShieldOff, TriangleAlert } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { FormField } from '@/components/form-field';
import { OtpInput } from '@/components/otp-input';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { PageProps } from '@/types';

interface TwoFactorProps extends Record<string, unknown> {
    isTwoFactorEnabled: boolean;
    isPendingConfirmation: boolean;
    qrCodeSvg: string | null;
    setupKey: string | null;
    recoveryCodes: string[];
}

export default function ProfileTwoFactor() {
    const {
        isTwoFactorEnabled,
        isPendingConfirmation,
        qrCodeSvg,
        setupKey,
        recoveryCodes,
    } = usePage<PageProps<TwoFactorProps>>().props;

    const confirmForm = useForm({ code: '' });
    const disableForm = useForm({ code: '' });
    const [isRecoveryCodesDialogOpen, setIsRecoveryCodesDialogOpen] =
        useState(false);
    const [isDisableDialogOpen, setIsDisableDialogOpen] = useState(false);

    function enableTwoFactor(): void {
        router.post(route('two-factor.enable'), {}, { preserveScroll: true });
    }

    function cancelTwoFactorSetup(): void {
        router.delete(route('two-factor.destroy'), { preserveScroll: true });
    }

    function closeDisableDialog(isOpen: boolean): void {
        setIsDisableDialogOpen(isOpen);
        if (!isOpen) {
            disableForm.reset();
            disableForm.clearErrors();
        }
    }

    function disableTwoFactor(event: FormEvent): void {
        event.preventDefault();
        disableForm.delete(route('two-factor.destroy'), {
            errorBag: 'disableTwoFactorAuthentication',
            preserveScroll: true,
            onSuccess: () => {
                disableForm.reset();
                setIsDisableDialogOpen(false);
            },
        });
    }

    function confirmTwoFactor(event: FormEvent): void {
        event.preventDefault();
        confirmForm.post(route('two-factor.confirm'), {
            errorBag: 'confirmTwoFactorAuthentication',
            preserveScroll: true,
            onSuccess: () => {
                confirmForm.reset();
                setIsRecoveryCodesDialogOpen(true);
            },
        });
    }

    return (
        <AppLayout title="Autenticação em Duas Etapas">
            <Card className="mx-auto w-full max-w-lg">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        {isTwoFactorEnabled ? (
                            <ShieldCheck className="size-5 text-emerald-600" />
                        ) : (
                            <ShieldOff className="size-5 text-muted-foreground" />
                        )}
                        Autenticação em duas etapas
                    </CardTitle>
                    <CardDescription>
                        {isTwoFactorEnabled
                            ? 'A autenticação em duas etapas está ativada. Ao entrar, será solicitado um código do seu aplicativo autenticador.'
                            : 'Adicione uma camada extra de segurança exigindo um código do seu aplicativo autenticador ao entrar.'}
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                    {!isTwoFactorEnabled && !isPendingConfirmation && (
                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('home')}>Voltar</Link>
                            </Button>
                            <Button onClick={enableTwoFactor}>Ativar</Button>
                        </div>
                    )}

                    {isPendingConfirmation && qrCodeSvg && (
                        <div className="space-y-4">
                            <p className="text-sm text-muted-foreground">
                                Escaneie o QR code abaixo com seu aplicativo
                                autenticador (Google Authenticator, Authy, etc.)
                                e informe o código gerado para concluir a
                                ativação.
                            </p>
                            <div
                                className="mx-auto w-fit rounded-lg border bg-white p-4"
                                dangerouslySetInnerHTML={{ __html: qrCodeSvg }}
                            />
                            {setupKey && (
                                <p className="text-center text-sm text-muted-foreground">
                                    Chave para configuração manual:{' '}
                                    <span className="font-mono font-medium text-foreground">
                                        {setupKey}
                                    </span>
                                </p>
                            )}
                            <form
                                onSubmit={confirmTwoFactor}
                                className="space-y-4"
                            >
                                <FormField
                                    id="code"
                                    label="Código de autenticação"
                                    error={confirmForm.errors.code}
                                    required
                                >
                                    <OtpInput
                                        id="code"
                                        value={confirmForm.data.code}
                                        onChange={(code) =>
                                            confirmForm.setData('code', code)
                                        }
                                    />
                                </FormField>
                                <div className="flex justify-end gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={cancelTwoFactorSetup}
                                    >
                                        Cancelar
                                    </Button>
                                    <Button
                                        type="submit"
                                        disabled={confirmForm.processing}
                                    >
                                        Confirmar ativação
                                    </Button>
                                </div>
                            </form>
                        </div>
                    )}

                    {isTwoFactorEnabled && (
                        <div className="flex flex-wrap justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('home')}>Voltar</Link>
                            </Button>
                            <Button
                                variant="destructive"
                                onClick={() => setIsDisableDialogOpen(true)}
                            >
                                Desativar
                            </Button>
                        </div>
                    )}
                </CardContent>
            </Card>

            <Dialog
                open={isDisableDialogOpen}
                onOpenChange={closeDisableDialog}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            Desativar autenticação em duas etapas
                        </DialogTitle>
                        <DialogDescription>
                            Para confirmar a desativação, informe o código
                            gerado pelo seu aplicativo autenticador.
                        </DialogDescription>
                    </DialogHeader>
                    <form onSubmit={disableTwoFactor} className="space-y-4">
                        <FormField
                            id="disable_code"
                            label="Código de autenticação"
                            error={disableForm.errors.code}
                            required
                        >
                            <OtpInput
                                id="disable_code"
                                value={disableForm.data.code}
                                onChange={(code) =>
                                    disableForm.setData('code', code)
                                }
                                autoFocus
                            />
                        </FormField>
                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => closeDisableDialog(false)}
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={disableForm.processing}
                            >
                                Desativar
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog
                open={isRecoveryCodesDialogOpen}
                onOpenChange={setIsRecoveryCodesDialogOpen}
            >
                <DialogContent showCloseButton={false}>
                    <DialogHeader>
                        <DialogTitle className="flex items-center gap-2">
                            <TriangleAlert className="size-5 text-amber-500" />
                            Guarde seus códigos de recuperação
                        </DialogTitle>
                        <DialogDescription>
                            Estes códigos serão exibidos{' '}
                            <span className="font-semibold text-foreground">
                                apenas uma vez
                            </span>
                            . Guarde-os em um local seguro — eles permitem
                            recuperar o acesso à sua conta caso você perca seu
                            aplicativo autenticador.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="grid grid-cols-2 gap-2 rounded-lg border bg-muted/50 p-4 font-mono text-sm">
                        {recoveryCodes.map((code) => (
                            <span key={code}>{code}</span>
                        ))}
                    </div>
                    <DialogFooter>
                        <Button
                            onClick={() => setIsRecoveryCodesDialogOpen(false)}
                        >
                            Já guardei meus códigos
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
