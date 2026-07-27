import { Head, Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { FlashToaster } from '@/components/flash-toaster';
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/sonner';
import type { PageProps } from '@/types';

interface GuestLayoutProps {
    title: string;
    children: ReactNode;
}

export default function GuestLayout({ title, children }: GuestLayoutProps) {
    const { auth } = usePage<PageProps>().props;

    return (
        <div className="flex min-h-screen flex-col">
            <Head title={title} />
            <header className="flex h-20 items-center bg-primary px-6 text-primary-foreground">
                <Link href="/home" className="flex items-center gap-3">
                    <img
                        src="/imgs/unicesumar-logo.png"
                        alt="Unicesumar"
                        className="h-12 w-auto"
                    />
                    <span className="text-xl font-bold">
                        Clínica Unicesumar
                    </span>
                </Link>
                <nav className="ml-auto flex items-center gap-2">
                    {auth.user ? (
                        <Button variant="secondary" asChild>
                            <Link href="/home">Painel</Link>
                        </Button>
                    ) : (
                        <>
                            <Button
                                variant="outline"
                                className="border-primary-foreground/40 bg-transparent text-primary-foreground hover:bg-primary-foreground hover:text-primary"
                                asChild
                            >
                                <Link href={route('login')}>Entrar</Link>
                            </Button>
                            <Button variant="secondary" asChild>
                                <Link href={route('register')}>Registrar</Link>
                            </Button>
                        </>
                    )}
                </nav>
            </header>
            <main className="flex-1">{children}</main>
            <Toaster richColors position="bottom-right" />
            <FlashToaster />
        </div>
    );
}
