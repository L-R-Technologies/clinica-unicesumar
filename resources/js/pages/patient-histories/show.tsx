import { Link, usePage } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import type { ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { PatientHistorySummary } from '@/components/patient-history-summary';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatDate, formatDateTime } from '@/lib/format';
import type { PageProps } from '@/types';
import type { PatientHistoryRecord } from './types';

interface PatientHistoriesShowProps extends Record<string, unknown> {
    patientHistory: PatientHistoryRecord;
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
    const { patientHistory } =
        usePage<PageProps<PatientHistoriesShowProps>>().props;

    return (
        <AppLayout
            title="Detalhes da Anamnese"
            actions={
                <div className="flex gap-2">
                    <BackButton href={route('patient-histories.index')} />
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
            <div className="mx-auto w-full max-w-3xl space-y-6">
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

                <PatientHistorySummary patientHistory={patientHistory} />
            </div>
        </AppLayout>
    );
}
