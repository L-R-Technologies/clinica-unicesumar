import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
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
import type { PageProps, Patient, SampleType } from '@/types';

interface SampleCreateProps extends Record<string, unknown> {
    patients: Patient[];
    sampleTypes: SampleType[];
    statusOptions: Record<string, string>;
}

function today(): string {
    return new Date().toISOString().slice(0, 10);
}

export default function SampleCreate() {
    const { patients, sampleTypes, statusOptions } = usePage<
        PageProps<SampleCreateProps>
    >().props;

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        sample_type_id: '',
        date: today(),
        status: 'under review',
        location: '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        post(route('samples.store'));
    }

    return (
        <AppLayout title="Nova Amostra"
            actions={
                <>
                    <BackButton
                        href={route('samples.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        Salvar
                    </Button>
                </>
            }>
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form id="resource-form" onSubmit={submit} className="space-y-4">
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

                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
