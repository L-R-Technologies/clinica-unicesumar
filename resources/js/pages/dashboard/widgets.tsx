import type { ReactElement, ReactNode } from 'react';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { cn } from '@/lib/utils';
import type { ExamStatus } from '@/types';
import type { DashboardStat, StatTone } from './types';

const LOCALE = 'pt-BR';
const FULL_WIDTH_PERCENT = 100;

const TONE_TEXT_CLASSES: Record<StatTone, string> = {
    primary: 'text-primary',
    success: 'text-emerald-600 dark:text-emerald-400',
    warning: 'text-amber-600 dark:text-amber-400',
    danger: 'text-red-600 dark:text-red-400',
};

/**
 * Cores de estado (mesma paleta dos badges de status). Reservadas para status:
 * as barras de magnitude usam sempre a cor primária.
 */
const STATUS_BAR_CLASSES: Record<ExamStatus, string> = {
    approved: 'bg-emerald-500 dark:bg-emerald-400',
    pending_approval: 'bg-amber-500 dark:bg-amber-400',
    pending: 'bg-amber-300 dark:bg-amber-600',
    rejected: 'bg-red-500 dark:bg-red-400',
};

export function formatNumber(value: number): string {
    return value.toLocaleString(LOCALE);
}

export function StatTile({ stat }: { stat: DashboardStat }): ReactElement {
    return (
        <Card>
            <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">
                    {stat.label}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p
                    className={cn(
                        'text-3xl font-bold tabular-nums',
                        TONE_TEXT_CLASSES[stat.tone],
                    )}
                >
                    {formatNumber(stat.value)}
                    {stat.suffix}
                </p>
                {stat.hint && (
                    <p className="mt-1 text-xs text-muted-foreground">
                        {stat.hint}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

interface SectionCardProps {
    title: string;
    description?: string;
    children: ReactNode;
    className?: string;
}

export function SectionCard({
    title,
    description,
    children,
    className,
}: SectionCardProps): ReactElement {
    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                {description && (
                    <CardDescription>{description}</CardDescription>
                )}
            </CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}

export function EmptyState({
    children,
}: {
    children: ReactNode;
}): ReactElement {
    return <p className="text-sm text-muted-foreground">{children}</p>;
}

export interface BarListItem {
    key: string;
    label: string;
    value: number;
    /** Texto exibido à direita (padrão: o valor formatado). */
    display?: string;
    /** Texto do tooltip nativo ao passar o mouse. */
    title?: string;
    status?: ExamStatus;
}

interface BarListProps {
    items: BarListItem[];
    emptyMessage: string;
}

/**
 * Gráfico de barras horizontais em CSS: uma barra fina por linha, largura
 * proporcional ao maior valor, rótulo e valor sempre em texto (nunca só cor).
 */
export function BarList({ items, emptyMessage }: BarListProps): ReactElement {
    if (items.length === 0) {
        return <EmptyState>{emptyMessage}</EmptyState>;
    }

    const maxValue = Math.max(...items.map((item) => item.value), 1);

    return (
        <ul className="space-y-3" role="list">
            {items.map((item) => (
                <li
                    key={item.key}
                    className="space-y-1"
                    title={
                        item.title ??
                        `${item.label}: ${formatNumber(item.value)}`
                    }
                >
                    <div className="flex items-baseline justify-between gap-4 text-sm">
                        <span className="truncate">{item.label}</span>
                        <span className="shrink-0 font-medium tabular-nums">
                            {item.display ?? formatNumber(item.value)}
                        </span>
                    </div>
                    <div className="h-2 w-full rounded-sm bg-muted">
                        <div
                            className={cn(
                                'h-2 rounded-sm',
                                item.status
                                    ? STATUS_BAR_CLASSES[item.status]
                                    : 'bg-primary',
                            )}
                            style={{
                                width: `${(item.value / maxValue) * FULL_WIDTH_PERCENT}%`,
                            }}
                        />
                    </div>
                </li>
            ))}
        </ul>
    );
}
