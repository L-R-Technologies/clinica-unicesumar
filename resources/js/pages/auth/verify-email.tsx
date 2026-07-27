import { router, useForm, usePage } from '@inertiajs/react';
import type { ReactElement } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import type { PageProps } from '@/types';

const VERIFICATION_LINK_SENT = 'verification-link-sent';

interface VerifyEmailProps extends Record<string, unknown> {
    status: string | null;
}

export default function VerifyEmail(): ReactElement {
    const { status } = usePage<PageProps<VerifyEmailProps>>().props;
    const { post, processing } = useForm({});

    const linkWasSent = status === VERIFICATION_LINK_SENT;

    function resendVerification(): void {
        post(route('verification.send'));
    }

    function logout(): void {
        router.post(route('logout'));
    }

    return (
        <AuthLayout
            title="Verifique seu e-mail"
            description="Confirme seu endereço de e-mail para continuar."
        >
            {linkWasSent && (
                <p className="mb-4 text-sm font-medium text-emerald-600">
                    Um novo link de verificação foi enviado para o seu e-mail.
                </p>
            )}

            <div className="space-y-4">
                <p className="text-sm text-muted-foreground">
                    Enviamos um link de verificação para o seu e-mail. Clique no
                    link para ativar sua conta. Se não encontrar a mensagem,
                    verifique a caixa de spam ou solicite um novo envio.
                </p>

                <Button
                    type="button"
                    className="w-full"
                    disabled={processing}
                    onClick={resendVerification}
                >
                    Reenviar e-mail de verificação
                </Button>

                <Button
                    type="button"
                    variant="outline"
                    className="w-full"
                    onClick={logout}
                >
                    Sair
                </Button>
            </div>
        </AuthLayout>
    );
}
