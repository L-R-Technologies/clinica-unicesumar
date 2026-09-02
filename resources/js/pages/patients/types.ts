export type PatientFormData = {
    name: string;
    email: string;
    birthday: string;
    sex: string;
    cpf: string;
    rg: string;
    ethnicity: string;
    phone: string;
    zip_code: string;
    street: string;
    number: string;
    complement: string;
    neighborhood: string;
    city: string;
    state: string;
    country: string;
    /** Foto ou PDF do termo LGPD assinado; obrigatório no cadastro. */
    lgpd_term: File | null;
};

export type PatientFormFieldName = keyof PatientFormData;

export type PatientFormErrors = Partial<Record<PatientFormFieldName, string>>;

export type SetPatientFormField = <K extends PatientFormFieldName>(
    field: K,
    value: PatientFormData[K],
) => void;

export const EMPTY_PATIENT_FORM_DATA: PatientFormData = {
    name: '',
    email: '',
    birthday: '',
    sex: '',
    cpf: '',
    rg: '',
    ethnicity: '',
    phone: '',
    zip_code: '',
    street: '',
    number: '',
    complement: '',
    neighborhood: '',
    city: '',
    state: '',
    country: '',
    lgpd_term: null,
};
