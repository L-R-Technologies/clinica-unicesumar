import { Link, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
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
import { cn } from '@/lib/utils';
import type { Machine, Paginated, PageProps } from '@/types';

type MachinesIndexFilters = {
    search: string;
    status: string;
};

interface MachinesIndexProps extends Record<string, unknown> {
    machines: Paginated<Machine>;
    filters: MachinesIndexFilters;
    statusOptions: Record<string, string>;
}

export default function MachinesIndex() {
    const { machines, filters, statusOptions } =
        usePage<PageProps<MachinesIndexProps>>().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'machines.index',
        initialValue: filters.search,
        extraParams: { status: filters.status },
    });

    const { applyFilters, clearFilters, hasActiveFilters } =
        useTableFilters<MachinesIndexFilters>({
            routeName: 'machines.index',
            filters,
            search,
            resetSearch: reset,
        });

    const columns: Column<Machine>[] = [
        {
            header: 'Nome',
            cell: (row) => <span className="font-medium">{row.name}</span>,
        },
        { header: 'Modelo', cell: (row) => row.model || '—' },
        {
            header: 'Nº Série',
            cell: (row) => (
                <span className="font-mono">{row.serial_number || '—'}</span>
            ),
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
                        href={route('machines.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Ver prontuário"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('machines.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                    <ConfirmDeleteDialog
                        action={route('machines.destroy', row.id)}
                        description="Isso excluirá o equipamento permanentemente. Máquinas com calibrações registradas não podem ser removidas."
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Equipamentos Laboratoriais"
            actions={
                <Button asChild>
                    <Link href={route('machines.create')}>
                        <Plus />
                        Nova máquina
                    </Link>
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
                        placeholder="Buscar por nome, modelo ou série..."
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
                        <SelectTrigger id="status" className="sm:w-48">
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
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={machines.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum equipamento encontrado."
            />
            <TablePagination paginator={machines} />
        </AppLayout>
    );
}
