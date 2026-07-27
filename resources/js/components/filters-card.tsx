import { X } from 'lucide-react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

interface FiltersCardProps {
    children: ReactNode;
    onClear?: () => void;
    hasActiveFilters?: boolean;
}

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
