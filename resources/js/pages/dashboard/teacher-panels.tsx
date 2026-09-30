import { Link } from '@inertiajs/react';
import { AlertTriangle, CheckCircle2, Snowflake } from 'lucide-react';
import type { ReactElement } from 'react';
import { DataTable, type Column } from '@/components/data-table';
import { StatusBadge } from '@/components/status-badge';
import { buttonVariants } from '@/components/ui/button';
import { formatDate, formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { SampleStatus } from '@/types';
import type {
    ApprovalQueueItem,
    MachineOverview,
    OverdueSample,
    SamplesOverview,
} from './types';
import { BarList, EmptyState, formatNumber } from './widgets';

export const STALE_APPROVAL_HOURS = 48;
export const FREEZER_ALERT_DAYS = 7;
export const CALIBRATION_DUE_DAYS = 30;

const SAMPLE_BAR_CLASSES: Record<SampleStatus, string> = {
    'under review': 'bg-amber-500 dark:bg-amber-400',
    stored: 'bg-emerald-500 dark:bg-emerald-400',
    discarded: 'bg-muted-foreground/40',
};

const ALERT_CLASSES =
    'flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300';
const OK_CLASSES =
    'flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300';

function pluralize(count: number, singular: string, plural: string): string {
    return count === 1 ? `1 ${singular}` : `${formatNumber(count)} ${plural}`;
}

export function ApprovalQueue({
    exams,
    staleCount,
}: {
    exams: ApprovalQueueItem[];
    staleCount: number;
}): ReactElement {
    if (exams.length === 0) {
        return (
            <p className={OK_CLASSES}>
                <CheckCircle2 className="size-4 shrink-0" aria-hidden="true" />
                Nenhum exame aguardando sua aprovação.
            </p>
        );
    }

    const columns: Column<ApprovalQueueItem>[] = [
        { header: 'Aluno', cell: (row) => row.student },
        { header: 'Exame', cell: (row) => row.exam_type },
        { header: 'Paciente', cell: (row) => row.patient },
        {
            header: 'Enviado em',
            cell: (row) => formatDateTime(row.submitted_at),
        },
        {
            header: 'Aguardando',
            className: 'tabular-nums',
            cell: (row) => (
                <span
                    className={cn(
                        row.is_stale &&
                            'font-medium text-amber-700 dark:text-amber-400',
                    )}
                >
                    {formatNumber(row.waiting_hours)} h
                </span>
            ),
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <Link
                    href={route('exam.show', row.id)}
                    className={cn(
                        buttonVariants({ variant: 'outline', size: 'sm' }),
                    )}
                >
                    Revisar
                </Link>
            ),
        },
    ];

    return (
        <div className="space-y-3">
            {staleCount > 0 ? (
                <p className={ALERT_CLASSES}>
                    <AlertTriangle
                        className="size-4 shrink-0"
                        aria-hidden="true"
                    />
                    {pluralize(staleCount, 'exame aguarda', 'exames aguardam')}{' '}
                    aprovação há mais de {STALE_APPROVAL_HOURS} horas.
                </p>
            ) : (
                <p className={OK_CLASSES}>
                    <CheckCircle2
                        className="size-4 shrink-0"
                        aria-hidden="true"
                    />
                    Nenhum exame parado há mais de {STALE_APPROVAL_HOURS} horas.
                </p>
            )}
            <DataTable
                columns={columns}
                rows={exams}
                getRowKey={(row) => row.id}
            />
        </div>
    );
}

function OverdueSamplesList({
    samples,
}: {
    samples: OverdueSample[];
}): ReactElement {
    if (samples.length === 0) {
        return (
            <p className="flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400">
                <CheckCircle2 className="size-4" aria-hidden="true" />
                Nenhuma amostra há mais de {FREEZER_ALERT_DAYS} dias no freezer.
            </p>
        );
    }

    return (
        <ul className="divide-y" role="list">
            {samples.map((sample) => (
                <li
                    key={sample.id}
                    className="flex items-center justify-between gap-3 py-2 first:pt-0 last:pb-0 text-sm"
                >
                    <div className="min-w-0">
                        <Link
                            href={route('samples.show', sample.id)}
                            className="font-medium hover:underline"
                        >
                            {sample.code}
                        </Link>
                        <p className="truncate text-muted-foreground">
                            {sample.patient}
                        </p>
                    </div>
                    <span className="flex shrink-0 items-center gap-1 text-amber-700 tabular-nums dark:text-amber-400">
                        <Snowflake className="size-4" aria-hidden="true" />
                        {pluralize(sample.days, 'dia', 'dias')}
                    </span>
                </li>
            ))}
        </ul>
    );
}

export function SamplesPanel({
    samples,
}: {
    samples: SamplesOverview;
}): ReactElement {
    return (
        <div className="space-y-5">
            <BarList
                items={samples.distribution.map((item) => ({
                    key: item.status,
                    label: item.label,
                    value: item.count,
                    barClassName: SAMPLE_BAR_CLASSES[item.status],
                }))}
                emptyMessage="Nenhuma amostra cadastrada."
            />
            <div className="space-y-2">
                <h3 className="text-sm font-medium">
                    Armazenadas há mais de {FREEZER_ALERT_DAYS} dias
                </h3>
                <OverdueSamplesList samples={samples.overdueInFreezer} />
            </div>
        </div>
    );
}

export function MachinesPanel({
    machines,
}: {
    machines: MachineOverview[];
}): ReactElement {
    if (machines.length === 0) {
        return <EmptyState>Nenhum equipamento cadastrado.</EmptyState>;
    }

    return (
        <ul className="divide-y" role="list">
            {machines.map((machine) => (
                <li
                    key={machine.id}
                    className="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 py-3 first:pt-0 last:pb-0"
                >
                    <div className="min-w-0 space-y-1">
                        <div className="flex flex-wrap items-center gap-2">
                            <Link
                                href={route('machines.show', machine.id)}
                                className="font-medium hover:underline"
                            >
                                {machine.name}
                            </Link>
                            <StatusBadge status={machine.status} />
                        </div>
                        <p className="text-sm text-muted-foreground">
                            {machine.last_calibration_at
                                ? `Última calibração em ${formatDate(machine.last_calibration_at)}`
                                : 'Nenhuma calibração registrada'}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        {machine.last_calibration_status && (
                            <StatusBadge
                                status={machine.last_calibration_status}
                            />
                        )}
                        {machine.is_calibration_due && (
                            <span className="flex items-center gap-1 text-sm text-amber-700 dark:text-amber-400">
                                <AlertTriangle
                                    className="size-4"
                                    aria-hidden="true"
                                />
                                Calibração vencida
                            </span>
                        )}
                    </div>
                </li>
            ))}
        </ul>
    );
}
