import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { AppSidebar } from '@/components/app-sidebar';
import { FlashToaster } from '@/components/flash-toaster';
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

export default function AppLayout({
    title,
    children,
    actions,
}: AppLayoutProps) {
    return (
        <SidebarProvider>
            <Head title={title} />
            <AppSidebar />
            <SidebarInset>
                <header className="flex h-16 shrink-0 items-center gap-2 border-b px-4">
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
                <main className="flex flex-1 flex-col p-4 sm:p-6">
                    <div className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-4">
                        {children}
                    </div>
                </main>
            </SidebarInset>
            <Toaster richColors position="top-right" />
            <FlashToaster />
        </SidebarProvider>
    );
}
