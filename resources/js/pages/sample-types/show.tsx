import { Link, usePage } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
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
                <>
                    <BackButton href={route('sample-type.index')} />
                    <Button asChild>
                        <Link href={route('sample-type.edit', sampleType.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    <ConfirmDeleteDialog
                        action={route('sample-type.destroy', sampleType.id)}
                        title="Excluir tipo de amostra"
                        description="O tipo de amostra será removido permanentemente."
                        trigger={
                            <Button variant="destructive">
                                <Trash2 />
                                Excluir
                            </Button>
                        }
                    />
                </>
            }
        >
            <Card className="mx-auto w-full max-w-2xl">
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
