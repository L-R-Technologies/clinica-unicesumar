import { Link, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus, Power } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { FiltersCard } from '@/components/filters-card';
import { SearchInput } from '@/components/search-input';
import { ActiveBadge } from '@/components/status-badge';
import { TablePagination } from '@/components/table-pagination';
import { Badge } from '@/components/ui/badge';
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
import type { Paginated, PageProps, User } from '@/types';

const ALL_OPTION = 'all';

interface UsersIndexFilters {
    search: string;
    role: string;
    status: string;
}

interface UsersIndexProps extends Record<string, unknown> {
    users: Paginated<User>;
    filters: UsersIndexFilters;
    roleOptions: Record<string, string>;
    statusOptions: Record<string, string>;
}

export default function UsersIndex() {
    const { users, filters, roleOptions, statusOptions } = usePage<
        PageProps<UsersIndexProps>
    >().props;

    const { search, setSearch, reset } = useDebouncedSearch({
        routeName: 'user-management.index',
        initialValue: filters.search,
        extraParams: { role: filters.role, status: filters.status },
    });

    function applyFilters(next: Partial<UsersIndexFilters>): void {
        const params = {
            search,
            role: filters.role,
            status: filters.status,
            ...next,
        };

        router.get(
            route('user-management.index'),
            Object.fromEntries(
                Object.entries(params).filter(([, value]) => value !== ''),
            ),
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function clearFilters(): void {
        reset();
        router.get(
            route('user-management.index'),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters = !!(search || filters.role || filters.status);

    const columns: Column<User>[] = [
        { header: 'Nome', cell: (row) => row.name },
        {
            header: 'Email',
            cell: (row) => row.email,
            className: 'text-muted-foreground',
        },
        {
            header: 'Perfil',
            cell: (row) => (
                <Badge variant="secondary">
                    {roleOptions[row.role] ?? row.role}
                </Badge>
            ),
        },
        {
            header: 'Status',
            cell: (row) => <ActiveBadge active={row.active} />,
        },
        {
            header: 'Ações',
            className: 'w-1 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Link
                        href={route('user-management.show', row.id)}
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                        )}
                        aria-label="Visualizar"
                    >
                        <Eye className="size-4" />
                    </Link>
                    <Link
                        href={route('user-management.edit', row.id)}
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
                        aria-label={row.active ? 'Desativar' : 'Ativar'}
                        onClick={() =>
                            router.patch(
                                route(
                                    'user-management.toggle-status',
                                    row.id,
                                ),
                                {},
                                { preserveScroll: true },
                            )
                        }
                    >
                        <Power className="size-4" />
                    </Button>
                    <ConfirmDeleteDialog
                        action={route('user-management.destroy', row.id)}
                        description="Esta ação não pode ser desfeita e removerá o usuário permanentemente."
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout
            title="Gerenciamento de Usuários"
            actions={
                <Button asChild>
                    <Link href={route('user-management.create')}>
                        <Plus />
                        Novo usuário
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
                        placeholder="Buscar por nome ou email..."
                        className="w-full"
                    />
                </div>
                <div className="space-y-1.5">
                    <Label htmlFor="role">Perfil</Label>
                    <Select
                        value={filters.role || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                role: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="role" className="sm:w-44">
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL_OPTION}>Todos</SelectItem>
                            {Object.entries(roleOptions).map(
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
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={filters.status || ALL_OPTION}
                        onValueChange={(value) =>
                            applyFilters({
                                status: value === ALL_OPTION ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger id="status" className="sm:w-44">
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
            </FiltersCard>
            <DataTable
                columns={columns}
                rows={users.data}
                getRowKey={(row) => row.id}
                emptyMessage="Nenhum usuário encontrado."
            />
            <TablePagination paginator={users} />
        </AppLayout>
    );
}
