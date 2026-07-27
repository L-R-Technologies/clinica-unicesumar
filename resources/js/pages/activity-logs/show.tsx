import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { PageProps } from '@/types';

const EVENT_BADGE_CLASSES: Record<string, string> = {
    created:
        'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
    updated:
        'border-transparent bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300',
    deleted:
        'border-transparent bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300',
};

const DIFF_TITLES: Record<string, string> = {
    updated: 'Alterações realizadas',
    created: 'Dados criados',
    deleted: 'Dados excluídos',
};

type DiffType = 'updated' | 'created' | 'deleted' | 'none';

interface DiffItem {
    field: string;
    old?: string;
    new?: string;
    value?: string;
}

interface ActivityLogEntry {
    id: number;
    log_name: string | null;
    log_name_label: string;
    event: string | null;
    event_label: string;
    description: string | null;
    causer: { name: string; email: string } | null;
    subject_type: string | null;
    subject_type_label: string | null;
    subject_id: number | null;
    created_at: string | null;
    diff: { type: DiffType; items: DiffItem[] };
}

interface ActivityLogShowProps extends Record<string, unknown> {
    log: ActivityLogEntry;
}

function InfoRow({ label, value }: { label: string; value: ReactNode }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <div className="font-medium">{value}</div>
        </div>
    );
}

function DiffTable({ diff }: { diff: ActivityLogEntry['diff'] }) {
    if (diff.type === 'none' || diff.items.length === 0) {
        return (
            <p className="text-sm text-muted-foreground">
                Nenhum detalhe adicional disponível para este registro.
            </p>
        );
    }

    if (diff.type === 'updated') {
        return (
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Campo</TableHead>
                            <TableHead>Valor anterior</TableHead>
                            <TableHead>Valor novo</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {diff.items.map((item) => (
                            <TableRow key={item.field}>
                                <TableCell className="font-medium">
                                    {item.field}
                                </TableCell>
                                <TableCell className="text-red-600 dark:text-red-400">
                                    {item.old}
                                </TableCell>
                                <TableCell className="text-emerald-600 dark:text-emerald-400">
                                    {item.new}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        );
    }

    return (
        <div className="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Campo</TableHead>
                        <TableHead>Valor</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {diff.items.map((item) => (
                        <TableRow key={item.field}>
                            <TableCell className="font-medium">
                                {item.field}
                            </TableCell>
                            <TableCell>{item.value}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}

export default function ActivityLogShow() {
    const { log } = usePage<PageProps<ActivityLogShowProps>>().props;

    const eventClassName =
        (log.event && EVENT_BADGE_CLASSES[log.event]) ??
        'border-transparent bg-muted text-muted-foreground';

    return (
        <AppLayout
            title="Detalhes da Atividade"
            actions={
                <Button variant="outline" asChild>
                    <Link href={route('activity-logs.index')}>
                        <ArrowLeft />
                        Voltar
                    </Link>
                </Button>
            }
        >
            <Card className="mx-auto w-full max-w-4xl">
                <CardContent className="space-y-6">
                    <div className="grid gap-4 sm:grid-cols-3">
                        <InfoRow
                            label="Data/Hora"
                            value={formatDateTime(log.created_at)}
                        />
                        <InfoRow
                            label="Tipo de registro"
                            value={
                                <Badge variant="secondary">
                                    {log.log_name_label}
                                </Badge>
                            }
                        />
                        <InfoRow
                            label="Evento"
                            value={
                                <Badge
                                    className={cn(
                                        'font-medium',
                                        eventClassName,
                                    )}
                                >
                                    {log.event_label}
                                </Badge>
                            }
                        />
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2">
                        <InfoRow
                            label="Usuário responsável"
                            value={
                                log.causer ? (
                                    <div className="flex flex-col">
                                        <span>{log.causer.name}</span>
                                        <span className="text-sm font-normal text-muted-foreground">
                                            {log.causer.email}
                                        </span>
                                    </div>
                                ) : (
                                    <span className="text-muted-foreground">
                                        Sistema
                                    </span>
                                )
                            }
                        />
                        <InfoRow
                            label="Objeto"
                            value={
                                <span className="flex items-center gap-2">
                                    <Badge variant="secondary">
                                        {log.subject_type_label ?? '—'}
                                    </Badge>
                                    {log.subject_id !== null && (
                                        <span className="text-sm text-muted-foreground">
                                            #{log.subject_id}
                                        </span>
                                    )}
                                </span>
                            }
                        />
                    </div>

                    <div className="space-y-3">
                        <h3 className="text-lg font-semibold">
                            {DIFF_TITLES[log.diff.type] ?? 'Detalhes'}
                        </h3>
                        <DiffTable diff={log.diff} />
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
