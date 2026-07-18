import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { SearchInput } from '@/components/search-input';
import { StatusBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Button, buttonVariants } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useDebouncedSearch } from '@/hooks/use-debounced-search';
import { cn } from '@/lib/utils';
import type { Exam, ExamType, Paginated, PageProps } from '@/types';

// Valor sentinela para o item "Todos" (o Select do shadcn não aceita value vazio).
const ALL_OPTION = 'all';

interface ExamFilters {
    search: string;
    status: string;
    exam_type_id: string;
    date_from: string;
    date_to: string;
}

interface ExamsIndexProps extends Record<string, unknown> {
    exams: Paginated<Exam>;
    filters: ExamFilters;
    statusOptions: Record<string, string>;
    examTypes: ExamType[];
}

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
}

export default function ExamsIndex() {
    const { exams, filters, statusOptions, examTypes } =
        usePage<PageProps<ExamsIndexProps>>().props;

    const otherFilters = {
        status: filters.status,
        exam_type_id: filters.exam_type_id,
        date_from: filters.date_from,
        date_to: filters.date_to,
    };

    const { search, setSearch } = useDebouncedSearch({
        routeName: 'exam.index',
        initialValue: filters.search,
        extraParams: otherFilters,
    });

    function applyFilter(key: keyof ExamFilters, value: string): void {
        const params: Record<string, string> = {
            search,
            ...otherFilters,
            [key]: value,
        };
        const cleaned = Object.fromEntries(
            Object.entries(params).filter(([, v]) => v !== ''),
        );
        router.get(route('exam.index'), cleaned, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    const columns: Column<Exam>[] = [
        {
            header: 'Paciente',
            cell: (row) => row.patient?.user?.name ?? '—',
        },
        {
            header: 'Responsável',
            cell: (row) => row.user?.name ?? '—',
            className: 'text-muted-foreground',
        },
        {
            header: 'Tipo',
            cell: (row) => row.exam_type?.name ?? '—',
        },
        {
            header: 'Amostra',
            cell: (row) => row.sample?.code ?? '—',
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
                        href={route('exam.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('exam.edit', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Editar"
                    >
                        <Pencil className="size-4" />
                    </Link>
                    <ConfirmDeleteDialog
                        action={route('exam.destroy', row.id)}
                        description="O exame será removido permanentemente."
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Exames"
            actions={
                <Button asChild>
                    <Link href={route('exam.create')}>
                        <Plus />
                        Novo exame
                    </Link>
                </Button>
            }
        >
            <div className="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div className="space-y-1 sm:col-span-2 lg:col-span-1">
                    <Label htmlFor="search">Buscar</Label>
                    <SearchInput
                        id="search"
                        value={search}
                        onChange={setSearch}
                        placeholder="Tipo, paciente ou responsável..."
                        className="max-w-none"
                    />
                </div>

                <div className="space-y-1">
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={filters.status || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilter(
                                'status',
                                value === ALL_OPTION ? '' : value,
                            )
                        }
                    >
                        <SelectTrigger id="status" className="w-full">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_OPTION}>Todos</SelectItem>
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

                <div className="space-y-1">
                    <Label htmlFor="type">Tipo</Label>
                    <Select
                        value={filters.exam_type_id || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilter(
                                'exam_type_id',
                                value === ALL_OPTION ? '' : value,
                            )
                        }
                    >
                        <SelectTrigger id="type" className="w-full">
                            <SelectValue placeholder="Todos os tipos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_OPTION}>
                                Todos os tipos
                            </SelectItem>
                            {examTypes.map((examType) => (
                                <SelectItem
                                    key={examType.id}
                                    value={String(examType.id)}
                                >
                                    {examType.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-1">
                    <Label htmlFor="date_from">Data de</Label>
                    <Input
                        id="date_from"
                        type="date"
                        value={filters.date_from}
                        onChange={(e) =>
                            applyFilter('date_from', e.target.value)
                        }
                    />
                </div>

                <div className="space-y-1">
                    <Label htmlFor="date_to">Data até</Label>
                    <Input
                        id="date_to"
                        type="date"
                        value={filters.date_to}
                        onChange={(e) => applyFilter('date_to', e.target.value)}
                    />
                </div>
            </div>

            <DataTable
                columns={columns}
                rows={exams.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum exame encontrado com os filtros aplicados."
            />
            <TablePagination paginator={exams} />
        </AppLayout>
    );
}
