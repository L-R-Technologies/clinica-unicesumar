import { X } from 'lucide-react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

interface FiltersCardProps {
    children: ReactNode;
    /** Chamado ao clicar em "Limpar filtros". Omitido = botão não aparece. */
    onClear?: () => void;
    /** Só mostra o botão de limpar quando algum filtro estiver ativo. */
    hasActiveFilters?: boolean;
}

/** Card padrão que envolve a barra de filtros das telas de índice. */
export function FiltersCard({
    children,
    onClear,
    hasActiveFilters,
}: FiltersCardProps) {
    return (
        <Card>
            <CardContent className="flex flex-col gap-3">
                <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                    {children}
                </div>
                {onClear && hasActiveFilters && (
                    <div className="flex justify-end">
                        <Button variant="ghost" size="sm" onClick={onClear}>
                            <X />
                            Limpar filtros
                        </Button>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
