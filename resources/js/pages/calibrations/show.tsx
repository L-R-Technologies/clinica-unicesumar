import { Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { Calibration, PageProps } from '@/types';

interface CalibrationShowProps extends Record<string, unknown> {
    calibration: Calibration;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    const time = isoDate.slice(11, 16);
    return `${day}/${month}/${year} ${time}`;
}

export default function CalibrationShow() {
    const { calibration } = usePage<PageProps<CalibrationShowProps>>().props;

    return (
        <AppLayout
            title={`Calibração #${calibration.id}`}
            actions={
                <div className="flex gap-2">
                    <BackButton href={route('calibrations.index')} />
                    <Button variant="outline" asChild>
                        <Link href={route('calibrations.edit', calibration.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    <ConfirmDeleteDialog
                        action={route('calibrations.destroy', calibration.id)}
                        description="Esta ação removerá permanentemente o registro de calibração."
                        trigger={
                            <Button variant="destructive">Excluir</Button>
                        }
                    />
                </div>
            }
        >
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent className="space-y-4">
                    <div>
                        <p className="text-sm text-muted-foreground">Status</p>
                        <StatusBadge status={calibration.status} />
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Máquina</p>
                        <p className="font-medium">
                            {calibration.machine?.name ?? 'N/A'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Data</p>
                        <p className="font-medium">
                            {formatDate(calibration.calibration_date)}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Valor</p>
                        <p className="font-medium">{calibration.value}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Responsável
                        </p>
                        <p className="font-medium">
                            {calibration.user?.name ?? 'N/A'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Observações
                        </p>
                        <p className="font-medium">
                            {calibration.observation || 'Nenhuma'}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
