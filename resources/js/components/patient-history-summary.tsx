import type { ReactNode } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatMenstrualPeriod } from '@/pages/patient-histories/types';
import type { PatientHistory } from '@/types';

function booleanLabel(value: boolean): string {
    return value ? 'Sim' : 'Não';
}

function InfoItem({ label, value }: { label: string; value: ReactNode }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value}</p>
        </div>
    );
}

/**
 * Detalhes clínicos completos de uma anamnese, em cards de leitura. Usado na
 * própria tela da anamnese e na tela de detalhes do exame vinculado a ela —
 * mantém as duas telas consistentes sem duplicar a formatação dos ~20 campos.
 */
export function PatientHistorySummary({
    patientHistory,
}: {
    patientHistory: PatientHistory;
}) {
    return (
        <>
            <Card>
                <CardHeader>
                    <CardTitle>Jejum e Álcool</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <InfoItem
                        label="Jejum"
                        value={booleanLabel(patientHistory.fasting)}
                    />
                    <InfoItem
                        label="Horas de jejum"
                        value={patientHistory.fasting_hours ?? '—'}
                    />
                    <InfoItem
                        label="Álcool nas últimas 24h"
                        value={booleanLabel(patientHistory.alcohol_last_24h)}
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Medicações e Suplementos</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <InfoItem
                        label="Uso de medicamentos"
                        value={booleanLabel(patientHistory.on_medication)}
                    />
                    <InfoItem
                        label="Medicamentos"
                        value={patientHistory.medications || '—'}
                    />
                    <InfoItem
                        label="Uso de suplementos"
                        value={booleanLabel(patientHistory.on_supplements)}
                    />
                    <InfoItem
                        label="Suplementos"
                        value={patientHistory.supplements || '—'}
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Histórico de Doenças</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <InfoItem
                        label="Doença crônica"
                        value={booleanLabel(patientHistory.chronic_disease)}
                    />
                    <InfoItem
                        label="Detalhes"
                        value={patientHistory.chronic_disease_details || '—'}
                    />
                    <InfoItem
                        label="Doença infecciosa"
                        value={booleanLabel(
                            patientHistory.infectious_disease_history,
                        )}
                    />
                    <InfoItem
                        label="Detalhes"
                        value={patientHistory.infectious_disease_details || '—'}
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Cirurgias e Alergias</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <InfoItem
                        label="Cirurgia recente"
                        value={booleanLabel(patientHistory.recent_surgery)}
                    />
                    <InfoItem
                        label="Detalhes"
                        value={patientHistory.surgery_details || '—'}
                    />
                    <InfoItem
                        label="Alergias"
                        value={booleanLabel(patientHistory.allergies)}
                    />
                    <InfoItem
                        label="Detalhes"
                        value={patientHistory.allergy_details || '—'}
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Estilo de Vida e Saúde</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <InfoItem
                        label="Fumante"
                        value={booleanLabel(patientHistory.smokes)}
                    />
                    <InfoItem
                        label="Cigarros por dia"
                        value={patientHistory.cigarettes_per_day ?? '—'}
                    />
                    <InfoItem
                        label="Fisicamente ativo"
                        value={booleanLabel(patientHistory.physically_active)}
                    />
                    <InfoItem
                        label="Gestante ou lactante"
                        value={booleanLabel(
                            patientHistory.pregnant_or_lactating,
                        )}
                    />
                    <InfoItem
                        label="Período menstrual"
                        value={formatMenstrualPeriod(
                            patientHistory.menstrual_period,
                        )}
                    />
                    <InfoItem
                        label="Febre ou gripe recente"
                        value={booleanLabel(patientHistory.recent_fever_or_flu)}
                    />
                </CardContent>
            </Card>

            {patientHistory.observation && (
                <Card>
                    <CardHeader>
                        <CardTitle>Observações da Anamnese</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-line">
                            {patientHistory.observation}
                        </p>
                    </CardContent>
                </Card>
            )}
        </>
    );
}
