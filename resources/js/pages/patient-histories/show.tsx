import { Link, usePage } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import type { ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { PageProps } from '@/types';
import { formatMenstrualPeriod, type PatientHistoryRecord } from './types';

interface PatientHistoriesShowProps extends Record<string, unknown> {
    patientHistory: PatientHistoryRecord;
}

function formatDate(value: string | null): string {
    return value ? new Date(value).toLocaleDateString('pt-BR') : '—';
}

function formatDateTime(value: string | null): string {
    return value
        ? new Date(value).toLocaleString('pt-BR', {
              dateStyle: 'short',
              timeStyle: 'short',
          })
        : '—';
}

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

export default function PatientHistoriesShow() {
    const { patientHistory } = usePage<
        PageProps<PatientHistoriesShowProps>
    >().props;

    return (
        <AppLayout
            title="Detalhes da Anamnese"
            actions={
                <div className="flex gap-2">
                    <Button asChild>
                        <Link
                            href={route(
                                'patient-histories.edit',
                                patientHistory.id,
                            )}
                        >
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    <ConfirmDeleteDialog
                        action={route(
                            'patient-histories.destroy',
                            patientHistory.id,
                        )}
                        title="Excluir anamnese"
                        description="Esta ação não pode ser desfeita."
                        trigger={
                            <Button variant="destructive">
                                <Trash2 />
                                Excluir
                            </Button>
                        }
                    />
                </div>
            }
        >
            <div className="max-w-3xl space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações Básicas</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <InfoItem
                            label="Paciente"
                            value={patientHistory.patient?.user?.name ?? '—'}
                        />
                        <InfoItem
                            label="Profissional"
                            value={patientHistory.user?.name ?? '—'}
                        />
                        <InfoItem
                            label="Data da coleta"
                            value={formatDate(patientHistory.recorded_at)}
                        />
                        <InfoItem
                            label="Criado em"
                            value={formatDateTime(patientHistory.created_at)}
                        />
                    </CardContent>
                </Card>

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
                            value={booleanLabel(
                                patientHistory.alcohol_last_24h,
                            )}
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
                            value={
                                patientHistory.chronic_disease_details || '—'
                            }
                        />
                        <InfoItem
                            label="Doença infecciosa"
                            value={booleanLabel(
                                patientHistory.infectious_disease_history,
                            )}
                        />
                        <InfoItem
                            label="Detalhes"
                            value={
                                patientHistory.infectious_disease_details ||
                                '—'
                            }
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
                            value={booleanLabel(
                                patientHistory.physically_active,
                            )}
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
                            value={booleanLabel(
                                patientHistory.recent_fever_or_flu,
                            )}
                        />
                    </CardContent>
                </Card>

                {patientHistory.observation && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Observações</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="whitespace-pre-line">
                                {patientHistory.observation}
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
