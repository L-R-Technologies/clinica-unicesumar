import { Plus, Trash2 } from 'lucide-react';
import type { ReactElement } from 'react';
import {
    ExamTypeReferencesRepeater,
    mapReferenceToRow,
    prepareReferenceForSubmit,
    type ExamTypeFieldReferencePayload,
    type ExamTypeFieldReferenceRow,
} from '@/components/exam-type-references-repeater';
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
import type { ExamFieldType, ExamTypeField } from '@/types';

export interface ExamTypeFieldRow {
    id?: number;
    name: string;
    label: string;
    field_type: ExamFieldType;
    unit: string;
    references: ExamTypeFieldReferenceRow[];
}

export interface ExamTypeFieldPayload extends Omit<
    ExamTypeFieldRow,
    'references'
> {
    references: ExamTypeFieldReferencePayload[];
}

interface FieldTypeOption {
    value: ExamFieldType;
    label: string;
}

const FIELD_TYPE_OPTIONS: readonly FieldTypeOption[] = [
    { value: 'int', label: 'Inteiro' },
    { value: 'float', label: 'Decimal' },
    { value: 'string', label: 'Texto' },
    { value: 'boolean', label: 'Booleano' },
];

/** Só campos numéricos aceitam valores de referência. */
const NUMERIC_FIELD_TYPES: readonly ExamFieldType[] = ['int', 'float'];

export function isNumericFieldType(fieldType: ExamFieldType): boolean {
    return NUMERIC_FIELD_TYPES.includes(fieldType);
}

function isExamFieldType(value: string): value is ExamFieldType {
    return FIELD_TYPE_OPTIONS.some((option) => option.value === value);
}

export function createEmptyExamTypeField(): ExamTypeFieldRow {
    return {
        name: '',
        label: '',
        field_type: 'string',
        unit: '',
        references: [],
    };
}

export function mapExamTypeFieldToRow(field: ExamTypeField): ExamTypeFieldRow {
    return {
        id: field.id,
        name: field.name,
        label: field.label,
        field_type: field.field_type,
        unit: field.unit ?? '',
        references: (field.references ?? []).map(mapReferenceToRow),
    };
}

/** Descarta linhas vazias e converte as referências para o formato do backend. */
export function prepareExamTypeFieldsForSubmit(
    fields: ExamTypeFieldRow[],
): ExamTypeFieldPayload[] {
    return fields
        .filter((field) => field.name !== '' || field.label !== '')
        .map((field) => ({
            ...field,
            references: field.references.map(prepareReferenceForSubmit),
        }));
}

interface ExamTypeFieldsRepeaterProps {
    fields: ExamTypeFieldRow[];
    onChange: (fields: ExamTypeFieldRow[]) => void;
    errors: Partial<Record<string, string>>;
}

export function ExamTypeFieldsRepeater({
    fields,
    onChange,
    errors,
}: ExamTypeFieldsRepeaterProps): ReactElement {
    function updateField<K extends keyof ExamTypeFieldRow>(
        index: number,
        key: K,
        value: ExamTypeFieldRow[K],
    ): void {
        onChange(
            fields.map((field, currentIndex) =>
                currentIndex === index ? { ...field, [key]: value } : field,
            ),
        );
    }

    function changeFieldType(index: number, value: string): void {
        if (!isExamFieldType(value)) {
            return;
        }

        // Referências só valem para campos numéricos: ao trocar para texto ou
        // booleano, descarta as que existiam.
        onChange(
            fields.map((field, currentIndex) =>
                currentIndex === index
                    ? {
                          ...field,
                          field_type: value,
                          references: isNumericFieldType(value)
                              ? field.references
                              : [],
                      }
                    : field,
            ),
        );
    }

    function addField(): void {
        onChange([...fields, createEmptyExamTypeField()]);
    }

    function removeField(index: number): void {
        onChange(fields.filter((_, currentIndex) => currentIndex !== index));
    }

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <Label>Campos personalizados</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={addField}
                >
                    <Plus className="size-4" />
                    Adicionar campo
                </Button>
            </div>

            {fields.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    Nenhum campo personalizado adicionado.
                </p>
            ) : (
                <div className="space-y-4">
                    {fields.map((field, index) => (
                        <div
                            key={field.id ?? `new-${index}`}
                            className="grid gap-3 rounded-md border p-4 sm:grid-cols-2"
                        >
                            <FormField
                                id={`fields-${index}-name`}
                                label="Nome (identificador)"
                                error={errors[`fields.${index}.name`]}
                                required
                            >
                                <Input
                                    id={`fields-${index}-name`}
                                    value={field.name}
                                    onChange={(e) =>
                                        updateField(
                                            index,
                                            'name',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id={`fields-${index}-label`}
                                label="Rótulo"
                                error={errors[`fields.${index}.label`]}
                                required
                            >
                                <Input
                                    id={`fields-${index}-label`}
                                    value={field.label}
                                    onChange={(e) =>
                                        updateField(
                                            index,
                                            'label',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id={`fields-${index}-field_type`}
                                label="Tipo"
                                error={errors[`fields.${index}.field_type`]}
                                required
                            >
                                <Select
                                    value={field.field_type}
                                    onValueChange={(value) =>
                                        changeFieldType(index, value)
                                    }
                                >
                                    <SelectTrigger
                                        id={`fields-${index}-field_type`}
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {FIELD_TYPE_OPTIONS.map((option) => (
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
                                id={`fields-${index}-unit`}
                                label="Unidade"
                                error={errors[`fields.${index}.unit`]}
                            >
                                <Input
                                    id={`fields-${index}-unit`}
                                    value={field.unit}
                                    onChange={(e) =>
                                        updateField(
                                            index,
                                            'unit',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Ex.: mg/dL"
                                />
                            </FormField>

                            {isNumericFieldType(field.field_type) && (
                                <ExamTypeReferencesRepeater
                                    fieldIndex={index}
                                    references={field.references}
                                    onChange={(references) =>
                                        updateField(
                                            index,
                                            'references',
                                            references,
                                        )
                                    }
                                    errors={errors}
                                />
                            )}

                            <div className="sm:col-span-2 flex justify-end">
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    className="text-destructive hover:text-destructive"
                                    onClick={() => removeField(index)}
                                >
                                    <Trash2 className="size-4" />
                                    Remover
                                </Button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
