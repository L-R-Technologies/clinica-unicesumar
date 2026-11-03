import { router, useForm } from '@inertiajs/react';
import { Check, Copy, KeyRound, Plus, Power, RefreshCw } from 'lucide-react';
import { useState, type FormEvent, type ReactElement } from 'react';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { FormField } from '@/components/form-field';
import { ActiveBadge } from '@/components/status-badge';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { formatDateTime } from '@/lib/format';
import type { LabAgent, Machine, NewAgentToken } from '@/types';

const EMPTY_PLACEHOLDER = '—';
const COPY_FEEDBACK_MS = 2000;
const MAX_AGENT_NAME = 100;

interface LabAgentsCardProps {
    machine: Machine;
    newAgentToken: NewAgentToken | null;
}

function serverUrl(): string {
    return typeof window === 'undefined' ? '' : window.location.origin;
}

function NewTokenBanner({ token }: { token: NewAgentToken }): ReactElement {
    const [copied, setCopied] = useState(false);

    async function copyToken(): Promise<void> {
        try {
            await navigator.clipboard.writeText(token.token);
            setCopied(true);
            setTimeout(() => setCopied(false), COPY_FEEDBACK_MS);
        } catch {
            setCopied(false);
        }
    }

    return (
        <div className="space-y-3 rounded-md border border-amber-300 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950">
            <div className="flex items-start gap-2">
                <KeyRound
                    className="mt-0.5 size-4 shrink-0 text-amber-700 dark:text-amber-300"
                    aria-hidden="true"
                />
                <div>
                    <p className="font-medium">
                        Token do agente &quot;{token.agent_name}&quot;
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Copie agora: por segurança ele não será exibido de novo.
                        No agente, informe o endereço do servidor{' '}
                        <code className="rounded bg-muted px-1">
                            {serverUrl()}
                        </code>{' '}
                        e este token.
                    </p>
                </div>
            </div>
            <div className="flex flex-wrap items-center gap-2">
                <code className="rounded-md bg-background px-3 py-2 font-mono text-sm break-all select-all">
                    {token.token}
                </code>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={copyToken}
                >
                    {copied ? <Check /> : <Copy />}
                    {copied ? 'Copiado' : 'Copiar'}
                </Button>
            </div>
        </div>
    );
}

function CreateAgentDialog({ machine }: { machine: Machine }): ReactElement {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('lab-agents.store', machine.id), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setOpen(false);
            },
        });
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button size="sm">
                    <Plus />
                    Novo agente
                </Button>
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Novo agente de captura</DialogTitle>
                        <DialogDescription>
                            O agente é o programa instalado no computador ligado
                            a esta máquina pela porta serial. Dê um nome que
                            identifique esse computador.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-4">
                        <FormField
                            id="agent-name"
                            label="Nome do agente"
                            error={errors.name}
                            required
                        >
                            <Input
                                id="agent-name"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                maxLength={MAX_AGENT_NAME}
                                placeholder="Ex.: PC da bancada 1"
                                autoFocus
                            />
                        </FormField>
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setOpen(false)}
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Gerar token
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

function RegenerateTokenDialog({ agent }: { agent: LabAgent }): ReactElement {
    function regenerate(): void {
        router.post(
            route('lab-agents.regenerate-token', agent.id),
            {},
            { preserveScroll: true },
        );
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label="Regenerar token"
                >
                    <RefreshCw className="size-4" />
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Regenerar token</AlertDialogTitle>
                    <AlertDialogDescription>
                        O token atual deixa de funcionar na hora e o agente
                        &quot;{agent.name}&quot; precisará ser pareado de novo
                        com o token novo.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction onClick={regenerate}>
                        Regenerar
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}

export function LabAgentsCard({
    machine,
    newAgentToken,
}: LabAgentsCardProps): ReactElement {
    const agents = machine.lab_agents ?? [];

    function toggleStatus(agent: LabAgent): void {
        router.patch(
            route('lab-agents.toggle-status', agent.id),
            {},
            { preserveScroll: true },
        );
    }

    const columns: Column<LabAgent>[] = [
        { header: 'Nome', cell: (row) => row.name },
        {
            header: 'Token',
            cell: (row) => (
                <span className="font-mono text-xs">{row.token_prefix}…</span>
            ),
        },
        {
            header: 'Versão',
            cell: (row) => row.agent_version ?? EMPTY_PLACEHOLDER,
            className: 'text-muted-foreground',
        },
        {
            header: 'Último contato',
            cell: (row) => formatDateTime(row.last_seen_at),
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
                    <RegenerateTokenDialog agent={row} />
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label={row.active ? 'Desativar' : 'Ativar'}
                        onClick={() => toggleStatus(row)}
                    >
                        <Power className="size-4" />
                    </Button>
                    <ConfirmDeleteDialog
                        action={route('lab-agents.destroy', row.id)}
                        title="Remover agente"
                        description="O agente perde o acesso e as capturas que ele enviou serão removidas."
                    />
                </div>
            ),
        },
    ];

    return (
        <Card>
            <CardHeader className="flex flex-row items-start justify-between gap-4">
                <div>
                    <CardTitle>Agentes de captura</CardTitle>
                    <CardDescription>
                        Programas que leem a porta serial desta máquina e enviam
                        os resultados para o sistema.
                    </CardDescription>
                </div>
                <CreateAgentDialog machine={machine} />
            </CardHeader>
            <CardContent className="space-y-4">
                {newAgentToken && <NewTokenBanner token={newAgentToken} />}
                <DataTable
                    columns={columns}
                    rows={agents}
                    getRowKey={(row) => row.id}
                    emptyMessage="Nenhum agente configurado para esta máquina."
                />
            </CardContent>
        </Card>
    );
}
