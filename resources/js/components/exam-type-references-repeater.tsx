import { Plus, Trash2 } from 'lucide-react';
import type { ReactElement } from 'react';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { ExamTypeFieldReference, ReferenceSex } from '@/types';

/** Valor sentinela do <Select> de sexo: a referência vale para ambos os sexos. */
export const ANY_SEX_VALUE = 'any';

export type ReferenceSexOption = ReferenceSex | typeof ANY_SEX_VALUE;

/** Linha do formulário (inputs controlados guardam texto). */
export interface ExamTypeFieldReferenceRow {
    id?: number;
    sex: ReferenceSexOption;
    age_min: string;
    age_max: string;
    min_value: string;
    max_value: string;
}

/** Formato enviado ao backend: o sentinela de sexo vira null. */
export interface ExamTypeFieldReferencePayload {
    id?: number;
    sex: ReferenceSex | null;
    age_min: string;
    age_max: string;
    min_value: string;
    max_value: string;
}

interface SexOption {
    value: ReferenceSexOption;
    label: string;
}

const SEX_OPTIONS: readonly SexOption[] = [
    { value: ANY_SEX_VALUE, label: 'Ambos' },
    { value: 'male', label: 'Masculino' },
    { value: 'female', label: 'Feminino' },
];

const MIN_AGE = 0;
const MAX_AGE = 150;

export function createEmptyReferenceRow(): ExamTypeFieldReferenceRow {
    return {
        sex: ANY_SEX_VALUE,
        age_min: '',
        age_max: '',
        min_value: '',
        max_value: '',
    };
}

function numberToInputValue(value: number | null): string {
    return value === null ? '' : String(value);
}

export function mapReferenceToRow(
    reference: ExamTypeFieldReference,
): ExamTypeFieldReferenceRow {
    return {
        id: reference.id,
        sex: reference.sex ?? ANY_SEX_VALUE,
        age_min: numberToInputValue(reference.age_min),
        age_max: numberToInputValue(reference.age_max),
        min_value: numberToInputValue(reference.min_value),
        max_value: numberToInputValue(reference.max_value),
    };
}

export function prepareReferenceForSubmit(
    row: ExamTypeFieldReferenceRow,
): ExamTypeFieldReferencePayload {
    return { ...row, sex: row.sex === ANY_SEX_VALUE ? null : row.sex };
}

function isReferenceSexOption(value: string): value is ReferenceSexOption {
    return SEX_OPTIONS.some((option) => option.value === value);
}

interface ExamTypeReferencesRepeaterProps {
    fieldIndex: number;
    references: ExamTypeFieldReferenceRow[];
    onChange: (references: ExamTypeFieldReferenceRow[]) => void;
    errors: Partial<Record<string, string>>;
}

export function ExamTypeReferencesRepeater({
    fieldIndex,
    references,
    onChange,
    errors,
}: ExamTypeReferencesRepeaterProps): ReactElement {
    const errorPrefix = `fields.${fieldIndex}.references`;

    function updateReference<K extends keyof ExamTypeFieldReferenceRow>(
        index: number,
        key: K,
        value: ExamTypeFieldReferenceRow[K],
    ): void {
        onChange(
            references.map((reference, currentIndex) =>
                currentIndex === index
                    ? { ...reference, [key]: value }
                    : reference,
            ),
        );
    }

    function addReference(): void {
        onChange([...references, createEmptyReferenceRow()]);
    }

    function removeReference(index: number): void {
        onChange(
            references.filter((_, currentIndex) => currentIndex !== index),
        );
    }

    return (
        <div className="space-y-3 rounded-md border border-dashed p-3 sm:col-span-2">
            <div className="flex items-center justify-between gap-2">
                <div>
                    <Label>Valores de referência</Label>
                    <p className="text-xs text-muted-foreground">
                        Faixa esperada para uma pessoa saudável. Deixe sexo e
                        idades em branco para valer para todos.
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={addReference}
                >
                    <Plus className="size-4" />
                    Adicionar referência
                </Button>
            </div>

            {errors[errorPrefix] && (
                <p className="text-sm text-destructive">
                    {errors[errorPrefix]}
                </p>
            )}

            {references.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    Nenhum valor de referência definido.
                </p>
            ) : (
                references.map((reference, index) => {
                    const idPrefix = `fields-${fieldIndex}-references-${index}`;
                    const errorKey = `${errorPrefix}.${index}`;

                    return (
                        <div
                            key={reference.id ?? `new-${index}`}
                            className="grid gap-3 sm:grid-cols-2 lg:grid-cols-6"
                        >
                            <FormField
                                id={`${idPrefix}-sex`}
                                label="Sexo"
                                error={errors[`${errorKey}.sex`]}
                            >
                                <Select
                                    value={reference.sex}
                                    onValueChange={(value) => {
                                        if (isReferenceSexOption(value)) {
                                            updateReference(
                                                index,
                                                'sex',
                                                value,
                                            );
                                        }
                                    }}
                                >
                                    <SelectTrigger
                                        id={`${idPrefix}-sex`}
                                        className="w-full"
                                    >
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {SEX_OPTIONS.map((option) => (
                                            <SelectItem
                                                key={option.value}
                                                value={option.value}
                                            >
                                                {option.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormField>

                            <FormField
                                id={`${idPrefix}-age_min`}
                                label="Idade mín."
                                error={errors[`${errorKey}.age_min`]}
                            >
                                <Input
                                    id={`${idPrefix}-age_min`}
                                    type="number"
                                    min={MIN_AGE}
                                    max={MAX_AGE}
                                    value={reference.age_min}
                                    onChange={(e) =>
                                        updateReference(
                                            index,
                                            'age_min',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Qualquer"
                                />
                            </FormField>

                            <FormField
                                id={`${idPrefix}-age_max`}
                                label="Idade máx."
                                error={errors[`${errorKey}.age_max`]}
                            >
                                <Input
                                    id={`${idPrefix}-age_max`}
                                    type="number"
                                    min={MIN_AGE}
                                    max={MAX_AGE}
                                    value={reference.age_max}
                                    onChange={(e) =>
                                        updateReference(
                                            index,
                                            'age_max',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Qualquer"
                                />
                            </FormField>

                            <FormField
                                id={`${idPrefix}-min_value`}
                                label="Valor mín."
                                error={errors[`${errorKey}.min_value`]}
                            >
                                <Input
                                    id={`${idPrefix}-min_value`}
                                    type="number"
                                    step="any"
                                    value={reference.min_value}
                                    onChange={(e) =>
                                        updateReference(
                                            index,
                                            'min_value',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id={`${idPrefix}-max_value`}
                                label="Valor máx."
                                error={errors[`${errorKey}.max_value`]}
                            >
                                <Input
                                    id={`${idPrefix}-max_value`}
                                    type="number"
                                    step="any"
                                    value={reference.max_value}
                                    onChange={(e) =>
                                        updateReference(
                                            index,
                                            'max_value',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <div className="flex items-end justify-end sm:col-span-2 lg:col-span-1">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    className="text-destructive hover:text-destructive"
                                    onClick={() => removeReference(index)}
                                >
                                    <Trash2 className="size-4" />
                                    Remover
                                </Button>
                            </div>
                        </div>
                    );
                })
            )}
        </div>
    );
}
