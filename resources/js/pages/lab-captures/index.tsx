import { Link, router, usePage } from '@inertiajs/react';
import { Eye } from 'lucide-react';
import { useEffect, type ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
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
import { useTableFilters } from '@/hooks/use-table-filters';
import { ALL_FILTER_VALUE } from '@/lib/constants';
import { todayIsoDate } from '@/lib/date';
import { formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { LabCapture, Machine, PageProps, Paginated } from '@/types';

const POLL_INTERVAL_MS = 5000;
const EMPTY_PLACEHOLDER = '—';
const PREVIEW_MAX_CHARS = 60;

type CaptureFilters = {
    search: string;
    status: string;
    machine_id: string;
    date_from: string;
    date_to: string;
};

interface LabCapturesIndexProps extends Record<string, unknown> {
    captures: Paginated<LabCapture>;
    filters: CaptureFilters;
    statusOptions: Record<string, string>;
    machines: Pick<Machine, 'id' | 'name'>[];
}

function describeCapture(capture: LabCapture): string {
    const examLine = capture.raw_text
        .split('\n')
        .find((line) => /exame\s*:/i.test(line));
    const preview = (examLine ?? capture.raw_text).replace(/\s+/g, ' ').trim();

    return preview.length > PREVIEW_MAX_CHARS
        ? `${preview.slice(0, PREVIEW_MAX_CHARS)}…`
        : preview || EMPTY_PLACEHOLDER;
}

function describeSuggestion(capture: LabCapture): string {
    if (capture.exam?.exam_type) {
        return capture.exam.exam_type.name;
    }

    return capture.analysis?.suggested_exam_type_name ?? EMPTY_PLACEHOLDER;
}

export default function LabCapturesIndex(): ReactElement {
    const { captures, filters, statusOptions, machines } =
        usePage<PageProps<LabCapturesIndexProps>>().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'lab-captures.index',
        initialValue: filters.search,
        extraParams: {
            status: filters.status,
            machine_id: filters.machine_id,
            date_from: filters.date_from,
            date_to: filters.date_to,
        },
    });

    const { applyFilters, clearFilters, hasActiveFilters } =
        useTableFilters<CaptureFilters>({
            routeName: 'lab-captures.index',
            filters,
            search,
            resetSearch: reset,
        });

    const hasCaptureInProgress = captures.data.some(
        (capture) =>
            capture.status === 'received' || capture.status === 'analyzing',
    );

    useEffect(() => {
        if (!hasCaptureInProgress) {
            return;
        }

        const timer = setInterval(() => {
            router.reload({ only: ['captures'] });
        }, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [hasCaptureInProgress]);

    const columns: Column<LabCapture>[] = [
        {
            header: 'Capturado em',
            cell: (row) => formatDateTime(row.captured_at),
        },
        {
            header: 'Máquina',
            cell: (row) => row.machine?.name ?? EMPTY_PLACEHOLDER,
        },
        {
            header: 'Prévia',
            cell: (row) => (
                <span className="font-mono text-xs">
                    {describeCapture(row)}
                </span>
            ),
        },
        {
            header: 'Tipo de exame',
            cell: (row) => describeSuggestion(row),
        },
        {
            header: 'Paciente',
            cell: (row) => row.exam?.patient?.user?.name ?? EMPTY_PLACEHOLDER,
            className: 'text-muted-foreground',
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
                        href={route('lab-captures.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Conferir captura"
                    >
                        <Eye className="size-4" />
                    </Link>
                </div>
            ),
        },
    ];

    return (
        <AppLayout title="Capturas do Laboratório">
            <p className="text-sm text-muted-foreground">
                Resultados enviados automaticamente pelas máquinas do
                laboratório. A IA sugere o tipo de exame e os campos; confira e
                vincule cada captura ao exame correspondente.
            </p>

            <FiltersCard
                onClear={clearFilters}
                hasActiveFilters={hasActiveFilters}
            >
                <div className="min-w-56 flex-1 space-y-1.5">
                    <Label htmlFor="search">Buscar</Label>
                    <SearchInput
                        id="search"
                        value={search}
                        onChange={setSearch}
                        placeholder="Texto da captura, máquina ou paciente..."
                        className="w-full"
                    />
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={filters.status || ALL_FILTER_VALUE}
                        onValueChange={(value) =>
                            applyFilters({
                                status: value === ALL_FILTER_VALUE ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="status" className="sm:w-52">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_FILTER_VALUE}>
                                Todos
                            </SelectItem>
                            {Object.entries(statusOptions).map(
                                ([value, label]) => (
                                    <SelectItem key={value} value={value}>
                                        {label}
                                    </SelectItem>
                                ),
                            )}
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="machine">Máquina</Label>
                    <Select
                        value={filters.machine_id || ALL_FILTER_VALUE}
                        onValueChange={(value) =>
                            applyFilters({
                                machine_id:
                                    value === ALL_FILTER_VALUE ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="machine" className="sm:w-48">
                            <SelectValue placeholder="Todas" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_FILTER_VALUE}>
                                Todas
                            </SelectItem>
                            {machines.map((machine) => (
                                <SelectItem
                                    key={machine.id}
                                    value={String(machine.id)}
                                >
                                    {machine.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="date_from">Data de</Label>
                    <Input
                        id="date_from"
                        type="date"
                        max={todayIsoDate()}
                        value={filters.date_from}
                        onChange={(e) =>
                            applyFilters({ date_from: e.target.value })
                        }
                    />
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="date_to">Data até</Label>
                    <Input
                        id="date_to"
                        type="date"
                        max={todayIsoDate()}
                        value={filters.date_to}
                        onChange={(e) =>
                            applyFilters({ date_to: e.target.value })
                        }
                    />
                </div>
            </FiltersCard>

            <DataTable
                columns={columns}
                rows={captures.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhuma captura recebida com os filtros aplicados."
            />
            <TablePagination paginator={captures} />
        </AppLayout>
    );
}
