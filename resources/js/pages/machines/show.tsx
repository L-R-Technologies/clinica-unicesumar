import { Link, usePage } from '@inertiajs/react';
import { Download, Eye, Pencil, Plus } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { StatusBadge } from '@/components/status-badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import type { Calibration, Machine, PageProps } from '@/types';

interface MachineShowProps extends Record<string, unknown> {
    machine: Machine;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

function formatValue(value: number): string {
    return value.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export default function MachineShow() {
    const { machine } = usePage<PageProps<MachineShowProps>>().props;
    const calibrations = machine.calibrations ?? [];

    const columns: Column<Calibration>[] = [
        { header: 'Data', cell: (row) => formatDate(row.calibration_date) },
        {
            header: 'Valor',
            cell: (row) => (
                <span className="font-medium">{formatValue(row.value)}</span>
            ),
        },
        {
            header: 'Status',
            cell: (row) => <StatusBadge status={row.status} />,
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('calibrations.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar calibração"
                    >
                        <Eye className="size-4" />
                    </Link>
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title={`Prontuário: ${machine.name}`}
            actions={
                <div className="flex gap-2">
                    <Button variant="outline" asChild>
                        <Link href={route('machines.edit', machine.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    <Button variant="outline" asChild>
                        <a href={route('machines.pdf', machine.id)}>
                            <Download />
                            Exportar PDF
                        </a>
                    </Button>
                    <Button asChild>
                        <Link href={route('calibrations.create', machine.id)}>
                            <Plus />
                            Nova calibração
                        </Link>
                    </Button>
                </div>
            }
        >
            <Card className="max-w-3xl">
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Equipamento
                        </p>
                        <p className="font-medium">{machine.name}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Modelo</p>
                        <p className="font-medium">{machine.model || '—'}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Número de série
                        </p>
                        <p className="font-mono font-medium">
                            {machine.serial_number || '—'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Localização
                        </p>
                        <p className="font-medium">{machine.location || '—'}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Faixa de calibração
                        </p>
                        <p className="font-medium">
                            {machine.calibration_range_min !== null &&
                            machine.calibration_range_max !== null
                                ? `${formatValue(machine.calibration_range_min)} — ${formatValue(machine.calibration_range_max)}`
                                : '—'}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">Status</p>
                        <StatusBadge status={machine.status} />
                    </div>
                </CardContent>
            </Card>

            <div>
                <h2 className="mb-3 text-lg font-semibold">
                    Histórico de calibrações
                </h2>
                <DataTable
                    columns={columns}
                    rows={calibrations}
                    getRowKey={(row) => row.id}
                    emptyMessage="Nenhuma calibração registrada para este equipamento."
                />
            </div>
        </AppLayout>
    );
}
