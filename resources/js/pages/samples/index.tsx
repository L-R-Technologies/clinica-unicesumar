import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
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
import type { Paginated, PageProps, Sample } from '@/types';

const ALL_STATUS = 'all';

interface SamplesIndexFilters {
    search: string;
    status: string;
    date: string;
}

interface SamplesIndexProps extends Record<string, unknown> {
    samples: Paginated<Sample>;
    filters: SamplesIndexFilters;
    statusOptions: Record<string, string>;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

export default function SamplesIndex() {
    const { samples, filters, statusOptions } = usePage<
        PageProps<SamplesIndexProps>
    >().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'samples.index',
        initialValue: filters.search,
        extraParams: { status: filters.status, date: filters.date },
    });

    function applyFilters(next: Partial<SamplesIndexFilters>): void {
        const params = {
            search,
            status: filters.status,
            date: filters.date,
            ...next,
        };

        router.get(
            route('samples.index'),
            Object.fromEntries(
                Object.entries(params).filter(([, value]) => value !== ''),
            ),
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function clearFilters(): void {
        reset();
        router.get(
            route('samples.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters = !!(search || filters.status || filters.date);

    const columns: Column<Sample>[] = [
        {
            header: 'Código',
            cell: (row) => <span className="font-mono">{row.code}</span>,
        },
        {
            header: 'Paciente',
            cell: (row) => row.patient?.user?.name ?? 'N/A',
        },
        {
            header: 'Registrado por',
            cell: (row) => row.user?.name ?? 'N/A',
        },
        {
            header: 'Tipo',
            cell: (row) => row.sample_type?.name ?? 'N/A',
        },
        {
            header: 'Data',
            cell: (row) => formatDate(row.date),
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
                        href={route('samples.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('samples.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                    <ConfirmDeleteDialog
                        action={route('samples.destroy', row.id)}
                        description="Esta ação não pode ser desfeita e removerá a amostra permanentemente."
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Amostras"
            actions={
                <Button asChild>
                    <Link href={route('samples.create')}>
                        <Plus />
                        Nova amostra
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
                        placeholder="Buscar por código, tipo, paciente ou responsável..."
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
                        <SelectTrigger id="status" className="sm:w-48">
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
                    <Label htmlFor="date">Data da coleta</Label>
                    <Input
                        id="date"
                        type="date"
                        value={filters.date}
                        onChange={(e) => applyFilters({ date: e.target.value })}
                    />
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={samples.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhuma amostra encontrada."
            />
            <TablePagination paginator={samples} />
        </AppLayout>
    );
}
