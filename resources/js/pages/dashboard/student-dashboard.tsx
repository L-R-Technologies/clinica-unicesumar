import { Link } from '@inertiajs/react';
import { CheckCircle2 } from 'lucide-react';
import type { ReactElement } from 'react';
import { DataTable, type Column } from '@/components/data-table';
import { StatusBadge } from '@/components/status-badge';
import { buttonVariants } from '@/components/ui/button';
import { formatDate, formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { ActionRequiredExam, StudentDashboardData } from './types';
import { BarList, EmptyState, SectionCard, StatTile } from './widgets';

function ActionRequiredTable({
    exams,
}: {
    exams: ActionRequiredExam[];
}): ReactElement {
    if (exams.length === 0) {
        return (
            <p className="flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400">
                <CheckCircle2 className="size-4" aria-hidden="true" />
                Nenhum exame pendente ou rejeitado. Tudo em dia!
            </p>
        );
    }

    const columns: Column<ActionRequiredExam>[] = [
        { header: 'Exame', cell: (row) => row.exam_type },
        { header: 'Paciente', cell: (row) => row.patient },
        { header: 'Data', cell: (row) => formatDate(row.date) },
        {
            header: 'Status',
            cell: (row) => <StatusBadge status={row.status} />,
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <Link
                    href={route('exam.edit', row.id)}
                    className={cn(
                        buttonVariants({ variant: 'outline', size: 'sm' }),
                    )}
                >
                    {row.status === 'rejected' ? 'Corrigir' : 'Continuar'}
                </Link>
            ),
        },
    ];

    return (
        <DataTable columns={columns} rows={exams} getRowKey={(row) => row.id} />
    );
}

export function StudentDashboard({
    data,
}: {
    data: StudentDashboardData;
}): ReactElement {
    return (
        <div className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {data.stats.map((stat) => (
                    <StatTile key={stat.label} stat={stat} />
                ))}
            </div>

            <SectionCard
                title="Exames que dependem de você"
                description="Exames pendentes de envio ou rejeitados pelo professor."
            >
                <ActionRequiredTable exams={data.actionRequired} />
            </SectionCard>

            <div className="grid gap-6 lg:grid-cols-2">
                <SectionCard
                    title="Meus exames por status"
                    description="Todos os exames que você registrou."
                >
                    <BarList
                        items={data.statusDistribution.map((item) => ({
                            key: item.status,
                            label: item.label,
                            value: item.count,
                            status: item.status,
                        }))}
                        emptyMessage="Você ainda não registrou exames."
                    />
                </SectionCard>

                <SectionCard
                    title="Meus exames por mês"
                    description="Pela data do exame, últimos 6 meses."
                >
                    <BarList
                        items={data.monthlyVolume.map((item) => ({
                            key: item.label,
                            label: item.label,
                            value: item.count,
                        }))}
                        emptyMessage="Nenhum exame no período."
                    />
                </SectionCard>
            </div>

            <SectionCard
                title="Últimas rejeições"
                description="Justificativas dos professores para você revisar."
            >
                {data.recentRejections.length === 0 ? (
                    <EmptyState>Nenhuma rejeição registrada.</EmptyState>
                ) : (
                    <ul className="divide-y">
                        {data.recentRejections.map((rejection, index) => (
                            <li
                                key={`${rejection.exam_id}-${index}`}
                                className="space-y-1 py-3 first:pt-0 last:pb-0"
                            >
                                <div className="flex flex-wrap items-baseline justify-between gap-2">
                                    <Link
                                        href={route(
                                            'exam.show',
                                            rejection.exam_id,
                                        )}
                                        className="font-medium hover:underline"
                                    >
                                        {rejection.exam_type}
                                    </Link>
                                    <span className="text-xs text-muted-foreground">
                                        {rejection.rejected_by} ·{' '}
                                        {formatDateTime(rejection.rejected_at)}
                                    </span>
                                </div>
                                <p className="text-sm whitespace-pre-line text-muted-foreground">
                                    {rejection.justification}
                                </p>
                            </li>
                        ))}
                    </ul>
                )}
            </SectionCard>
        </div>
    );
}
