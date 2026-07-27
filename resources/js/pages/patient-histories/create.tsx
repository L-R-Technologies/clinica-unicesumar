import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Patient, PageProps } from '@/types';
import { PatientHistoryFields } from './patient-history-fields';
import type { PatientHistoryFormData } from './types';

interface PatientHistoriesCreateProps extends Record<string, unknown> {
    patients: Patient[];
}

function todayIsoDate(): string {
    return new Date().toISOString().slice(0, 10);
}

const INITIAL_FORM_DATA: PatientHistoryFormData = {
    patient_id: '',
    recorded_at: todayIsoDate(),
    fasting: false,
    fasting_hours: '',
    alcohol_last_24h: false,
    on_medication: false,
    medications: '',
    on_supplements: false,
    supplements: '',
    chronic_disease: false,
    chronic_disease_details: '',
    infectious_disease_history: false,
    infectious_disease_details: '',
    recent_surgery: false,
    surgery_details: '',
    allergies: false,
    allergy_details: '',
    pregnant_or_lactating: false,
    menstrual_period: '',
    smokes: false,
    cigarettes_per_day: '',
    physically_active: false,
    recent_fever_or_flu: false,
    observation: '',
};

export default function PatientHistoriesCreate() {
    const { patients } =
        usePage<PageProps<PatientHistoriesCreateProps>>().props;

    const { data, setData, post, processing, errors } =
        useForm<PatientHistoryFormData>(INITIAL_FORM_DATA);

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('patient-histories.store'));
    }

    return (
        <AppLayout
            title="Nova Anamnese"
            actions={
                <>
                    <BackButton
                        href={route('patient-histories.index')}
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
            }
        >
            <form
                id="resource-form"
                onSubmit={submit}
                className="mx-auto w-full max-w-3xl space-y-6"
            >
                <PatientHistoryFields
                    data={data}
                    setData={setData}
                    errors={errors}
                    patientField={
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
                                <SelectTrigger
                                    id="patient_id"
                                    className="w-full"
                                >
                                    <SelectValue placeholder="Selecione o paciente" />
                                </SelectTrigger>
                                <SelectContent>
                                    {patients.map((patient) => (
                                        <SelectItem
                                            key={patient.id}
                                            value={String(patient.id)}
                                        >
                                            {patient.user?.name ??
                                                `Paciente #${patient.id}`}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>
                    }
                />
            </form>
        </AppLayout>
    );
}
