import { Link, useForm, usePage } from '@inertiajs/react';
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
import type { PageProps, Patient, Sample, SampleType } from '@/types';

interface SampleEditProps extends Record<string, unknown> {
    sample: Sample;
    patients: Patient[];
    sampleTypes: SampleType[];
    statusOptions: Record<string, string>;
}

export default function SampleEdit() {
    const { sample, patients, sampleTypes, statusOptions } = usePage<
        PageProps<SampleEditProps>
    >().props;

    const { data, setData, put, processing, errors } = useForm({
        patient_id: String(sample.patient_id),
        sample_type_id: String(sample.sample_type_id),
        date: sample.date.slice(0, 10),
        status: sample.status,
        location: sample.location ?? '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        put(route('samples.update', sample.id));
    }

    return (
        <AppLayout title="Editar Amostra">
            <Card className="max-w-2xl">
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <FormField
                            id="patient_id"
                            label="Paciente"
                            error={errors.patient_id}
                            required
                        >
                            <Select
                                value={data.patient_id}
                                onValueChange={(value) =>
                                    setData('patient_id', value)
                                }
                            >
                                <SelectTrigger id="patient_id">
                                    <SelectValue placeholder="Selecione o paciente" />
                                </SelectTrigger>
                                <SelectContent>
                                    {patients.map((patient) => (
                                        <SelectItem
                                            key={patient.id}
                                            value={String(patient.id)}
                                        >
                                            {patient.user?.name ?? 'N/A'}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>

                        <FormField
                            id="sample_type_id"
                            label="Tipo de amostra"
                            error={errors.sample_type_id}
                            required
                        >
                            <Select
                                value={data.sample_type_id}
                                onValueChange={(value) =>
                                    setData('sample_type_id', value)
                                }
                            >
                                <SelectTrigger id="sample_type_id">
                                    <SelectValue placeholder="Selecione o tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    {sampleTypes.map((sampleType) => (
                                        <SelectItem
                                            key={sampleType.id}
                                            value={String(sampleType.id)}
                                        >
                                            {sampleType.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>

                        <FormField
                            id="date"
                            label="Data da coleta"
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
                            id="status"
                            label="Status"
                            error={errors.status}
                            required
                        >
                            <Select
                                value={data.status}
                                onValueChange={(value) =>
                                    setData('status', value)
                                }
                            >
                                <SelectTrigger id="status">
                                    <SelectValue placeholder="Selecione o status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.entries(statusOptions).map(
                                        ([value, label]) => (
                                            <SelectItem
                                                key={value}
                                                value={value}
                                            >
                                                {label}
                                            </SelectItem>
                                        ),
                                    )}
                                </SelectContent>
                            </Select>
                        </FormField>

                        <FormField
                            id="location"
                            label="Localização"
                            error={errors.location}
                        >
                            <Input
                                id="location"
                                value={data.location}
                                onChange={(e) =>
                                    setData('location', e.target.value)
                                }
                                placeholder="Ex.: Geladeira 2, prateleira B"
                            />
                        </FormField>

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('samples.index')}>
                                    Cancelar
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Salvar alterações
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
