import { Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PageProps, Sample } from '@/types';

interface SampleShowProps extends Record<string, unknown> {
    sample: Sample;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

export default function SampleShow() {
    const { sample } = usePage<PageProps<SampleShowProps>>().props;

    return (
        <AppLayout
            title={`Amostra ${sample.code}`}
            actions={
                <Button asChild>
                    <Link href={route('samples.edit', sample.id)}>
                        <Pencil />
                        Editar
                    </Link>
                </Button>
            }
        >
            <Card className="max-w-2xl">
                <CardContent className="space-y-4">
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Código único
                        </p>
                        <p className="font-mono font-medium">{sample.code}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Paciente</p>
                        <p className="font-medium">
                            {sample.patient?.user?.name ?? 'N/A'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Registrado por
                        </p>
                        <p className="font-medium">
                            {sample.user?.name ?? 'N/A'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Tipo de amostra
                        </p>
                        <p className="font-medium">
                            {sample.sample_type?.name ?? 'N/A'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Data da coleta
                        </p>
                        <p className="font-medium">{formatDate(sample.date)}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Status</p>
                        <StatusBadge status={sample.status} />
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Localização
                        </p>
                        <p className="font-medium">
                            {sample.location || 'Não informada'}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
