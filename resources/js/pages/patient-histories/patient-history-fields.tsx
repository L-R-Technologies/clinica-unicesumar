import type { SetDataAction } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { FormField } from '@/components/form-field';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { todayIsoDate } from '@/lib/date';
import { MENSTRUAL_PERIOD_OPTIONS, type PatientHistoryFormData } from './types';

interface PatientHistoryFieldsProps {
    data: PatientHistoryFormData;
    setData: SetDataAction<PatientHistoryFormData>;
    errors: Partial<Record<string, string>>;
    patientField: ReactNode;
}

interface CheckboxRowProps {
    id: string;
    label: string;
    checked: boolean;
    onChange: (checked: boolean) => void;
}

function CheckboxRow({ id, label, checked, onChange }: CheckboxRowProps) {
    return (
        <div className="flex items-center gap-2">
            <Checkbox
                id={id}
                checked={checked}
                onCheckedChange={(value) => onChange(value === true)}
            />
            <Label htmlFor={id} className="cursor-pointer font-normal">
                {label}
            </Label>
        </div>
    );
}

export function PatientHistoryFields({
    data,
    setData,
    errors,
    patientField,
}: PatientHistoryFieldsProps) {
    function toggleWithDetail(
        field: string,
        detailField: string,
        checked: boolean,
    ): void {
        setData(field, checked);
        if (!checked) {
            setData(detailField, '');
        }
    }

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Informações Básicas</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    {patientField}
                    <FormField
                        id="recorded_at"
                        label="Data da coleta"
                        error={errors.recorded_at}
                        required
                    >
                        <Input
                            id="recorded_at"
                            type="date"
                            max={todayIsoDate()}
                            value={data.recorded_at}
                            onChange={(e) =>
                                setData('recorded_at', e.target.value)
                            }
                        />
                    </FormField>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Jejum e Álcool</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <CheckboxRow
                        id="fasting"
                        label="Paciente está em jejum"
                        checked={data.fasting}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'fasting',
                                'fasting_hours',
                                checked,
                            )
                        }
                    />
                    {data.fasting && (
                        <FormField
                            id="fasting_hours"
                            label="Horas de jejum"
                            error={errors.fasting_hours}
                            required
                        >
                            <Input
                                id="fasting_hours"
                                type="number"
                                min={0}
                                value={data.fasting_hours}
                                onChange={(e) =>
                                    setData('fasting_hours', e.target.value)
                                }
                            />
                        </FormField>
                    )}
                    <CheckboxRow
                        id="alcohol_last_24h"
                        label="Consumiu álcool nas últimas 24h"
                        checked={data.alcohol_last_24h}
                        onChange={(checked) =>
                            setData('alcohol_last_24h', checked)
                        }
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Medicações e Suplementos</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <CheckboxRow
                        id="on_medication"
                        label="Faz uso de medicamentos"
                        checked={data.on_medication}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'on_medication',
                                'medications',
                                checked,
                            )
                        }
                    />
                    {data.on_medication && (
                        <FormField
                            id="medications"
                            label="Quais medicamentos"
                            error={errors.medications}
                            required
                        >
                            <Input
                                id="medications"
                                value={data.medications}
                                onChange={(e) =>
                                    setData('medications', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                    <CheckboxRow
                        id="on_supplements"
                        label="Faz uso de suplementos"
                        checked={data.on_supplements}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'on_supplements',
                                'supplements',
                                checked,
                            )
                        }
                    />
                    {data.on_supplements && (
                        <FormField
                            id="supplements"
                            label="Quais suplementos"
                            error={errors.supplements}
                            required
                        >
                            <Input
                                id="supplements"
                                value={data.supplements}
                                onChange={(e) =>
                                    setData('supplements', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Histórico de Doenças</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <CheckboxRow
                        id="chronic_disease"
                        label="Possui doença crônica"
                        checked={data.chronic_disease}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'chronic_disease',
                                'chronic_disease_details',
                                checked,
                            )
                        }
                    />
                    {data.chronic_disease && (
                        <FormField
                            id="chronic_disease_details"
                            label="Detalhes da doença crônica"
                            error={errors.chronic_disease_details}
                            required
                        >
                            <Input
                                id="chronic_disease_details"
                                value={data.chronic_disease_details}
                                onChange={(e) =>
                                    setData(
                                        'chronic_disease_details',
                                        e.target.value,
                                    )
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                    <CheckboxRow
                        id="infectious_disease_history"
                        label="Histórico de doença infecciosa"
                        checked={data.infectious_disease_history}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'infectious_disease_history',
                                'infectious_disease_details',
                                checked,
                            )
                        }
                    />
                    {data.infectious_disease_history && (
                        <FormField
                            id="infectious_disease_details"
                            label="Detalhes da doença infecciosa"
                            error={errors.infectious_disease_details}
                            required
                        >
                            <Input
                                id="infectious_disease_details"
                                value={data.infectious_disease_details}
                                onChange={(e) =>
                                    setData(
                                        'infectious_disease_details',
                                        e.target.value,
                                    )
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Cirurgias e Alergias</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <CheckboxRow
                        id="recent_surgery"
                        label="Cirurgia recente"
                        checked={data.recent_surgery}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'recent_surgery',
                                'surgery_details',
                                checked,
                            )
                        }
                    />
                    {data.recent_surgery && (
                        <FormField
                            id="surgery_details"
                            label="Detalhes da cirurgia"
                            error={errors.surgery_details}
                            required
                        >
                            <Input
                                id="surgery_details"
                                value={data.surgery_details}
                                onChange={(e) =>
                                    setData('surgery_details', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                    <CheckboxRow
                        id="allergies"
                        label="Possui alergias"
                        checked={data.allergies}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'allergies',
                                'allergy_details',
                                checked,
                            )
                        }
                    />
                    {data.allergies && (
                        <FormField
                            id="allergy_details"
                            label="Detalhes das alergias"
                            error={errors.allergy_details}
                            required
                        >
                            <Input
                                id="allergy_details"
                                value={data.allergy_details}
                                onChange={(e) =>
                                    setData('allergy_details', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Estilo de Vida e Saúde</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <CheckboxRow
                        id="smokes"
                        label="É fumante"
                        checked={data.smokes}
                        onChange={(checked) =>
                            toggleWithDetail(
                                'smokes',
                                'cigarettes_per_day',
                                checked,
                            )
                        }
                    />
                    {data.smokes && (
                        <FormField
                            id="cigarettes_per_day"
                            label="Cigarros por dia"
                            error={errors.cigarettes_per_day}
                            required
                        >
                            <Input
                                id="cigarettes_per_day"
                                type="number"
                                min={0}
                                value={data.cigarettes_per_day}
                                onChange={(e) =>
                                    setData(
                                        'cigarettes_per_day',
                                        e.target.value,
                                    )
                                }
                            />
                        </FormField>
                    )}
                    <CheckboxRow
                        id="physically_active"
                        label="Fisicamente ativo"
                        checked={data.physically_active}
                        onChange={(checked) =>
                            setData('physically_active', checked)
                        }
                    />
                    <CheckboxRow
                        id="pregnant_or_lactating"
                        label="Gestante ou lactante"
                        checked={data.pregnant_or_lactating}
                        onChange={(checked) =>
                            setData('pregnant_or_lactating', checked)
                        }
                    />
                    <CheckboxRow
                        id="recent_fever_or_flu"
                        label="Febre ou gripe recente"
                        checked={data.recent_fever_or_flu}
                        onChange={(checked) =>
                            setData('recent_fever_or_flu', checked)
                        }
                    />
                    <FormField
                        id="menstrual_period"
                        label="Período menstrual"
                        error={errors.menstrual_period}
                        required
                    >
                        <Select
                            value={data.menstrual_period}
                            onValueChange={(value) =>
                                setData('menstrual_period', value)
                            }
                        >
                            <SelectTrigger
                                id="menstrual_period"
                                className="w-full"
                            >
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                {MENSTRUAL_PERIOD_OPTIONS.map((option) => (
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
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Observações</CardTitle>
                </CardHeader>
                <CardContent>
                    <FormField
                        id="observation"
                        label="Observações gerais"
                        error={errors.observation}
                    >
                        <Textarea
                            id="observation"
                            value={data.observation}
                            onChange={(e) =>
                                setData('observation', e.target.value)
                            }
                            rows={4}
                            maxLength={1000}
                        />
                    </FormField>
                </CardContent>
            </Card>
        </div>
    );
}
