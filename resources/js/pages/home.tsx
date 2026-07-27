import { usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import type { PageProps } from '@/types';

interface Stat {
    label: string;
    value: number;
    tone: 'primary' | 'success' | 'warning';
}

const TONE_CLASSES: Record<Stat['tone'], string> = {
    primary: 'text-primary',
    success: 'text-emerald-600 dark:text-emerald-400',
    warning: 'text-amber-600 dark:text-amber-400',
};

interface HomeProps extends Record<string, unknown> {
    stats: Stat[];
}

export default function Home() {
    const { auth, stats } = usePage<PageProps<HomeProps>>().props;

    return (
        <AppLayout title="Dashboard">
            <div className="mb-2">
                <h2 className="text-2xl font-bold">
                    Bem-vindo{auth.user ? `, ${auth.user.name}` : ''}!
                </h2>
                <p className="text-muted-foreground">
                    Resumo da sua atividade na Clínica Unicesumar.
                </p>
            </div>

            {stats.length > 0 && (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {stats.map((stat) => (
                        <Card key={stat.label}>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium text-muted-foreground">
                                    {stat.label}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p
                                    className={cn(
                                        'text-4xl font-bold',
                                        TONE_CLASSES[stat.tone],
                                    )}
                                >
                                    {stat.value}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}
        </AppLayout>
    );
}
