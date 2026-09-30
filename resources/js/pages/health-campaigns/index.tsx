import { Link, router, usePage } from '@inertiajs/react';
import {
    CalendarRange,
    Clock,
    Eye,
    Sparkles,
    UserRound,
    Users,
} from 'lucide-react';
import { useEffect } from 'react';
import type { ReactElement, ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatDate, formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { HealthCampaign, PageProps, Paginated } from '@/types';

interface HealthCampaignsIndexProps extends Record<string, unknown> {
    campaigns: Paginated<HealthCampaign>;
}

const POLL_INTERVAL_MS = 5000;
const CAMPAIGN_NAME_PLACEHOLDER = 'Campanha em geração';
const EMPTY_VALUE = '—';

function formatPeriod(campaign: HealthCampaign): string {
    if (!campaign.date_from && !campaign.date_to) {
        return 'Todo o histórico';
    }

    return `${formatDate(campaign.date_from)} a ${formatDate(campaign.date_to)}`;
}

function describeCampaign(campaign: HealthCampaign): string {
    if (campaign.status === 'failed') {
        return campaign.error_message ?? 'A geração falhou.';
    }

    if (campaign.analysis) {
        return `Problema principal: ${campaign.analysis.main_problem.name}`;
    }

    return 'A IA ainda está analisando os exames deste recorte.';
}

function CampaignDetail({
    icon,
    label,
    children,
}: {
    icon: ReactNode;
    label: string;
    children: ReactNode;
}): ReactElement {
    return (
        <div className="flex items-start gap-2">
            <span
                className="mt-0.5 shrink-0 text-muted-foreground"
                aria-hidden="true"
            >
                {icon}
            </span>
            <div className="min-w-0">
                <dt className="text-xs text-muted-foreground">{label}</dt>
                <dd className="truncate text-sm">{children}</dd>
            </div>
        </div>
    );
}

function CampaignCard({
    campaign,
}: {
    campaign: HealthCampaign;
}): ReactElement {
    return (
        <Card className="h-full gap-4 py-5">
            <CardHeader className="gap-1 px-5">
                <div className="flex items-start justify-between gap-3">
                    <CardTitle className="leading-snug">
                        {campaign.campaign?.name ?? CAMPAIGN_NAME_PLACEHOLDER}
                    </CardTitle>
                    <StatusBadge status={campaign.status} />
                </div>
                <CardDescription className="line-clamp-2">
                    {describeCampaign(campaign)}
                </CardDescription>
            </CardHeader>

            <CardContent className="flex-1 px-5">
                {campaign.campaign?.objective && (
                    <p className="mb-4 line-clamp-3 text-sm text-muted-foreground">
                        {campaign.campaign.objective}
                    </p>
                )}
                <dl className="grid gap-3 sm:grid-cols-2">
                    <CampaignDetail
                        icon={<CalendarRange className="size-4" />}
                        label="Período dos exames"
                    >
                        {formatPeriod(campaign)}
                    </CampaignDetail>
                    <CampaignDetail
                        icon={<Users className="size-4" />}
                        label="Base analisada"
                    >
                        {campaign.patients_count} pacientes ·{' '}
                        {campaign.exams_count} exames
                    </CampaignDetail>
                    <CampaignDetail
                        icon={<UserRound className="size-4" />}
                        label="Gerada por"
                    >
                        {campaign.user?.name ?? EMPTY_VALUE}
                    </CampaignDetail>
                    <CampaignDetail
                        icon={<Clock className="size-4" />}
                        label="Em"
                    >
                        {formatDateTime(campaign.created_at)}
                    </CampaignDetail>
                </dl>
            </CardContent>

            <CardFooter className="justify-end gap-2 px-5">
                <ConfirmDeleteDialog
                    action={route('health-campaigns.destroy', campaign.id)}
                    description="A campanha e a análise serão removidas permanentemente."
                />
                <Link
                    href={route('health-campaigns.show', campaign.id)}
                    className={cn(
                        buttonVariants({ variant: 'outline', size: 'sm' }),
                    )}
                >
                    <Eye className="size-4" />
                    Ver campanha
                </Link>
            </CardFooter>
        </Card>
    );
}

export default function HealthCampaignsIndex(): ReactElement {
    const { campaigns } = usePage<PageProps<HealthCampaignsIndexProps>>().props;

    const hasCampaignInProgress = campaigns.data.some(
        (campaign) =>
            campaign.status === 'pending' || campaign.status === 'processing',
    );

    useEffect(() => {
        if (!hasCampaignInProgress) {
            return;
        }

        const timer = setInterval(() => {
            router.reload({ only: ['campaigns'] });
        }, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [hasCampaignInProgress]);

    return (
        <AppLayout
            title="Campanhas de Saúde"
            actions={
                <Button asChild>
                    <Link href={route('health-campaigns.create')}>
                        <Sparkles />
                        Gerar campanha
                    </Link>
                </Button>
            }
        >
            <p className="text-sm text-muted-foreground">
                Campanhas geradas por IA a partir dos resultados de exames
                aprovados, sempre com dados anonimizados.
            </p>

            {campaigns.data.length === 0 ? (
                <div className="rounded-md border bg-card p-6 text-center text-sm text-muted-foreground">
                    Nenhuma campanha gerada ainda.
                </div>
            ) : (
                <div className="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                    {campaigns.data.map((campaign) => (
                        <CampaignCard key={campaign.id} campaign={campaign} />
                    ))}
                </div>
            )}

            <TablePagination paginator={campaigns} />
        </AppLayout>
    );
}
