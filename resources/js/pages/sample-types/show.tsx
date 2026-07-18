import { Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { ActiveBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PageProps, SampleType } from '@/types';

interface SampleTypeShowProps extends Record<string, unknown> {
    sampleType: SampleType;
}

export default function SampleTypeShow() {
    const { sampleType } = usePage<PageProps<SampleTypeShowProps>>().props;

    return (
        <AppLayout
            title={sampleType.name}
            actions={
                <Button asChild>
                    <Link href={route('sample-type.edit', sampleType.id)}>
                        <Pencil />
                        Editar
                    </Link>
                </Button>
            }
        >
            <Card className="max-w-2xl">
                <CardContent className="space-y-4">
                    <div>
                        <p className="text-sm text-muted-foreground">Nome</p>
                        <p className="font-medium">{sampleType.name}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Descrição
                        </p>
                        <p className="font-medium">
                            {sampleType.description || '—'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Status</p>
                        <ActiveBadge active={sampleType.is_active} />
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
