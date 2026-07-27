import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PageProps } from '@/types';
import { PatientHistoryFields } from './patient-history-fields';
import type { PatientHistoryFormData, PatientHistoryRecord } from './types';

interface PatientHistoriesEditProps extends Record<string, unknown> {
    patientHistory: PatientHistoryRecord;
}

function numberToString(value: number | null): string {
    return value === null ? '' : String(value);
}

function toDateInput(value: string | null): string {
    return value ? value.slice(0, 10) : '';
}

function buildFormData(record: PatientHistoryRecord): PatientHistoryFormData {
    return {
        patient_id: String(record.patient_id),
        recorded_at: toDateInput(record.recorded_at),
        fasting: record.fasting,
        fasting_hours: numberToString(record.fasting_hours),
        alcohol_last_24h: record.alcohol_last_24h,
        on_medication: record.on_medication,
        medications: record.medications ?? '',
        on_supplements: record.on_supplements,
        supplements: record.supplements ?? '',
        chronic_disease: record.chronic_disease,
        chronic_disease_details: record.chronic_disease_details ?? '',
        infectious_disease_history: record.infectious_disease_history,
        infectious_disease_details: record.infectious_disease_details ?? '',
        recent_surgery: record.recent_surgery,
        surgery_details: record.surgery_details ?? '',
        allergies: record.allergies,
        allergy_details: record.allergy_details ?? '',
        pregnant_or_lactating: record.pregnant_or_lactating,
        menstrual_period: record.menstrual_period ?? '',
        smokes: record.smokes,
        cigarettes_per_day: numberToString(record.cigarettes_per_day),
        physically_active: record.physically_active,
        recent_fever_or_flu: record.recent_fever_or_flu,
        observation: record.observation ?? '',
    };
}

export default function PatientHistoriesEdit() {
    const { patientHistory } =
        usePage<PageProps<PatientHistoriesEditProps>>().props;

    const { data, setData, put, processing, errors } =
        useForm<PatientHistoryFormData>(buildFormData(patientHistory));

    function submit(event: FormEvent): void {
        event.preventDefault();
        put(route('patient-histories.update', patientHistory.id));
    }

    return (
        <AppLayout
            title="Editar Anamnese"
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
                        Salvar alterações
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
                        <FormField id="patient_id" label="Paciente">
                            <Input
                                id="patient_id"
                                value={
                                    patientHistory.patient?.user?.name ?? '—'
                                }
                                readOnly
                                disabled
                            />
                        </FormField>
                    }
                />
            </form>
        </AppLayout>
    );
}
