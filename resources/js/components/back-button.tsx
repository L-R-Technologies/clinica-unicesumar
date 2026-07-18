import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';

/** Botão de voltar padrão do header — leva de volta à listagem/tela anterior. */
export function BackButton({
    href,
    label = 'Voltar',
}: {
    href: string;
    label?: string;
}) {
    return (
        <Button variant="outline" asChild>
            <Link href={href}>
                <ArrowLeft />
                {label}
            </Link>
        </Button>
    );
}
