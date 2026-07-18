import type { Patient, User } from '@/types';

/**
 * Registro completo de anamnese (todos os campos do $fillable do model).
 * O tipo PatientHistory de @/types é parcial (index signature); este é o
 * shape completo usado pelas telas de leitura e edição deste domínio.
 */
export interface PatientHistoryRecord {
    id: number;
    user_id: number;
    patient_id: number;
    fasting: boolean;
    fasting_hours: number | null;
    alcohol_last_24h: boolean;
    on_medication: boolean;
    medications: string | null;
    on_supplements: boolean;
    supplements: string | null;
    chronic_disease: boolean;
    chronic_disease_details: string | null;
    infectious_disease_history: boolean;
    infectious_disease_details: string | null;
    recent_surgery: boolean;
    surgery_details: string | null;
    allergies: boolean;
    allergy_details: string | null;
    pregnant_or_lactating: boolean;
    menstrual_period: string;
    smokes: boolean;
    cigarettes_per_day: number | null;
    physically_active: boolean;
    recent_fever_or_flu: boolean;
    observation: string | null;
    recorded_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    patient?: Patient;
    user?: User;
}

/**
 * Shape do formulário de anamnese. Campos numéricos ficam como string (valor
 * dos inputs); os booleanos habilitam seus respectivos campos de detalhe.
 */
export interface PatientHistoryFormData {
    patient_id: string;
    recorded_at: string;
    fasting: boolean;
    fasting_hours: string;
    alcohol_last_24h: boolean;
    on_medication: boolean;
    medications: string;
    on_supplements: boolean;
    supplements: string;
    chronic_disease: boolean;
    chronic_disease_details: string;
    infectious_disease_history: boolean;
    infectious_disease_details: string;
    recent_surgery: boolean;
    surgery_details: string;
    allergies: boolean;
    allergy_details: string;
    pregnant_or_lactating: boolean;
    menstrual_period: string;
    smokes: boolean;
    cigarettes_per_day: string;
    physically_active: boolean;
    recent_fever_or_flu: boolean;
    observation: string;
    [key: string]: string | boolean;
}

export const MENSTRUAL_PERIOD_OPTIONS: ReadonlyArray<{
    value: string;
    label: string;
}> = [
    { value: 'yes', label: 'Sim' },
    { value: 'no', label: 'Não' },
    { value: 'n/a', label: 'Não se aplica' },
];

export function formatMenstrualPeriod(value: string | null): string {
    const option = MENSTRUAL_PERIOD_OPTIONS.find((item) => item.value === value);

    return option ? option.label : '—';
}
