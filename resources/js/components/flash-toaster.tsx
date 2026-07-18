import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import type { PageProps } from '@/types';

export function FlashToaster() {
    const { flash } = usePage<PageProps>().props;

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        // O objeto flash é recriado a cada visita Inertia, então mensagens
        // repetidas em ações consecutivas ainda disparam um novo toast.
    }, [flash]);

    return null;
}
