import { CheckCircle2 } from 'lucide-react';
import type { ReactElement } from 'react';
import {
    ApprovalQueue,
    CALIBRATION_DUE_DAYS,
    MachinesPanel,
    SamplesPanel,
} from './teacher-panels';
import type { TeacherDashboardData } from './types';
import { BarList, SectionCard, StatTile } from './widgets';

const INACTIVE_STUDENT_DAYS = 30;

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
                title="Fila de aprovação"
                description="Exames enviados por alunos, dos mais antigos para os mais recentes."
            >
                <ApprovalQueue
                    exams={data.approvalQueue}
                    staleCount={data.staleApprovalsCount}
                />
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
                    title="Exames mais realizados"
                    description="Tipos de exame com maior volume acumulado."
                >
                    <BarList
                        items={data.topExamTypes.map((item) => ({
                            key: item.label,
                            label: item.label,
                            value: item.total,
                        }))}
                        emptyMessage="Nenhum exame cadastrado."
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
                    title="Alunos mais ativos"
                    description={`Exames registrados nos últimos ${INACTIVE_STUDENT_DAYS} dias.`}
                >
                    <BarList
                        items={data.activeStudents.map((item) => ({
                            key: item.name,
                            label: item.name,
                            value: item.total,
                        }))}
                        emptyMessage="Nenhum exame registrado no período."
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

                <SectionCard
                    title="Amostras"
                    description="Situação das amostras coletadas e alertas de freezer."
                >
                    <SamplesPanel samples={data.samples} />
                </SectionCard>

                <SectionCard
                    title="Equipamentos"
                    description={`Última calibração de cada equipamento. Ativos sem calibração há ${CALIBRATION_DUE_DAYS} dias aparecem primeiro.`}
                >
                    <MachinesPanel machines={data.machines} />
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
