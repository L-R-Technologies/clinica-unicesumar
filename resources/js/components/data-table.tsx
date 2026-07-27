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
    return (
        <div className="overflow-hidden rounded-md border bg-card">
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
                    {rows.length === 0 ? (
                        <TableRow>
                            <TableCell
                                colSpan={columns.length}
                                className="h-24 text-center text-muted-foreground"
                            >
                                {emptyMessage}
                            </TableCell>
                        </TableRow>
                    ) : (
                        rows.map((row) => (
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
                        ))
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
