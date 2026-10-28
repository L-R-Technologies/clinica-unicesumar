import type { ReactElement } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { ExamTypeField } from '@/types';

export type ResultValue = string | boolean;

export function buildInitialResults(
    fields: ExamTypeField[],
    saved: Record<string, string | number | boolean | null> | null | undefined,
): Record<string, ResultValue> {
    const initial: Record<string, ResultValue> = {};
    for (const field of fields) {
        const value = saved?.[field.name];
        if (field.field_type === 'boolean') {
            initial[field.name] = value === true || value === 'true';
        } else {
            initial[field.name] = value != null ? String(value) : '';
        }
    }
    return initial;
}

interface ResultInputProps {
    field: ExamTypeField;
    value: ResultValue;
    onChange: (value: ResultValue) => void;
    hint?: string;
}

export function ResultInput({
    field,
    value,
    onChange,
    hint,
}: ResultInputProps): ReactElement {
    const label = field.unit ? `${field.label} (${field.unit})` : field.label;

    if (field.field_type === 'boolean') {
        return (
            <label className="flex items-center gap-2 self-end pb-2 text-sm">
                <Checkbox
                    checked={value === true}
                    onCheckedChange={(checked) => onChange(checked === true)}
                />
                <span>{label}</span>
            </label>
        );
    }

    const isNumber = field.field_type === 'int' || field.field_type === 'float';

    return (
        <div className="space-y-2">
            <Label htmlFor={`result-${field.name}`}>{label}</Label>
            <Input
                id={`result-${field.name}`}
                type={isNumber ? 'number' : 'text'}
                step={field.field_type === 'float' ? '0.01' : undefined}
                value={typeof value === 'string' ? value : ''}
                onChange={(e) => onChange(e.target.value)}
                placeholder={field.label}
            />
            {hint && <p className="text-xs text-muted-foreground">{hint}</p>}
        </div>
    );
}
