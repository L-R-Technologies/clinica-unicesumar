import { Search } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

interface SearchInputProps {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    id?: string;
    /** Sobrescreve a largura padrão (max-w-sm) — útil dentro de grids de filtro. */
    className?: string;
}

export function SearchInput({
    value,
    onChange,
    placeholder = 'Buscar...',
    id,
    className,
}: SearchInputProps) {
    return (
        <div className={cn('relative w-full max-w-sm', className)}>
            <Search className="absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
                id={id}
                type="search"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                className="pl-8"
            />
        </div>
    );
}
