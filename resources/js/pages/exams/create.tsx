import { Link, router, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { ExamType, PageProps } from '@/types';

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

interface ExamsCreateProps extends Record<string, unknown> {
    patients: PatientOption[];
    examTypes: ExamType[];
    histories: HistoryOption[];
    samples: SampleOption[];
}

function today(): string {
    return new Date().toISOString().slice(0, 10);
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }
    return new Date(value).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
}

export default function ExamsCreate() {
    const { patients, examTypes, histories, samples } =
        usePage<PageProps<ExamsCreateProps>>().props;

    const { data, setData, post, processing, errors, transform } = useForm({
        patient_id: '',
        patient_history_id: '',
        sample_id: '',
        exam_type_id: '',
        date: today(),
        observation: '',
    });

    // Ao trocar o paciente, recarrega histórico/amostras via partial reload do Inertia.
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

    function submit(event: FormEvent): void {
        event.preventDefault();
        transform((formData) => ({
            ...formData,
            sample_id: formData.sample_id || null,
        }));
        post(route('exam.store'));
    }

    const hasPatient = data.patient_id !== '';

    return (
        <AppLayout title="Novo Exame">
            <Card className="max-w-3xl">
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
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
                                    disabled={!hasPatient}
                                >
                                    <SelectTrigger
                                        id="patient_history_id"
                                        className="w-full"
                                    >
                                        <SelectValue
                                            placeholder={
                                                hasPatient
                                                    ? 'Selecione uma anamnese'
                                                    : 'Selecione um paciente primeiro'
                                            }
                                        />
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
                                id="exam_type_id"
                                label="Tipo de Exame"
                                error={errors.exam_type_id}
                                required
                            >
                                <Select
                                    value={data.exam_type_id}
                                    onValueChange={(value) =>
                                        setData('exam_type_id', value)
                                    }
                                >
                                    <SelectTrigger
                                        id="exam_type_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione o tipo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {examTypes.map((examType) => (
                                            <SelectItem
                                                key={examType.id}
                                                value={String(examType.id)}
                                            >
                                                {examType.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
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
                                    disabled={!hasPatient}
                                >
                                    <SelectTrigger
                                        id="sample_id"
                                        className="w-full"
                                    >
                                        <SelectValue
                                            placeholder={
                                                hasPatient
                                                    ? 'Selecione uma amostra'
                                                    : 'Selecione um paciente primeiro'
                                            }
                                        />
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

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('exam.index')}>Cancelar</Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Salvar exame
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
