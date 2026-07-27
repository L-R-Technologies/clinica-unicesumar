import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState, type ReactNode } from 'react';
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
import { buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';

interface ConfirmDeleteDialogProps {
    /** URL da rota DELETE (ex.: route('samples.destroy', id)). */
    action: string;
    title?: string;
    description?: string;
    /** Elemento que dispara o diálogo. Um botão-lixeira padrão, se omitido. */
    trigger?: ReactNode;
}

export function ConfirmDeleteDialog({
    action,
    title = 'Confirmar exclusão',
    description = 'Esta ação não pode ser desfeita.',
    trigger,
}: ConfirmDeleteDialogProps) {
    const [processing, setProcessing] = useState(false);

    function handleDelete() {
        router.delete(action, {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
        });
    }

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>
                {trigger ?? (
                    <button
                        type="button"
                        className={cn(
                            buttonVariants({ variant: 'ghost', size: 'icon' }),
                            'text-destructive hover:text-destructive',
                        )}
                        aria-label="Excluir"
                    >
                        <Trash2 className="size-4" />
                    </button>
                )}
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>{title}</AlertDialogTitle>
                    <AlertDialogDescription>
                        {description}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction
                        onClick={handleDelete}
                        disabled={processing}
                        className={buttonVariants({ variant: 'destructive' })}
                    >
                        Excluir
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
