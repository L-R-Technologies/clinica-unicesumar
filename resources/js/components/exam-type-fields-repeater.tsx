import { Plus, Trash2 } from 'lucide-react';
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
import type { ExamFieldType } from '@/types';

export interface ExamTypeFieldRow {
    id?: number;
    name: string;
    label: string;
    field_type: ExamFieldType;
    unit: string;
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

export function createEmptyExamTypeField(): ExamTypeFieldRow {
    return { name: '', label: '', field_type: 'string', unit: '' };
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
}: ExamTypeFieldsRepeaterProps) {
    function updateField(
        index: number,
        key: keyof ExamTypeFieldRow,
        value: string,
    ) {
        onChange(
            fields.map((field, currentIndex) =>
                currentIndex === index ? { ...field, [key]: value } : field,
            ),
        );
    }

    function addField() {
        onChange([...fields, createEmptyExamTypeField()]);
    }

    function removeField(index: number) {
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
                                        updateField(index, 'field_type', value)
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
