import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent, ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { Button } from '@/components/ui/button';
import { stripDigits } from '@/lib/masks';
import type { PageProps, Patient } from '@/types';
import { PatientFormFields } from './patient-form-fields';
import type { PatientFormData, SetPatientFormField } from './types';

interface PatientsEditProps extends Record<string, unknown> {
    patient: Patient;
    sexOptions: Record<string, string>;
}

/**
 * Upload de arquivo via PUT não é suportado pelo PHP (multipart só chega em
 * POST), então o formulário envia POST com _method=put (method spoofing).
 */
type PatientEditFormData = PatientFormData & { _method: 'put' };

const ISO_DATE_LENGTH = 10;

function toDateInputValue(value: string | null): string {
    return value ? value.slice(0, ISO_DATE_LENGTH) : '';
}

export default function PatientsEdit(): ReactElement {
    const { patient, sexOptions } =
        usePage<PageProps<PatientsEditProps>>().props;

    const { data, setData, post, processing, errors, transform } =
        useForm<PatientEditFormData>({
            _method: 'put',
            name: patient.user?.name ?? '',
            email: patient.user?.email ?? '',
            birthday: toDateInputValue(patient.birthday),
            sex: patient.sex ?? '',
            cpf: patient.cpf ?? '',
            rg: patient.rg ?? '',
            ethnicity: patient.ethnicity ?? '',
            phone: patient.phone ?? '',
            zip_code: patient.address?.zip_code ?? '',
            street: patient.address?.street ?? '',
            number: patient.address?.number ?? '',
            complement: patient.address?.complement ?? '',
            neighborhood: patient.address?.neighborhood ?? '',
            city: patient.address?.city ?? '',
            state: patient.address?.state ?? '',
            country: patient.address?.country ?? '',
            lgpd_term: null,
        });

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

        post(route('patients.update', patient.id), { forceFormData: true });
    }

    return (
        <AppLayout
            title="Editar Paciente"
            actions={
                <>
                    <BackButton
                        href={route('patients.show', patient.id)}
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
                <PatientFormFields
                    data={data}
                    errors={errors}
                    setField={setField}
                    sexOptions={sexOptions}
                    isEditing
                    currentTermUrl={
                        patient.lgpd_term_path
                            ? route('patients.lgpd-term', patient.id)
                            : null
                    }
                />
            </form>
        </AppLayout>
    );
}
