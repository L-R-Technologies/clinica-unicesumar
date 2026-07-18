import type { ReactNode } from 'react';
import { Label } from '@/components/ui/label';

interface FormFieldProps {
    id: string;
    label: string;
    error?: string;
    required?: boolean;
    className?: string;
    children: ReactNode;
}

/** Agrupa Label + controle + mensagem de erro com espaçamento consistente. */
export function FormField({
    id,
    label,
    error,
    required,
    className,
    children,
}: FormFieldProps) {
    return (
        <div className={className ? `space-y-2 ${className}` : 'space-y-2'}>
            <Label htmlFor={id}>
                {label}
                {required && <span className="text-destructive"> *</span>}
            </Label>
            {children}
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}
