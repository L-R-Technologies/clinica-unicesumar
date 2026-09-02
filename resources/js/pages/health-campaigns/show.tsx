import { router, usePage } from '@inertiajs/react';
import { AlertTriangle, ChevronDown, Loader2, RefreshCw } from 'lucide-react';
import { useEffect, type ReactElement, type ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { StatusBadge } from '@/components/status-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { formatDate, formatDateTime } from '@/lib/format';
import type {
    HealthAnalysis,
    HealthAnalysisProblem,
    HealthCampaign,
    HealthCampaignPlan,
    PageProps,
} from '@/types';

const EMPTY_PLACEHOLDER = '—';
const POLL_INTERVAL_MS = 5000;

interface HealthCampaignsShowProps extends Record<string, unknown> {
    campaign: HealthCampaign;
    examTypeNames: string[];
}

function DetailItem({
    label,
    value,
}: {
    label: string;
    value: string;
}): ReactElement {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value || EMPTY_PLACEHOLDER}</p>
        </div>
    );
}

function Section({
    title,
    description,
    children,
}: {
    title: string;
    description?: string;
    children: ReactNode;
}): ReactElement {
    return (
        <Card>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                {description && (
                    <CardDescription>{description}</CardDescription>
                )}
            </CardHeader>
            <CardContent className="space-y-4">{children}</CardContent>
        </Card>
    );
}

function BulletList({ items }: { items: string[] }): ReactElement {
    if (items.length === 0) {
        return (
            <p className="text-sm text-muted-foreground">{EMPTY_PLACEHOLDER}</p>
        );
    }

    return (
        <ul className="list-disc space-y-1 pl-5 text-sm">
            {items.map((item, index) => (
                <li key={`${index}-${item}`}>{item}</li>
            ))}
        </ul>
    );
}

function Subsection({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}): ReactElement {
    return (
        <div className="space-y-2">
            <h3 className="text-sm font-semibold">{title}</h3>
            {children}
        </div>
    );
}

function ProcessingCard({
    campaign,
}: {
    campaign: HealthCampaign;
}): ReactElement {
    return (
        <Card>
            <CardContent className="flex items-center gap-4 py-8">
                <Loader2
                    className="size-6 shrink-0 animate-spin text-primary"
                    aria-hidden="true"
                />
                <div>
                    <p className="font-medium">
                        {campaign.status === 'processing'
                            ? 'A IA está analisando os exames...'
                            : 'Campanha na fila de geração...'}
                    </p>
                    <p className="text-sm text-muted-foreground">
                        São duas consultas à IA (análise e campanha). Esta
                        página atualiza sozinha quando o resultado ficar pronto.
                    </p>
                </div>
            </CardContent>
        </Card>
    );
}

function FailedCard({ campaign }: { campaign: HealthCampaign }): ReactElement {
    function retry(): void {
        router.post(
            route('health-campaigns.retry', campaign.id),
            {},
            { preserveScroll: true },
        );
    }

    return (
        <Card className="border-destructive/50">
            <CardContent className="space-y-4 py-6">
                <div className="flex items-start gap-3">
                    <AlertTriangle
                        className="mt-0.5 size-5 shrink-0 text-destructive"
                        aria-hidden="true"
                    />
                    <div>
                        <p className="font-medium text-destructive">
                            Não foi possível gerar a campanha.
                        </p>
                        <p className="text-sm text-muted-foreground">
                            {campaign.error_message ??
                                'Falha desconhecida ao consultar a IA.'}
                        </p>
                    </div>
                </div>
                <Button variant="outline" onClick={retry}>
                    <RefreshCw />
                    Tentar novamente
                </Button>
            </CardContent>
        </Card>
    );
}

function AnalysisSection({
    analysis,
}: {
    analysis: HealthAnalysis;
}): ReactElement {
    const problemColumns: Column<HealthAnalysisProblem>[] = [
        { header: 'Problema', cell: (row) => row.problem },
        {
            header: 'Pacientes',
            cell: (row) => row.affected_patients,
            className: 'tabular-nums',
        },
        { header: 'Evidência', cell: (row) => row.evidence },
    ];

    return (
        <Section
            title="Análise dos exames"
            description="Leitura clínica feita pela IA sobre os dados anonimizados."
        >
            <p className="text-sm">{analysis.summary}</p>

            <div className="rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950">
                <p className="text-xs font-semibold tracking-wide text-amber-800 uppercase dark:text-amber-300">
                    Principal problema de saúde
                </p>
                <p className="mt-1 text-lg font-semibold">
                    {analysis.main_problem.name}
                </p>
                <p className="mt-1 text-sm">
                    {analysis.main_problem.description}
                </p>
                <Badge variant="outline" className="mt-2">
                    {analysis.main_problem.affected_share}
                </Badge>
            </div>

            <div className="grid gap-4 sm:grid-cols-2">
                <Subsection title="Gravidade">
                    <p className="text-sm">{analysis.severity}</p>
                </Subsection>
                <Subsection title="Urgência">
                    <p className="text-sm">{analysis.urgency}</p>
                </Subsection>
            </div>

            <Subsection title="Problemas mais frequentes">
                <DataTable
                    columns={problemColumns}
                    rows={analysis.frequent_problems}
                    getRowKey={(row) => row.problem}
                    emptyMessage="Nenhum problema listado."
                />
            </Subsection>

            <Subsection title="Causas e fatores de risco">
                <BulletList items={analysis.risk_factors} />
            </Subsection>
        </Section>
    );
}

