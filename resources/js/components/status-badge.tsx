import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

interface StatusConfig {
    label: string;
    className: string;
}

const SUCCESS = 'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
const WARNING = 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
const DANGER = 'border-transparent bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
const NEUTRAL = 'border-transparent bg-muted text-muted-foreground';

const STATUS_MAP: Record<string, StatusConfig> = {
    pending: { label: 'Pendente', className: WARNING },
    pending_approval: { label: 'Aguardando Aprovação', className: WARNING },
    approved: { label: 'Aprovado', className: SUCCESS },
    rejected: { label: 'Rejeitado', className: DANGER },
    active: { label: 'Ativo', className: SUCCESS },
    inactive: { label: 'Inativo', className: NEUTRAL },
    maintenance: { label: 'Manutenção', className: WARNING },
    stored: { label: 'Armazenada', className: SUCCESS },
    discarded: { label: 'Descartada', className: NEUTRAL },
};

export function StatusBadge({ status }: { status: string }) {
    const config = STATUS_MAP[status] ?? {
        label: status,
        className: NEUTRAL,
    };

    return (
        <Badge className={cn('font-medium', config.className)}>
            {config.label}
        </Badge>
    );
}

export function ActiveBadge({ active }: { active: boolean }) {
    return <StatusBadge status={active ? 'active' : 'inactive'} />;
}
