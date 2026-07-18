import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useDebouncedSearch } from '@/hooks/use-debounced-search';
import { cn } from '@/lib/utils';
import type { Paginated, PageProps } from '@/types';
import type { PatientHistoryRecord } from './types';

interface PatientHistoriesIndexProps extends Record<string, unknown> {
    patientHistories: Paginated<PatientHistoryRecord>;
    filters: { search: string; date: string };
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleDateString('pt-BR');
}

export default function PatientHistoriesIndex() {
    const { patientHistories, filters } = usePage<
        PageProps<PatientHistoriesIndexProps>
    >().props;

    const [date, setDate] = useState(filters.date ?? '');

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'patient-histories.index',
        initialValue: filters.search,
        extraParams: { date: date || undefined },
    });

    function handleDateChange(value: string): void {
        setDate(value);
        router.get(
            route('patient-histories.index'),
            { search: search || undefined, date: value || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function clearFilters(): void {
        reset();
        setDate('');
        router.get(
            route('patient-histories.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters = !!(search || date);

    const columns: Column<PatientHistoryRecord>[] = [
        {
            header: 'Paciente',
            cell: (row) => row.patient?.user?.name ?? '—',
        },
        {
            header: 'Profissional',
            cell: (row) => row.user?.name ?? '—',
            className: 'text-muted-foreground',
        },
        {
            header: 'Data da coleta',
            cell: (row) => formatDate(row.recorded_at),
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('patient-histories.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Ver detalhes"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('patient-histories.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                    <ConfirmDeleteDialog
                        action={route('patient-histories.destroy', row.id)}
                        title="Excluir anamnese"
                        description="Esta ação não pode ser desfeita."
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Anamneses"
            actions={
                <Button asChild>
                    <Link href={route('patient-histories.create')}>
                        <Plus />
                        Nova anamnese
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
                        placeholder="Buscar por paciente ou profissional..."
                        className="w-full"
                    />
                </div>
                <div className="space-y-1.5">
                    <Label htmlFor="date">Data da coleta</Label>
                    <Input
                        id="date"
                        type="date"
                        value={date}
                        onChange={(e) => handleDateChange(e.target.value)}
                        className="sm:w-48"
                    />
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={patientHistories.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhuma anamnese encontrada."
            />
            <TablePagination paginator={patientHistories} />
        </AppLayout>
    );
}
