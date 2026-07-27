import { cloneElement, isValidElement, type ReactNode } from 'react';
import { Label } from '@/components/ui/label';

interface FormFieldProps {
    id: string;
    label: string;
    error?: string;
    required?: boolean;
    className?: string;
    children: ReactNode;
}

/**
 * Atributos ARIA injetados no controle filho para que erros e obrigatoriedade
 * sejam anunciados por leitores de tela — não apenas indicados visualmente.
 */
interface InjectedControlProps {
    'aria-invalid'?: boolean;
    'aria-describedby'?: string;
    'aria-required'?: boolean;
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
    const errorId = `${id}-error`;

    const control = isValidElement<InjectedControlProps>(children)
        ? cloneElement(children, {
              'aria-invalid': error ? true : undefined,
              'aria-describedby': error ? errorId : undefined,
              'aria-required': required ? true : undefined,
          })
        : children;

    return (
        <div className={className ? `space-y-2 ${className}` : 'space-y-2'}>
            <Label htmlFor={id}>
                {label}
                {required && (
                    <span className="text-destructive" aria-hidden="true">
                        {' '}
                        *
                    </span>
                )}
            </Label>
            {control}
            {error && (
                <p id={errorId} className="text-sm text-destructive">
                    {error}
                </p>
            )}
        </div>
    );
}
