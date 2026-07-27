import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { cn } from '@/lib/utils';

const SCALE_VALUES = [1, 2, 3, 4, 5] as const;

interface RatingScaleProps {
    id: string;
    question: string;
    value: number | null;
    onChange: (value: number) => void;
    error?: string;
}

/** Campo de avaliação de 1 a 5 usado nos formulários de feedback de exame. */
export function RatingScale({
    id,
    question,
    value,
    onChange,
    error,
}: RatingScaleProps) {
    const labelId = `${id}-label`;
    const errorId = `${id}-error`;

    return (
        <div className="space-y-2">
            <Label id={labelId}>{question}</Label>
            <RadioGroup
                value={value ? String(value) : ''}
                onValueChange={(next) => onChange(Number(next))}
                aria-labelledby={labelId}
                aria-invalid={error ? true : undefined}
                aria-describedby={error ? errorId : undefined}
                className="grid grid-flow-col justify-start gap-6"
            >
                {SCALE_VALUES.map((scaleValue) => (
                    <div
                        key={scaleValue}
                        className="flex flex-col items-center gap-1.5"
                    >
                        <RadioGroupItem
                            id={`${id}-${scaleValue}`}
                            value={String(scaleValue)}
                        />
                        <Label
                            htmlFor={`${id}-${scaleValue}`}
                            className="text-xs font-normal text-muted-foreground"
                        >
                            {scaleValue}
                        </Label>
                    </div>
                ))}
            </RadioGroup>
            {error && (
                <p id={errorId} className="text-sm text-destructive">
                    {error}
                </p>
            )}
        </div>
    );
}

/** Exibição somente leitura de uma avaliação já enviada. */
export function RatingScaleReadOnly({
    question,
    value,
}: {
    question: string;
    value: number;
}) {
    return (
        <div className="space-y-1.5">
            <p className="text-sm text-muted-foreground">{question}</p>
            <div className="flex gap-2">
                {SCALE_VALUES.map((scaleValue) => (
                    <span
                        key={scaleValue}
                        className={cn(
                            'flex size-8 items-center justify-center rounded-full border text-sm font-medium',
                            scaleValue === value
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-input text-muted-foreground',
                        )}
                    >
                        {scaleValue}
                    </span>
                ))}
            </div>
        </div>
    );
}
