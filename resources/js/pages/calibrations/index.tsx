import { Link, router, usePage } from '@inertiajs/react';
import { Download, Eye, Pencil } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
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
import type { Calibration, Machine, Paginated, PageProps } from '@/types';

const ALL_STATUS = 'all';
const ALL_MACHINES = 'all';

interface CalibrationsIndexFilters {
    search: string;
    status: string;
    machine_id: string;
}

interface CalibrationsIndexProps extends Record<string, unknown> {
    calibrations: Paginated<Calibration>;
    machines: Machine[];
    filters: CalibrationsIndexFilters;
    statusOptions: Record<string, string>;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    const time = isoDate.slice(11, 16);
    return `${day}/${month}/${year} ${time}`;
}

function cleanParams(
    params: Record<string, string>,
): Record<string, string> {
    return Object.fromEntries(
        Object.entries(params).filter(([, value]) => value !== ''),
    );
}

export default function CalibrationsIndex() {
    const { calibrations, machines, filters, statusOptions } = usePage<
        PageProps<CalibrationsIndexProps>
    >().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'calibrations.index',
        initialValue: filters.search,
        extraParams: {
            status: filters.status,
            machine_id: filters.machine_id,
        },
    });

    function applyFilters(next: Partial<CalibrationsIndexFilters>): void {
        const params = cleanParams({
            search,
            status: filters.status,
            machine_id: filters.machine_id,
            ...next,
        });

        router.get(route('calibrations.index'), params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    function clearFilters(): void {
        reset();
        router.get(
            route('calibrations.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters = !!(
        search ||
        filters.status ||
        filters.machine_id
    );

    const exportHref = route(
        'calibrations.export',
        cleanParams({
            search,
            status: filters.status,
            machine_id: filters.machine_id,
        }),
    );

    const columns: Column<Calibration>[] = [
        {
            header: 'Máquina / Série',
            cell: (row) => (
                <div>
                    <div className="font-medium">
                        {row.machine?.name ?? 'N/A'}
                    </div>
                    <div className="font-mono text-xs text-muted-foreground">
                        {row.machine?.serial_number ?? '—'}
                    </div>
                </div>
            ),
        },
        { header: 'Data', cell: (row) => formatDate(row.calibration_date) },
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
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('calibrations.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Gerenciamento de Calibrações"
            actions={
                <Button variant="outline" asChild>
                    <a href={exportHref}>
                        <Download />
                        Exportar CSV
                    </a>
                </Button>
            }
        >
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
                        placeholder="Buscar por nome do equipamento..."
                        className="w-full"
                    />
                </div>
                <div className="space-y-1.5">
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={filters.status || ALL_STATUS}
                        onValueChange={(value) =>
                            applyFilters({
                                status: value === ALL_STATUS ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="status" className="sm:w-44">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_STATUS}>Todos</SelectItem>
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
                        value={filters.machine_id || ALL_MACHINES}
                        onValueChange={(value) =>
                            applyFilters({
                                machine_id:
                                    value === ALL_MACHINES ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="machine" className="sm:w-56">
                            <SelectValue placeholder="Todas" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_MACHINES}>Todas</SelectItem>
                            {machines.map((machine) => (
                                <SelectItem
                                    key={machine.id}
                                    value={String(machine.id)}
                                >
                                    {machine.name} ({machine.serial_number})
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={calibrations.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhuma calibração encontrada."
            />
            <TablePagination paginator={calibrations} />
        </AppLayout>
    );
}
