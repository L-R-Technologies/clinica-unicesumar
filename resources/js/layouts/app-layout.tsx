import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { AppSidebar } from '@/components/app-sidebar';
import { FlashToaster } from '@/components/flash-toaster';
import { MAIN_CONTENT_ID, SkipLink } from '@/components/skip-link';
import { Separator } from '@/components/ui/separator';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';

interface AppLayoutProps {
    title: string;
    children: ReactNode;
    /** Ações opcionais renderizadas à direita do header (ex.: botão "Novo"). */
    actions?: ReactNode;
}

/**
 * Cada página Inertia remonta o AppLayout (não há layout persistente aqui),
 * então o estado da sidebar não sobrevive à navegação por si só. O componente
 * shadcn já grava o cookie `sidebar_state` ao expandir/colapsar, mas nunca o lê
 * de volta (ele espera um Server Component lendo o cookie, o que não existe
 * numa SPA Inertia). Lendo o cookie aqui e usando como `defaultOpen`, o estado
 * visual é restaurado a cada novo mount.
 */
function getStoredSidebarOpen(): boolean {
    if (typeof document === 'undefined') {
        return true;
    }

    const match = document.cookie.match(/(?:^|;\s*)sidebar_state=(true|false)/);
    return match ? match[1] === 'true' : true;
}

export default function AppLayout({
    title,
    children,
    actions,
}: AppLayoutProps) {
    return (
        <SidebarProvider defaultOpen={getStoredSidebarOpen()}>
            <Head title={title} />
            <SkipLink />
            <AppSidebar />
            <SidebarInset>
                <header className="sticky top-0 z-10 flex h-16 shrink-0 items-center gap-2 border-b bg-background px-4">
                    <SidebarTrigger className="-ml-1" />
                    <Separator
                        orientation="vertical"
                        className="mr-2 data-[orientation=vertical]:h-4"
                    />
                    <h1 className="text-base font-semibold">{title}</h1>
                    {actions && (
                        <div className="ml-auto flex items-center gap-2">
                            {actions}
                        </div>
                    )}
                </header>
                <main id={MAIN_CONTENT_ID} className="flex flex-1 flex-col p-4 sm:p-6">
                    <div className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-4">
                        {children}
                    </div>
                </main>
            </SidebarInset>
            <Toaster richColors position="bottom-right" />
            <FlashToaster />
        </SidebarProvider>
    );
}
