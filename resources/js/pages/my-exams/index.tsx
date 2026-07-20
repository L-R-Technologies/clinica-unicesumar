import { Link, router, usePage } from '@inertiajs/react';
import { Eye, FileText } from 'lucide-react';
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
import { cn } from '@/lib/utils';
import type { Exam, ExamType, Paginated, PageProps } from '@/types';

const ALL_OPTION = 'all';

interface MyExamsFilters {
    search: string;
    status: string;
    exam_type_id: string;
    date_from: string;
    date_to: string;
}

interface MyExamsIndexProps extends Record<string, unknown> {
    exams: Paginated<Exam>;
    statusOptions: Record<string, string>;
    examTypes: ExamType[];
    filters: MyExamsFilters;
}

function formatDate(isoDate: string): string {
    const [year, month, day] = isoDate.slice(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

export default function MyExamsIndex() {
    const { exams, statusOptions, examTypes, filters } = usePage<
        PageProps<MyExamsIndexProps>
    >().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'patient-exams.index',
        initialValue: filters.search,
        extraParams: {
            status: filters.status,
            exam_type_id: filters.exam_type_id,
            date_from: filters.date_from,
            date_to: filters.date_to,
        },
    });

    function applyFilters(next: Partial<MyExamsFilters>): void {
        const params = {
            search,
            status: filters.status,
            exam_type_id: filters.exam_type_id,
            date_from: filters.date_from,
            date_to: filters.date_to,
            ...next,
        };

        router.get(
            route('patient-exams.index'),
            Object.fromEntries(
                Object.entries(params).filter(([, value]) => value !== ''),
            ),
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function clearFilters(): void {
        reset();
        router.get(
            route('patient-exams.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters = !!(
        search ||
        filters.status ||
        filters.exam_type_id ||
        filters.date_from ||
        filters.date_to
    );

    const columns: Column<Exam>[] = [
        {
            header: 'Tipo',
            cell: (row) => row.exam_type?.name ?? 'N/A',
        },
        {
            header: 'Amostra',
            cell: (row) => (
                <span className="font-mono">{row.sample?.code ?? 'N/A'}</span>
            ),
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
                <div className="flex justify-end gap-2">
                    <Link
                        href={route('patient-exams.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'outline', size: 'sm' }),
                        )}
                        aria-label="Ver detalhes do exame"
                    >
                        <Eye className="size-4" />
                        Ver
                    </Link>
                    <a
                        href={route('patient-exams.pdf', row.id)}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={cn(
                            buttonVariants({ variant: 'outline', size: 'sm' }),
                        )}
                        aria-label="Visualizar PDF do exame"
                    >
                        <FileText className="size-4" />
                        PDF
                    </a>
                </div>
            ),
        },
    ];

    return (
        <AppLayout title="Meus Exames">
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
                        placeholder="Buscar por tipo de exame..."
                        className="w-full"
                    />
                </div>
                <div className="space-y-1.5">
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={filters.status || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                status: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="status" className="sm:w-48">
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
                <div className="space-y-2">
                    <Label htmlFor="exam_type_id">Tipo</Label>
                    <Select
                        value={filters.exam_type_id || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                exam_type_id: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="exam_type_id" className="sm:w-48">
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
                <div className="space-y-2">
                    <Label htmlFor="date_from">Data de</Label>
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
                    <Label htmlFor="date_to">Data até</Label>
                    <Input
                        id="date_to"
                        type="date"
                        value={filters.date_to}
                        onChange={(e) =>
                            applyFilters({ date_to: e.target.value })
                        }
                    />
                </div>
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={exams.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum exame encontrado."
            />
            <TablePagination paginator={exams} />
        </AppLayout>
    );
}
