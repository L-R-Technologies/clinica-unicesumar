import { Link, router, useForm, usePage } from '@inertiajs/react';
import { Check, Pencil, X } from 'lucide-react';
import { useState, type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { PatientHistorySummary } from '@/components/patient-history-summary';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { FormField } from '@/components/form-field';
import type { Exam, ExamTypeField, PageProps } from '@/types';

interface ExamsShowProps extends Record<string, unknown> {
    exam: Exam;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }
    return new Date(value).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
}

function formatResultValue(value: string | number | boolean | null): string {
    if (typeof value === 'boolean') {
        return value ? 'Sim' : 'Não';
    }
    if (value === null || value === '') {
        return '—';
    }
    return String(value);
}

function DetailItem({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value}</p>
        </div>
    );
}

export default function ExamsShow() {
    const { exam, auth } = usePage<PageProps<ExamsShowProps>>().props;
    const isTeacher = auth.user?.role === 'teacher';

    const fields: ExamTypeField[] = exam.exam_type?.fields ?? [];
    const fieldMap = new Map(fields.map((field) => [field.name, field]));
    const resultEntries = exam.results ? Object.entries(exam.results) : [];

    function handleApprove(): void {
        router.post(
            route('exam.approve', exam.id),
            {},
            { preserveScroll: true },
        );
    }

    return (
        <AppLayout
            title="Detalhes do Exame"
            actions={
                <div className="flex flex-wrap items-center gap-2">
                    <BackButton href={route('exam.index')} />
                    <Button asChild variant="outline">
                        <Link href={route('exam.edit', exam.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    {isTeacher && (
                        <>
                            <Button onClick={handleApprove}>
                                <Check />
                                Aprovar
                            </Button>
                            <RejectDialog examId={exam.id} />
                        </>
                    )}
                    <ConfirmDeleteDialog
                        action={route('exam.destroy', exam.id)}
                        description="O exame será removido permanentemente."
                        trigger={<Button variant="destructive">Excluir</Button>}
                    />
                </div>
            }
        >
            <div className="grid gap-4 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações do Exame</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 gap-4">
                        <DetailItem
                            label="Tipo"
                            value={exam.exam_type?.name ?? '—'}
                        />
                        <DetailItem
                            label="Data do Exame"
                            value={formatDate(exam.date)}
                        />
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Status
                            </p>
                            <StatusBadge status={exam.status} />
                        </div>
                        <DetailItem
                            label="Responsável"
                            value={exam.user?.name ?? '—'}
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Paciente e Amostra</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 gap-4">
                        <DetailItem
                            label="Paciente"
                            value={exam.patient?.user?.name ?? '—'}
                        />
                        <DetailItem
                            label="Anamnese"
                            value={formatDate(
                                exam.patient_history?.recorded_at ?? null,
                            )}
                        />
                        <DetailItem
                            label="Código da Amostra"
                            value={exam.sample?.code ?? '—'}
                        />
                        <DetailItem
                            label="Tipo da Amostra"
                            value={exam.sample?.sample_type?.name ?? '—'}
                        />
                    </CardContent>
                </Card>
            </div>

            {exam.patient_history && (
                <div className="space-y-4">
                    <h2 className="text-lg font-semibold">
                        Anamnese do Paciente
                    </h2>
                    <PatientHistorySummary
                        patientHistory={exam.patient_history}
                    />
                </div>
            )}

            {exam.observation && (
                <Card>
                    <CardHeader>
                        <CardTitle>Observações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-line">
                            {exam.observation}
                        </p>
                    </CardContent>
                </Card>
            )}

            <Card>
                <CardHeader>
                    <CardTitle>Resultados do Exame</CardTitle>
                </CardHeader>
                <CardContent>
                    {resultEntries.length > 0 ? (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Parâmetro</TableHead>
                                    <TableHead>Resultado</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {resultEntries.map(([key, value]) => {
                                    const field = fieldMap.get(key);
                                    return (
                                        <TableRow key={key}>
                                            <TableCell className="font-medium">
                                                {field?.label ?? key}
                                                {field?.unit && (
                                                    <span className="ml-1 text-muted-foreground">
                                                        ({field.unit})
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {formatResultValue(value)}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    ) : (
                        <p className="text-sm text-muted-foreground">
                            Este exame ainda não possui resultados cadastrados.
                        </p>
                    )}
                </CardContent>
            </Card>

            {exam.rejections && exam.rejections.length > 0 && (
                <Card className="border-destructive/50">
                    <CardHeader>
                        <CardTitle className="text-destructive">
                            Histórico de Rejeições ({exam.rejections.length})
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {exam.rejections.map((rejection, index) => (
                            <div
                                key={rejection.id}
                                className={
                                    index > 0 ? 'border-t pt-4' : undefined
                                }
                            >
                                <p className="whitespace-pre-line">
                                    {rejection.justification}
                                </p>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Rejeitado por{' '}
                                    {rejection.user?.name ?? '—'} em{' '}
                                    {formatDate(rejection.created_at)}
                                </p>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            )}
        </AppLayout>
    );
}

const MIN_JUSTIFICATION = 10;
const MAX_JUSTIFICATION = 1000;

function RejectDialog({ examId }: { examId: number }) {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        justification: '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('exam.reject', examId), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setOpen(false);
            },
        });
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline">
                    <X />
                    Rejeitar
                </Button>
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Rejeitar Exame</DialogTitle>
                        <DialogDescription>
                            A justificativa é obrigatória. O aluno receberá um
                            email com esse motivo.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-4">
                        <FormField
                            id="justification"
                            label="Justificativa da Rejeição"
                            error={errors.justification}
                            required
                        >
                            <Textarea
                                id="justification"
                                value={data.justification}
                                onChange={(e) =>
                                    setData('justification', e.target.value)
                                }
                                rows={4}
                                minLength={MIN_JUSTIFICATION}
                                maxLength={MAX_JUSTIFICATION}
                                placeholder="Explique o motivo da rejeição do exame..."
                                autoFocus
                            />
                        </FormField>
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setOpen(false)}
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            variant="destructive"
                            disabled={processing}
                        >
                            Rejeitar
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
