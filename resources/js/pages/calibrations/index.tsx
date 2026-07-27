import { Link, usePage } from '@inertiajs/react';
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
import { useTableFilters } from '@/hooks/use-table-filters';
import { ALL_FILTER_VALUE } from '@/lib/constants';
import { formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { Calibration, Machine, Paginated, PageProps } from '@/types';

type CalibrationsIndexFilters = {
    search: string;
    status: string;
    machine_id: string;
};

interface CalibrationsIndexProps extends Record<string, unknown> {
    calibrations: Paginated<Calibration>;
    machines: Machine[];
    filters: CalibrationsIndexFilters;
    statusOptions: Record<string, string>;
}

export default function CalibrationsIndex() {
    const { calibrations, machines, filters, statusOptions } =
        usePage<PageProps<CalibrationsIndexProps>>().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'calibrations.index',
        initialValue: filters.search,
        extraParams: {
            status: filters.status,
            machine_id: filters.machine_id,
        },
    });

    const { applyFilters, clearFilters, hasActiveFilters, buildParams } =
        useTableFilters<CalibrationsIndexFilters>({
            routeName: 'calibrations.index',
            filters,
            search,
            resetSearch: reset,
        });

    const exportHref = route('calibrations.export', buildParams());

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
        {
            header: 'Data',
            cell: (row) => formatDateTime(row.calibration_date),
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
                        value={filters.status || ALL_FILTER_VALUE}
                        onValueChange={(value) =>
                            applyFilters({
                                status: value === ALL_FILTER_VALUE ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="status" className="sm:w-44">
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
                        <SelectTrigger id="machine" className="sm:w-56">
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
