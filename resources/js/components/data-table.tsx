import type { ReactNode } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

export interface Column<T> {
    header: string;
    cell: (row: T) => ReactNode;
    className?: string;
}

interface DataTableProps<T> {
    columns: Column<T>[];
    rows: T[];
    getRowKey: (row: T) => string | number;
    emptyMessage?: string;
}

export function DataTable<T>({
    columns,
    rows,
    getRowKey,
    emptyMessage = 'Nenhum registro encontrado.',
}: DataTableProps<T>) {
    if (rows.length === 0) {
        return (
            <div className="rounded-md border bg-card p-6 text-center text-sm text-muted-foreground">
                {emptyMessage}
            </div>
        );
    }

    return (
        <>
            <div className="hidden overflow-hidden rounded-md border bg-card md:block">
                <Table>
                    <TableHeader className="bg-muted/50">
                        <TableRow>
                            {columns.map((column) => (
                                <TableHead
                                    key={column.header}
                                    className={column.className}
                                >
                                    {column.header}
                                </TableHead>
                            ))}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {rows.map((row) => (
                            <TableRow key={getRowKey(row)}>
                                {columns.map((column) => (
                                    <TableCell
                                        key={column.header}
                                        className={column.className}
                                    >
                                        {column.cell(row)}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            <div className="flex flex-col gap-3 md:hidden">
                {rows.map((row) => (
                    <dl
                        key={getRowKey(row)}
                        className="space-y-2 rounded-md border bg-card p-4"
                    >
                        {columns.map((column) => (
                            <div
                                key={column.header}
                                className="flex items-start justify-between gap-4"
                            >
                                <dt className="shrink-0 text-sm text-muted-foreground">
                                    {column.header}
                                </dt>
                                <dd className="text-right text-sm">
                                    {column.cell(row)}
                                </dd>
                            </div>
                        ))}
                    </dl>
                ))}
            </div>
        </>
    );
}
