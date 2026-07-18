import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus, Power } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { ActiveBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useDebouncedSearch } from '@/hooks/use-debounced-search';
import { cn } from '@/lib/utils';
import type { ExamType, Paginated, PageProps } from '@/types';

interface ExamTypesIndexProps extends Record<string, unknown> {
    examTypes: Paginated<ExamType>;
    filters: { search: string };
}

export default function ExamTypesIndex() {
    const { examTypes, filters } = usePage<
        PageProps<ExamTypesIndexProps>
    >().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'exam-type.index',
        initialValue: filters.search,
    });

    function clearFilters(): void {
        reset();
        router.get(
            route('exam-type.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const columns: Column<ExamType>[] = [
        { header: 'Nome', cell: (row) => row.name },
        {
            header: 'Descrição',
            cell: (row) => row.description || '—',
            className: 'text-muted-foreground',
        },
        {
            header: 'Campos',
            cell: (row) => row.fields?.length ?? 0,
        },
        {
            header: 'Status',
            cell: (row) => <ActiveBadge active={row.is_active} />,
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('exam-type.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('exam-type.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label={row.is_active ? 'Desativar' : 'Ativar'}
                        onClick={() =>
                            router.patch(
                                route('exam-type.toggle-status', row.id),
                                {},
                                { preserveScroll: true },
                            )
                        }
                    >
                        <Power className="size-4" />
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Tipos de Exame"
            actions={
                <Button asChild>
                    <Link href={route('exam-type.create')}>
                        <Plus />
                        Novo tipo
                    </Link>
                </Button>
            }
        >
            <FiltersCard onClear={clearFilters} hasActiveFilters={!!search}>
                <div className="min-w-56 flex-1 space-y-1.5">
                    <Label htmlFor="search">Buscar</Label>
                    <SearchInput
                        id="search"
                        value={search}
                        onChange={setSearch}
                        placeholder="Buscar por nome ou descrição..."
                        className="w-full"
                    />
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={examTypes.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum tipo de exame encontrado."
            />
            <TablePagination paginator={examTypes} />
        </AppLayout>
    );
}