function CampaignSection({ plan }: { plan: HealthCampaignPlan }): ReactElement {
    return (
        <Section
            title="Campanha de saúde pública"
            description="Proposta gerada a partir da análise, pensada para recursos limitados."
        >
            <div className="grid gap-4 sm:grid-cols-2">
                <Subsection title="Objetivo">
                    <p className="text-sm">{plan.objective}</p>
                </Subsection>
                <Subsection title="Público-alvo">
                    <p className="text-sm">{plan.target_audience}</p>
                </Subsection>
            </div>

            <Subsection title="Ações práticas">
                <ol className="space-y-3">
                    {plan.actions.map((action, index) => (
                        <li
                            key={`${index}-${action.title}`}
                            className="rounded-md border p-3"
                        >
                            <p className="font-medium">
                                {index + 1}. {action.title}
                            </p>
                            <p className="mt-1 text-sm text-muted-foreground">
                                {action.description}
                            </p>
                        </li>
                    ))}
                </ol>
            </Subsection>

            <div className="grid gap-4 sm:grid-cols-2">
                <Subsection title="Materiais e recursos">
                    <BulletList items={plan.materials} />
                </Subsection>
                <Subsection title="Parceiros sugeridos">
                    <BulletList items={plan.partners} />
                </Subsection>
            </div>

            <Subsection title="Cronograma">
                <div className="grid gap-4 lg:grid-cols-3">
                    <Subsection title="Curto prazo">
                        <BulletList items={plan.timeline.short_term} />
                    </Subsection>
                    <Subsection title="Médio prazo">
                        <BulletList items={plan.timeline.medium_term} />
                    </Subsection>
                    <Subsection title="Longo prazo">
                        <BulletList items={plan.timeline.long_term} />
                    </Subsection>
                </div>
            </Subsection>

            <div className="grid gap-4 sm:grid-cols-2">
                <Subsection title="Indicadores de sucesso">
                    <BulletList items={plan.success_indicators} />
                </Subsection>
                <Subsection title="Mensagens-chave">
                    <BulletList items={plan.key_messages} />
                </Subsection>
            </div>
        </Section>
    );
}

export default function HealthCampaignsShow(): ReactElement {
    const { campaign, examTypeNames } =
        usePage<PageProps<HealthCampaignsShowProps>>().props;

    const isInProgress =
        campaign.status === 'pending' || campaign.status === 'processing';

    // Polling enquanto o job roda: recarrega só a prop da campanha.
    useEffect(() => {
        if (!isInProgress) {
            return;
        }

        const timer = setInterval(() => {
            router.reload({ only: ['campaign'] });
        }, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [isInProgress]);

    const period =
        campaign.date_from || campaign.date_to
            ? `${formatDate(campaign.date_from)} a ${formatDate(campaign.date_to)}`
            : 'Todo o histórico';

    return (
        <AppLayout
            title={campaign.campaign?.name ?? 'Campanha de Saúde'}
            actions={
                <>
                    <BackButton href={route('health-campaigns.index')} />
                    <ConfirmDeleteDialog
                        action={route('health-campaigns.destroy', campaign.id)}
                        title="Excluir campanha"
                        description="A campanha e a análise serão removidas permanentemente."
                        trigger={<Button variant="destructive">Excluir</Button>}
                    />
                </>
            }
        >
            <div className="space-y-6">
                <Card>
                    <CardContent className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Status
                            </p>
                            <StatusBadge status={campaign.status} />
                        </div>
                        <DetailItem label="Período dos exames" value={period} />
                        <DetailItem
                            label="Base analisada"
                            value={`${campaign.patients_count} pacientes · ${campaign.exams_count} exames`}
                        />
                        <DetailItem
                            label="Tipos de exame"
                            value={
                                examTypeNames.length > 0
                                    ? examTypeNames.join(', ')
                                    : 'Todos'
                            }
                        />
                        <DetailItem
                            label="Gerada por"
                            value={`${campaign.user?.name ?? EMPTY_PLACEHOLDER} em ${formatDateTime(campaign.created_at)}`}
                        />
                    </CardContent>
                </Card>

                {isInProgress && <ProcessingCard campaign={campaign} />}
                {campaign.status === 'failed' && (
                    <FailedCard campaign={campaign} />
                )}
                {campaign.analysis && (
                    <AnalysisSection analysis={campaign.analysis} />
                )}
                {campaign.campaign && (
                    <CampaignSection plan={campaign.campaign} />
                )}

                <Collapsible>
                    <Card>
                        <CardHeader>
                            <CollapsibleTrigger asChild>
                                <button
                                    type="button"
                                    className="flex w-full items-center justify-between text-left"
                                >
                                    <div>
                                        <CardTitle>
                                            Dados enviados à IA
                                        </CardTitle>
                                        <CardDescription>
                                            Conjunto anonimizado usado nesta
                                            análise
                                            {campaign.model
                                                ? ` (modelo ${campaign.model})`
                                                : ''}
                                            .
                                        </CardDescription>
                                    </div>
                                    <ChevronDown className="size-4 shrink-0 text-muted-foreground" />
                                </button>
                            </CollapsibleTrigger>
                        </CardHeader>
                        <CollapsibleContent>
                            <CardContent>
                                <pre className="max-h-96 overflow-auto rounded-md bg-muted p-4 text-xs whitespace-pre-wrap">
                                    {campaign.dataset}
                                </pre>
                            </CardContent>
                        </CollapsibleContent>
                    </Card>
                </Collapsible>
            </div>
        </AppLayout>
    );
}
