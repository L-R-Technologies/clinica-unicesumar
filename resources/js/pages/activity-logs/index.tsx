import { Link, router, usePage } from '@inertiajs/react';
import { Eye } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { SearchInput } from '@/components/search-input';
import { TablePagination } from '@/components/table-pagination';
import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useDebouncedSearch } from '@/hooks/use-debounced-search';
import { cn } from '@/lib/utils';
import type { Paginated, PageProps } from '@/types';

const ALL_OPTION = 'all';

const EVENT_BADGE_CLASSES: Record<string, string> = {
    created:
        'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
    updated:
        'border-transparent bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300',
    deleted:
        'border-transparent bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300',
};

interface FilterOption {
    value: string;
    label: string;
}

interface ActivityLogSummary {
    id: number;
    log_name: string | null;
    log_name_label: string;
    event: string | null;
    event_label: string;
    causer: { name: string; email: string } | null;
    subject_type_label: string | null;
    subject_id: number | null;
    created_at: string | null;
}

interface ActivityLogsFilters {
    search: string;
    log_name: string;
    event: string;
    date_from: string;
    date_to: string;
}

interface ActivityLogsIndexProps extends Record<string, unknown> {
    logs: Paginated<ActivityLogSummary>;
    filters: ActivityLogsFilters;
    logNameOptions: FilterOption[];
    eventOptions: FilterOption[];
}

function formatDateTime(isoDate: string | null): string {
    if (!isoDate) {
        return '—';
    }

    const date = new Date(isoDate);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function EventBadge({
    event,
    label,
}: {
    event: string | null;
    label: string;
}) {
    const className =
        (event && EVENT_BADGE_CLASSES[event]) ??
        'border-transparent bg-muted text-muted-foreground';

    return <Badge className={cn('font-medium', className)}>{label}</Badge>;
}

export default function ActivityLogsIndex() {
    const { logs, filters, logNameOptions, eventOptions } = usePage<
        PageProps<ActivityLogsIndexProps>
    >().props;

    const { search, setSearch } = useDebouncedSearch({
        routeName: 'activity-logs.index',
        initialValue: filters.search,
        extraParams: {
            log_name: filters.log_name,
            event: filters.event,
            date_from: filters.date_from,
            date_to: filters.date_to,
        },
    });

    function applyFilters(next: Partial<ActivityLogsFilters>): void {
        const params = {
            search,
            log_name: filters.log_name,
            event: filters.event,
            date_from: filters.date_from,
            date_to: filters.date_to,
            ...next,
        };

        router.get(
            route('activity-logs.index'),
            Object.fromEntries(
                Object.entries(params).filter(([, value]) => value !== ''),
            ),
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const columns: Column<ActivityLogSummary>[] = [
        {
            header: 'Data/Hora',
            cell: (row) => formatDateTime(row.created_at),
        },
        {
            header: 'Tipo',
            cell: (row) => (
                <Badge variant="secondary">{row.log_name_label}</Badge>
            ),
        },
        {
            header: 'Evento',
            cell: (row) => (
                <EventBadge event={row.event} label={row.event_label} />
            ),
        },
        {
            header: 'Usuário',
            cell: (row) =>
                row.causer ? (
                    <div className="flex flex-col">
                        <span>{row.causer.name}</span>
                        <span className="text-sm text-muted-foreground">
                            {row.causer.email}
                        </span>
                    </div>
                ) : (
                    <span className="text-muted-foreground">Sistema</span>
                ),
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <Link
                    href={route('activity-logs.show', row.id)}
                    className={cn(
                        buttonVariants({ variant: 'ghost', size: 'icon' }),
                    )}
                    aria-label="Ver detalhes"
                >
                    <Eye className="size-4" />
                </Link>
            ),
        },
    ];

    return (
        <AppLayout title="Histórico de Atividades">
            <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                <div className="flex-1">
                    <SearchInput
                        value={search}
                        onChange={setSearch}
                        placeholder="Buscar por usuário ou objeto..."
                    />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="log_name">Tipo</Label>
                    <Select
                        value={filters.log_name || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                log_name: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="log_name" className="sm:w-44">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_OPTION}>Todos</SelectItem>
                            {logNameOptions.map((option) => (
                                <SelectItem
                                    key={option.value}
                                    value={option.value}
                                >
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
                <div className="space-y-2">
                    <Label htmlFor="event">Evento</Label>
                    <Select
                        value={filters.event || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                event: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="event" className="sm:w-44">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_OPTION}>Todos</SelectItem>
                            {eventOptions.map((option) => (
                                <SelectItem
                                    key={option.value}
                                    value={option.value}
                                >
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
                <div className="space-y-2">
                    <Label htmlFor="date_from">Data início</Label>
                    <Input
                        id="date_from"
                        type="date"
                        value={filters.date_from}
                        onChange={(e) =>
                            applyFilters({ date_from: e.target.value })
                        }
                    />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="date_to">Data fim</Label>
                    <Input
                        id="date_to"
                        type="date"
                        value={filters.date_to}
                        onChange={(e) =>
                            applyFilters({ date_to: e.target.value })
                        }
                    />
                </div>
            </div>
            <DataTable
                columns={columns}
                rows={logs.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum registro encontrado."
            />
            <TablePagination paginator={logs} />
        </AppLayout>
    );
}
