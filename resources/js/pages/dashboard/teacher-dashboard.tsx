import { Link } from '@inertiajs/react';
import { AlertTriangle, CheckCircle2 } from 'lucide-react';
import type { ReactElement } from 'react';
import { DataTable, type Column } from '@/components/data-table';
import { buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { StaleApproval, TeacherDashboardData } from './types';
import { BarList, SectionCard, StatTile, formatNumber } from './widgets';

const STALE_APPROVAL_HOURS = 48;
const INACTIVE_STUDENT_DAYS = 30;

function StaleApprovalsAlert({
    exams,
}: {
    exams: StaleApproval[];
}): ReactElement {
    if (exams.length === 0) {
        return (
            <p className="flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
                <CheckCircle2 className="size-4 shrink-0" aria-hidden="true" />
                Nenhum exame parado há mais de {STALE_APPROVAL_HOURS} horas.
            </p>
        );
    }

    const columns: Column<StaleApproval>[] = [
        { header: 'Aluno', cell: (row) => row.student },
        { header: 'Exame', cell: (row) => row.exam_type },
        { header: 'Paciente', cell: (row) => row.patient },
        {
            header: 'Aguardando',
            cell: (row) => `${formatNumber(row.waiting_hours)} h`,
            className: 'tabular-nums',
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
            <p className="flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300">
                <AlertTriangle className="size-4 shrink-0" aria-hidden="true" />
                {exams.length === 1
                    ? `1 exame aguarda sua aprovação há mais de ${STALE_APPROVAL_HOURS} horas.`
                    : `${exams.length} exames aguardam sua aprovação há mais de ${STALE_APPROVAL_HOURS} horas.`}
            </p>
            <DataTable
                columns={columns}
                rows={exams}
                getRowKey={(row) => row.id}
            />
        </div>
    );
}

export function TeacherDashboard({
    data,
}: {
    data: TeacherDashboardData;
}): ReactElement {
    return (
        <div className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {data.stats.map((stat) => (
                    <StatTile key={stat.label} stat={stat} />
                ))}
            </div>

            <SectionCard
                title="Central de alertas"
                description="Exames enviados por alunos que estão parados na fila de aprovação."
            >
                <StaleApprovalsAlert exams={data.staleApprovals} />
            </SectionCard>

            <div className="grid gap-6 lg:grid-cols-2">
                <SectionCard
                    title="Status geral dos exames"
                    description="Todos os exames cadastrados no sistema."
                >
                    <BarList
                        items={data.statusDistribution.map((item) => ({
                            key: item.status,
                            label: item.label,
                            value: item.count,
                            status: item.status,
                        }))}
                        emptyMessage="Nenhum exame cadastrado."
                    />
                </SectionCard>

                <SectionCard
                    title="Volume de exames por mês"
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

                <SectionCard
                    title="Taxa de rejeição por tipo de exame"
                    description="Tipos com maior proporção de exames rejeitados."
                >
                    <BarList
                        items={data.rejectionRateByExamType.map((item) => ({
                            key: item.label,
                            label: item.label,
                            value: item.rate,
                            display: `${item.rate}% (${item.rejected} de ${item.total})`,
                            title: `${item.label}: ${item.rejected} rejeitados em ${item.total} exames`,
                        }))}
                        emptyMessage="Nenhum exame rejeitado até agora."
                    />
                </SectionCard>

                <SectionCard
                    title="Ranking de rejeições por aluno"
                    description="Alunos com mais exames rejeitados (acumulado)."
                >
                    <BarList
                        items={data.rejectionRanking.map((item) => ({
                            key: item.name,
                            label: item.name,
                            value: item.total,
                        }))}
                        emptyMessage="Nenhuma rejeição registrada."
                    />
                </SectionCard>
            </div>

            <SectionCard
                title="Alunos inativos"
                description={`Alunos ativos sem nenhum exame registrado nos últimos ${INACTIVE_STUDENT_DAYS} dias.`}
            >
                {data.inactiveStudents.length === 0 ? (
                    <p className="flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-400">
                        <CheckCircle2 className="size-4" aria-hidden="true" />
                        Todos os alunos registraram exames no período.
                    </p>
                ) : (
                    <ul className="flex flex-wrap gap-2">
                        {data.inactiveStudents.map((name) => (
                            <li
                                key={name}
                                className="rounded-md border px-3 py-1 text-sm"
                            >
                                {name}
                            </li>
                        ))}
                    </ul>
                )}
            </SectionCard>
        </div>
    );
}
