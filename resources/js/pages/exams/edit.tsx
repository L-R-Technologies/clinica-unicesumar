import { Link, router, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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
import type { Exam, ExamTypeField, PageProps } from '@/types';

interface PatientOption {
    id: number;
    user?: { name: string } | null;
}

interface HistoryOption {
    id: number;
    recorded_at: string | null;
}

interface SampleOption {
    id: number;
    code: string;
    sample_type?: { name: string } | null;
}

interface ExamsEditProps extends Record<string, unknown> {
    exam: Exam;
    patients: PatientOption[];
    histories: HistoryOption[];
    samples: SampleOption[];
}

type ResultValue = string | boolean;

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }
    return new Date(value).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
}

function buildInitialResults(
    fields: ExamTypeField[],
    saved: Record<string, string | number | boolean | null> | null,
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

export default function ExamsEdit() {
    const { exam, patients, histories, samples } =
        usePage<PageProps<ExamsEditProps>>().props;

    const fields = exam.exam_type?.fields ?? [];
    const hasFields = fields.length > 0;

    const { data, setData, put, processing, errors, transform } = useForm({
        patient_id: String(exam.patient_id),
        patient_history_id: exam.patient_history_id
            ? String(exam.patient_history_id)
            : '',
        sample_id: exam.sample_id ? String(exam.sample_id) : '',
        exam_type_id: String(exam.exam_type_id),
        date: exam.date.slice(0, 10),
        observation: exam.observation ?? '',
        results: buildInitialResults(fields, exam.results),
        rawResults: exam.results ? JSON.stringify(exam.results, null, 2) : '',
    });

    function handlePatientChange(value: string): void {
        setData((previous) => ({
            ...previous,
            patient_id: value,
            patient_history_id: '',
            sample_id: '',
        }));
        router.reload({
            only: ['histories', 'samples'],
            data: { patient_id: value },
        });
    }

    function setResult(name: string, value: ResultValue): void {
        setData('results', { ...data.results, [name]: value });
    }

    function submit(event: FormEvent): void {
        event.preventDefault();
        transform((formData) => {
            const payload: Record<string, unknown> = {
                patient_id: formData.patient_id,
                patient_history_id: formData.patient_history_id,
                sample_id: formData.sample_id || null,
                exam_type_id: formData.exam_type_id,
                date: formData.date,
                observation: formData.observation,
            };

            if (hasFields) {
                payload.results = Object.fromEntries(
                    Object.entries(formData.results).filter(
                        ([, value]) => value !== '' && value !== null,
                    ),
                );
            } else if (formData.rawResults.trim()) {
                try {
                    payload.results = JSON.parse(formData.rawResults);
                } catch {
                    payload.results = null;
                }
            }

            return payload;
        });
        put(route('exam.update', exam.id));
    }

    return (
        <AppLayout title="Editar Exame"
            actions={
                <>
                    <BackButton
                        href={route('exam.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        Salvar alterações
                    </Button>
                </>
            }>
            <Card className="mx-auto w-full max-w-3xl">
                <CardContent>
                    <form id="resource-form" onSubmit={submit} className="space-y-6">
                        <div className="flex items-center gap-2">
                            <span className="text-sm text-muted-foreground">
                                Status:
                            </span>
                            <StatusBadge status={exam.status} />
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="patient_id"
                                label="Paciente"
                                error={errors.patient_id}
                                required
                            >
                                <Select
                                    value={data.patient_id}
                                    onValueChange={handlePatientChange}
                                >
                                    <SelectTrigger
                                        id="patient_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione um paciente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {patients.map((patient) => (
                                            <SelectItem
                                                key={patient.id}
                                                value={String(patient.id)}
                                            >
                                                {patient.user?.name ?? '—'}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormField>

                            <FormField
                                id="patient_history_id"
                                label="Anamnese do Paciente"
                                error={errors.patient_history_id}
                                required
                            >
                                <Select
                                    value={data.patient_history_id}
                                    onValueChange={(value) =>
                                        setData('patient_history_id', value)
                                    }
                                >
                                    <SelectTrigger
                                        id="patient_history_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione uma anamnese" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {histories.map((history) => (
                                            <SelectItem
                                                key={history.id}
                                                value={String(history.id)}
                                            >
                                                {formatDate(
                                                    history.recorded_at,
                                                )}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormField>

                            <FormField
                                id="exam_type"
                                label="Tipo de Exame"
                            >
                                <Input
                                    id="exam_type"
                                    value={exam.exam_type?.name ?? ''}
                                    disabled
                                    readOnly
                                />
                            </FormField>

                            <FormField
                                id="sample_id"
                                label="Amostra"
                                error={errors.sample_id}
                            >
                                <Select
                                    value={data.sample_id}
                                    onValueChange={(value) =>
                                        setData('sample_id', value)
                                    }
                                >
                                    <SelectTrigger
                                        id="sample_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione uma amostra" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {samples.map((sample) => (
                                            <SelectItem
                                                key={sample.id}
                                                value={String(sample.id)}
                                            >
                                                {sample.code}
                                                {sample.sample_type
                                                    ? ` - ${sample.sample_type.name}`
                                                    : ''}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormField>
                        </div>

                        <FormField
                            id="date"
                            label="Data do Exame"
                            error={errors.date}
                            required
                        >
                            <Input
                                id="date"
                                type="date"
                                value={data.date}
                                onChange={(e) => setData('date', e.target.value)}
                            />
                        </FormField>

                        <FormField
                            id="observation"
                            label="Observações"
                            error={errors.observation}
                        >
                            <Textarea
                                id="observation"
                                value={data.observation}
                                onChange={(e) =>
                                    setData('observation', e.target.value)
                                }
                                rows={3}
                                placeholder="Observações sobre o exame..."
                            />
                        </FormField>

                        <div className="space-y-4">
                            <h3 className="text-sm font-semibold">
                                Resultados do Exame
                            </h3>

                            {hasFields ? (
                                <div className="grid gap-4 sm:grid-cols-2">
                                    {fields.map((field) => (
                                        <ResultInput
                                            key={field.id}
                                            field={field}
                                            value={data.results[field.name]}
                                            onChange={(value) =>
                                                setResult(field.name, value)
                                            }
                                        />
                                    ))}
                                </div>
                            ) : (
                                <FormField
                                    id="rawResults"
                                    label="Resultados (JSON)"
                                    error={errors.results}
                                >
                                    <Textarea
                                        id="rawResults"
                                        value={data.rawResults}
                                        onChange={(e) =>
                                            setData(
                                                'rawResults',
                                                e.target.value,
                                            )
                                        }
                                        rows={6}
                                        placeholder='Exemplo: {"parametro": "valor"}'
                                        className="font-mono"
                                    />
                                </FormField>
                            )}
                        </div>

                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}

interface ResultInputProps {
    field: ExamTypeField;
    value: ResultValue;
    onChange: (value: ResultValue) => void;
}

function ResultInput({ field, value, onChange }: ResultInputProps) {
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
        </div>
    );
}
