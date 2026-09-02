import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Sparkles } from 'lucide-react';
import { useEffect } from 'react';
import type { ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
import { formatDate, formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { HealthCampaign, PageProps, Paginated } from '@/types';

interface HealthCampaignsIndexProps extends Record<string, unknown> {
    campaigns: Paginated<HealthCampaign>;
}

function formatPeriod(campaign: HealthCampaign): string {
    if (!campaign.date_from && !campaign.date_to) {
        return 'Todo o histórico';
    }

    return `${formatDate(campaign.date_from)} a ${formatDate(campaign.date_to)}`;
}

const POLL_INTERVAL_MS = 5000;

export default function HealthCampaignsIndex(): ReactElement {
    const { campaigns } = usePage<PageProps<HealthCampaignsIndexProps>>().props;

    const hasCampaignInProgress = campaigns.data.some(
        (campaign) =>
            campaign.status === 'pending' || campaign.status === 'processing',
    );

    // Enquanto houver campanha na fila, recarrega a lista para refletir o job.
    useEffect(() => {
        if (!hasCampaignInProgress) {
            return;
        }

        const timer = setInterval(() => {
            router.reload({ only: ['campaigns'] });
        }, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [hasCampaignInProgress]);

    const columns: Column<HealthCampaign>[] = [
        {
            header: 'Campanha',
            cell: (row) => (
                <div>
                    <p className="font-medium">
                        {row.campaign?.name ?? 'Campanha em geração'}
                    </p>
                    <p className="text-sm text-muted-foreground">
                        {row.analysis?.main_problem.name ?? formatPeriod(row)}
                    </p>
                </div>
            ),
        },
        {
            header: 'Status',
            cell: (row) => <StatusBadge status={row.status} />,
        },
        { header: 'Período dos exames', cell: (row) => formatPeriod(row) },
        {
            header: 'Base',
            cell: (row) =>
                `${row.patients_count} pacientes · ${row.exams_count} exames`,
        },
        { header: 'Gerada por', cell: (row) => row.user?.name ?? '—' },
        { header: 'Em', cell: (row) => formatDateTime(row.created_at) },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('health-campaigns.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <ConfirmDeleteDialog
                        action={route('health-campaigns.destroy', row.id)}
                        description="A campanha e a análise serão removidas permanentemente."
                    />
                </div>
            ),
        },
    ];

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
            <DataTable
                columns={columns}
                rows={campaigns.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhuma campanha gerada ainda."
            />
            <TablePagination paginator={campaigns} />
        </AppLayout>
    );
}
