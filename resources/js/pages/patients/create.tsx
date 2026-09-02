import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent, ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { Button } from '@/components/ui/button';
import { stripDigits } from '@/lib/masks';
import type { PageProps } from '@/types';
import { PatientFormFields } from './patient-form-fields';
import {
    EMPTY_PATIENT_FORM_DATA,
    type PatientFormData,
    type SetPatientFormField,
} from './types';

interface PatientsCreateProps extends Record<string, unknown> {
    sexOptions: Record<string, string>;
}

export default function PatientsCreate(): ReactElement {
    const { sexOptions } = usePage<PageProps<PatientsCreateProps>>().props;

    const { data, setData, post, processing, errors, transform } =
        useForm<PatientFormData>(EMPTY_PATIENT_FORM_DATA);

    const setField: SetPatientFormField = (field, value) =>
        setData((previous) => ({ ...previous, [field]: value }));

    function submit(event: FormEvent): void {
        event.preventDefault();

        transform((formData) => ({
            ...formData,
            cpf: stripDigits(formData.cpf),
            phone: stripDigits(formData.phone),
            zip_code: stripDigits(formData.zip_code),
        }));

        // Há upload de arquivo: força multipart/form-data.
        post(route('patients.store'), { forceFormData: true });
    }

    return (
        <AppLayout
            title="Novo Paciente"
            actions={
                <>
                    <BackButton
                        href={route('patients.index')}
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
                <PatientFormFields
                    data={data}
                    errors={errors}
                    setField={setField}
                    sexOptions={sexOptions}
                />
            </form>
        </AppLayout>
    );
}
