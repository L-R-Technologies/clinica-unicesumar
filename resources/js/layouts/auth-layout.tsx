import { Head, Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { FlashToaster } from '@/components/flash-toaster';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Toaster } from '@/components/ui/sonner';

interface AuthLayoutProps {
    title: string;
    description?: string;
    children: ReactNode;
}

export default function AuthLayout({
    title,
    description,
    children,
}: AuthLayoutProps) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-6 bg-muted p-6">
            <Head title={title} />
            <Link href="/" className="flex items-center gap-3">
                <img
                    src="/imgs/unicesumar-logo.png"
                    alt="Unicesumar"
                    className="h-12 w-auto"
                />
                <span className="text-xl font-bold text-foreground">
                    Clínica Unicesumar
                </span>
            </Link>
            <Card className="w-full max-w-md">
                <CardHeader>
                    <CardTitle className="text-2xl">{title}</CardTitle>
                    {description && (
                        <CardDescription>{description}</CardDescription>
                    )}
                </CardHeader>
                <CardContent>{children}</CardContent>
            </Card>
            <Toaster richColors position="top-right" />
            <FlashToaster />
        </div>
    );
}
