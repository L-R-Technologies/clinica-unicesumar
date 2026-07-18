import { Link, usePage } from '@inertiajs/react';
import { ClipboardList, FlaskConical, Microscope } from 'lucide-react';
import GuestLayout from '@/layouts/guest-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PageProps } from '@/types';

const FEATURES = [
    {
        icon: FlaskConical,
        title: 'Gestão de Amostras',
        description:
            'Cadastro e acompanhamento de amostras biológicas com rastreabilidade completa.',
    },
    {
        icon: ClipboardList,
        title: 'Anamneses',
        description:
            'Registro clínico detalhado do histórico de cada paciente da clínica-escola.',
    },
    {
        icon: Microscope,
        title: 'Exames Laboratoriais',
        description:
            'Emissão, aprovação e disponibilização de resultados com laudo em PDF.',
    },
] as const;

export default function Welcome() {
    const { auth } = usePage<PageProps>().props;

    return (
        <GuestLayout title="Bem-vindo">
            <section className="mx-auto w-full max-w-5xl px-6 py-16 text-center">
                <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
                    Clínica Unicesumar
                </h1>
                <p className="mx-auto mt-4 max-w-2xl text-lg text-muted-foreground">
                    Sistema de gestão da clínica-escola para amostras, anamneses
                    e exames laboratoriais.
                </p>
                <div className="mt-8 flex justify-center gap-3">
                    {auth.user ? (
                        <Button size="lg" asChild>
                            <Link href="/home">Acessar Painel</Link>
                        </Button>
                    ) : (
                        <>
                            <Button size="lg" asChild>
                                <Link href={route('register')}>
                                    Criar conta
                                </Link>
                            </Button>
                            <Button size="lg" variant="outline" asChild>
                                <Link href={route('login')}>Entrar</Link>
                            </Button>
                        </>
                    )}
                </div>

                <div className="mt-16 grid gap-6 sm:grid-cols-3">
                    {FEATURES.map((feature) => (
                        <Card key={feature.title}>
                            <CardContent className="flex flex-col items-center gap-3 p-6 text-center">
                                <feature.icon className="size-8 text-primary" />
                                <h2 className="font-semibold">
                                    {feature.title}
                                </h2>
                                <p className="text-sm text-muted-foreground">
                                    {feature.description}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </section>
        </GuestLayout>
    );
}
