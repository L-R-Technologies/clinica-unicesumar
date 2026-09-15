import { Link, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import type { ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { TablePagination } from '@/components/table-pagination';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useDebouncedSearch } from '@/hooks/use-debounced-search';
import { useTableFilters } from '@/hooks/use-table-filters';
import { formatDate } from '@/lib/format';
import { formatCpf, formatPhone } from '@/lib/masks';
import { cn } from '@/lib/utils';
import type { PageProps, Paginated, Patient } from '@/types';

const EMPTY_PLACEHOLDER = '—';

type PatientsIndexFilters = {
    search: string;
};

interface PatientsIndexProps extends Record<string, unknown> {
    patients: Paginated<Patient>;
    filters: PatientsIndexFilters;
}

export default function PatientsIndex(): ReactElement {
    const { patients, filters } =
        usePage<PageProps<PatientsIndexProps>>().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'patients.index',
        initialValue: filters.search,
    });

    const { clearFilters, hasActiveFilters } =
        useTableFilters<PatientsIndexFilters>({
            routeName: 'patients.index',
            filters,
            search,
            resetSearch: reset,
        });

    const columns: Column<Patient>[] = [
        {
            header: 'Nome',
            cell: (row) => (
                <div>
                    <p className="font-medium">
                        {row.user?.name ?? EMPTY_PLACEHOLDER}
                    </p>
                    <p className="text-sm text-muted-foreground">
                        {row.user?.email ?? EMPTY_PLACEHOLDER}
                    </p>
                </div>
            ),
        },
        {
            header: 'CPF',
            cell: (row) => (row.cpf ? formatCpf(row.cpf) : EMPTY_PLACEHOLDER),
        },
        {
            header: 'Telefone',
            cell: (row) =>
                row.phone ? formatPhone(row.phone) : EMPTY_PLACEHOLDER,
        },
        {
            header: 'Nascimento',
            cell: (row) => formatDate(row.birthday),
        },
        {
            header: 'Termo LGPD',
            cell: (row) =>
                row.lgpd_term_path ? (
                    <Badge variant="secondary">Anexado</Badge>
                ) : (
                    <Badge variant="destructive">Pendente</Badge>
                ),
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('patients.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('patients.edit', row.id)}
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
            title="Pacientes"
            actions={
                <Button asChild>
                    <Link href={route('patients.create')}>
                        <Plus />
                        Novo paciente
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
                        placeholder="Buscar por nome, e-mail ou CPF..."
                        className="w-full"
                    />
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={patients.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum paciente encontrado."
            />
            <TablePagination paginator={patients} />
        </AppLayout>
    );
}
