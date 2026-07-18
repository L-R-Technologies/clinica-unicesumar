export type UserRole = 'teacher' | 'student' | 'patient';

export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    active: boolean;
    email_verified_at: string | null;
}

export interface Address {
    id: number;
    street: string;
    number: string;
    complement: string | null;
    neighborhood: string;
    city: string;
    state: string;
    country: string;
    zip_code: string;
}

export interface Patient {
    id: number;
    user_id: number;
    address_id: number | null;
    birthday: string | null;
    ethnicity: string | null;
    sex: string | null;
    cpf: string | null;
    rg: string | null;
    phone: string | null;
    lgpd_consent_at: string | null;
    user?: User;
    address?: Address;
}

export interface Teacher {
    id: number;
    user_id: number;
    supervisor_id: number | null;
    registration_number: string | null;
    professional_license: string | null;
    user?: User;
}

export interface Student {
    id: number;
    user_id: number;
    supervisor_id: number | null;
    ra: string | null;
    course: string | null;
    semester: string | null;
    user?: User;
}

export type ExamFieldType = 'int' | 'float' | 'string' | 'boolean';

export interface ExamTypeField {
    id: number;
    exam_type_id: number;
    name: string;
    label: string;
    field_type: ExamFieldType;
    unit: string | null;
}

export interface ExamType {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    fields?: ExamTypeField[];
}

export interface SampleType {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
}

export interface Sample {
    id: number;
    patient_id: number;
    user_id: number;
    sample_type_id: number;
    code: string;
    date: string;
    location: string | null;
    status: string;
    stored_at: string | null;
    notified: boolean;
    patient?: Patient;
    sample_type?: SampleType;
    user?: User;
}

export type ExamStatus =
    | 'pending'
    | 'pending_approval'
    | 'approved'
    | 'rejected';

export interface ExamRejection {
    id: number;
    exam_id: number;
    user_id: number;
    justification: string;
    created_at: string;
}

export interface Exam {
    id: number;
    user_id: number;
    patient_history_id: number | null;
    patient_id: number;
    exam_type_id: number;
    sample_id: number | null;
    date: string;
    results: Record<string, string | number | boolean | null> | null;
    status: ExamStatus;
    observation: string | null;
    patient?: Patient;
    patient_history?: PatientHistory | null;
    exam_type?: ExamType;
    sample?: Sample;
    user?: User;
    latest_rejection?: ExamRejection | null;
}

export interface PatientHistory {
    id: number;
    user_id: number;
    patient_id: number;
    fasting: boolean;
    fasting_hours: number | null;
    recorded_at: string | null;
    patient?: Patient;
    user?: User;
    [key: string]: unknown;
}

export type MachineStatus = 'active' | 'inactive' | 'maintenance';

export interface Machine {
    id: number;
    name: string;
    model: string | null;
    serial_number: string | null;
    location: string | null;
    calibration_range_min: number | null;
    calibration_range_max: number | null;
    status: MachineStatus;
    calibrations?: Calibration[];
}

export type CalibrationStatus = 'approved' | 'rejected';

export interface Calibration {
    id: number;
    user_id: number;
    machine_id: number;
    calibration_date: string;
    value: number;
    status: CalibrationStatus;
    observation: string | null;
    machine?: Machine;
    user?: User;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface FlashMessages {
    success: string | null;
    error: string | null;
}

export interface Auth {
    user: User | null;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: Auth;
    flash: FlashMessages;
    errors: Record<string, string>;
};
