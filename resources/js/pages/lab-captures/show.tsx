import { Link, router, useForm, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    ExternalLink,
    Loader2,
    RefreshCw,
    Sparkles,
} from 'lucide-react';
import { useEffect, useState, type FormEvent, type ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import {
    buildInitialResults,
    ResultInput,
    type ResultValue,
} from '@/components/exam-result-input';
import { FormField } from '@/components/form-field';
import { StatusBadge } from '@/components/status-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { todayIsoDate } from '@/lib/date';
import { formatDate, formatDateTime, formatResultValue } from '@/lib/format';
import type {
    Exam,
    ExamType,
    ExamTypeField,
    LabCapture,
    LabCaptureAnalysis,
    PageProps,
} from '@/types';

const EMPTY_PLACEHOLDER = '—';
const POLL_INTERVAL_MS = 5000;
const PERCENT = 100;
const LINK_MODE_EXISTING = 'existing';
const LINK_MODE_NEW = 'new';
const ISO_DATE_LENGTH = 10;

type LinkMode = typeof LINK_MODE_EXISTING | typeof LINK_MODE_NEW;

interface PatientOption {
    id: number;
    user?: { name: string } | null;
}

interface HistoryOption {
    id: number;
    recorded_at: string | null;
}

interface SampleOption {
    id: number;
    code: string;
    sample_type?: { name: string } | null;
}

interface LabCaptureShowProps extends Record<string, unknown> {
    capture: LabCapture;
    examTypes: ExamType[];
    patients: PatientOption[];
    histories: HistoryOption[];
    samples: SampleOption[];
    linkableExams: Exam[];
    canReview: boolean;
    canDiscard: boolean;
    canRetry: boolean;
}

interface LinkFormData {
    mode: LinkMode;
    exam_type_id: string;
    exam_id: string;
    patient_id: string;
    patient_history_id: string;
    sample_id: string;
    date: string;
    observation: string;
    results: Record<string, ResultValue>;
}

function DetailItem({
    label,
    value,
}: {
    label: string;
    value: string;
}): ReactElement {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value || EMPTY_PLACEHOLDER}</p>
        </div>
    );
}

function defaultExamDate(capture: LabCapture): string {
    const printed = capture.analysis?.metadata?.exam_datetime;
    const candidate = (printed ?? capture.captured_at).slice(
        0,
        ISO_DATE_LENGTH,
    );
    const today = todayIsoDate();

    return /^\d{4}-\d{2}-\d{2}$/.test(candidate) && candidate <= today
        ? candidate
        : today;
}

function initialResultsFor(
    examType: ExamType | undefined,
    analysis: LabCaptureAnalysis | null,
): Record<string, ResultValue> {
    const fields = examType?.fields ?? [];
    const isSuggestedType =
        analysis !== null && examType?.id === analysis.exam_type_id;

    return buildInitialResults(
        fields,
        isSuggestedType ? analysis.results : null,
    );
}

function sourceHintFor(
    field: ExamTypeField,
    analysis: LabCaptureAnalysis | null,
    selectedTypeId: number | null,
): string | undefined {
    if (!analysis || analysis.exam_type_id !== selectedTypeId) {
        return undefined;
    }

    const detail = analysis.results_details.find(
        (item) => item.field_name === field.name,
    );

    if (!detail) {
        return undefined;
    }

    return `Lido de "${detail.source_label ?? '?'}": ${detail.source_value ?? '?'}`;
}

function ProcessingCard({ capture }: { capture: LabCapture }): ReactElement {
    return (
        <Card>
            <CardContent className="flex items-center gap-4 py-8">
                <Loader2
                    className="size-6 shrink-0 animate-spin text-primary"
                    aria-hidden="true"
                />
                <div>
                    <p className="font-medium">
                        {capture.status === 'analyzing'
                            ? 'A IA está lendo o resultado da máquina...'
                            : 'Captura na fila de análise...'}
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Esta página atualiza sozinha quando a sugestão ficar
                        pronta.
                    </p>
                </div>
            </CardContent>
        </Card>
    );
}

function FailedCard({
    capture,
    canRetry,
}: {
    capture: LabCapture;
    canRetry: boolean;
}): ReactElement {
    return (
        <Card className="border-destructive/50">
            <CardContent className="space-y-4 py-6">
                <div className="flex items-start gap-3">
                    <AlertTriangle
                        className="mt-0.5 size-5 shrink-0 text-destructive"
                        aria-hidden="true"
                    />
                    <div>
                        <p className="font-medium text-destructive">
                            A IA não conseguiu analisar esta captura.
                        </p>
                        <p className="text-sm text-muted-foreground">
                            {capture.error_message ??
                                'Falha desconhecida ao consultar a IA.'}
                            {canReview(capture) &&
                                ' Você ainda pode escolher o tipo e preencher os campos manualmente abaixo.'}
                        </p>
                    </div>
                </div>
                {canRetry && <RetryButton captureId={capture.id} />}
            </CardContent>
        </Card>
    );
}

function canReview(capture: LabCapture): boolean {
    return ['analyzed', 'unrecognized', 'failed'].includes(capture.status);
}

function RetryButton({ captureId }: { captureId: number }): ReactElement {
    function retry(): void {
        router.post(
            route('lab-captures.retry', captureId),
            {},
            { preserveScroll: true },
        );
    }

    return (
        <Button variant="outline" onClick={retry}>
            <RefreshCw />
            Reanalisar com a IA
        </Button>
    );
}

function UnrecognizedCard({
    capture,
    isTeacher,
}: {
    capture: LabCapture;
    isTeacher: boolean;
}): ReactElement {
    const suggested = capture.analysis?.suggested_exam_type_name;

    return (
        <Card className="border-amber-300 dark:border-amber-900">
            <CardContent className="space-y-3 py-6">
                <div className="flex items-start gap-3">
                    <AlertTriangle
                        className="mt-0.5 size-5 shrink-0 text-amber-600"
                        aria-hidden="true"
                    />
                    <div className="space-y-1">
                        <p className="font-medium">
                            A IA não encontrou um tipo de exame compatível no
                            sistema.
                        </p>
                        <p className="text-sm text-muted-foreground">
                            {suggested
                                ? `Nome do exame lido na impressão: "${suggested}".`
                                : 'Não foi possível identificar o nome do exame na impressão.'}{' '}
                            {capture.analysis?.exam_type_reasoning}
                        </p>
                        <p className="text-sm text-muted-foreground">
                            Você pode escolher o tipo manualmente no formulário
                            ao lado
                            {isTeacher
                                ? ' ou cadastrar um tipo novo e clicar em "Reanalisar".'
                                : ' ou pedir ao professor que cadastre o tipo.'}
                        </p>
                    </div>
                </div>
                {isTeacher && (
                    <Button asChild variant="outline">
                        <Link href={route('exam-type.create')}>
                            <ExternalLink />
                            Cadastrar tipo de exame
                        </Link>
                    </Button>
                )}
            </CardContent>
        </Card>
    );
}

function RawTextCard({ capture }: { capture: LabCapture }): ReactElement {
    const metadata = capture.analysis?.metadata ?? {};
    const unmapped = capture.analysis?.unmapped_values ?? [];
    const metadataItems: [string, string | null | undefined][] = [
        ['Data impressa', metadata.exam_datetime],
        ['Nº de série', metadata.machine_serial],
        ['Sequencial', metadata.sequence_number],
        ['Canal', metadata.channel],
        ['Operador', metadata.operator],
    ];

    return (
        <Card>
            <CardHeader>
                <CardTitle>Texto bruto da máquina</CardTitle>
                <CardDescription>
                    Exatamente como foi recebido pela porta serial. Use como
                    referência para conferir a sugestão da IA.
                </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                <pre className="max-h-[32rem] overflow-auto rounded-md bg-muted p-4 font-mono text-xs whitespace-pre-wrap">
                    {capture.raw_text}
                </pre>

                {metadata.status_note && (
                    <div className="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm dark:border-amber-900 dark:bg-amber-950">
                        <span className="font-semibold">Aviso da máquina:</span>{' '}
                        {metadata.status_note}
                    </div>
                )}

                {capture.analysis && (
                    <dl className="grid grid-cols-2 gap-3 text-sm">
                        {metadataItems.map(([label, value]) => (
                            <div key={label}>
                                <dt className="text-xs text-muted-foreground">
                                    {label}
                                </dt>
                                <dd className="font-medium">
                                    {value || EMPTY_PLACEHOLDER}
                                </dd>
                            </div>
                        ))}
                    </dl>
                )}

                {unmapped.length > 0 && (
                    <div className="space-y-1">
                        <p className="text-sm font-semibold">
                            Valores sem campo correspondente
                        </p>
                        <ul className="list-disc pl-5 text-sm text-muted-foreground">
                            {unmapped.map((item, index) => (
                                <li key={`${index}-${item.label}`}>
                                    {item.label}: {item.value}
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}

function LinkedCard({ capture }: { capture: LabCapture }): ReactElement {
    const exam = capture.exam;
    const confirmed = capture.analysis?.confirmed_results ?? {};
    const fields = exam?.exam_type?.fields ?? [];

    return (
        <Card className="border-emerald-300 dark:border-emerald-900">
            <CardHeader>
                <CardTitle>Resultado vinculado</CardTitle>
                <CardDescription>
                    Conferido por {capture.reviewer?.name ?? EMPTY_PLACEHOLDER}{' '}
                    em {formatDateTime(capture.reviewed_at)}.
                </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <DetailItem
                        label="Paciente"
                        value={exam?.patient?.user?.name ?? ''}
                    />
                    <DetailItem
                        label="Tipo de exame"
                        value={exam?.exam_type?.name ?? ''}
                    />
                </div>
                {Object.keys(confirmed).length > 0 && (
                    <dl className="grid grid-cols-2 gap-3 text-sm">
                        {Object.entries(confirmed).map(([name, value]) => {
                            const field = fields.find((f) => f.name === name);
                            return (
                                <div key={name}>
                                    <dt className="text-xs text-muted-foreground">
                                        {field?.label ?? name}
                                        {field?.unit ? ` (${field.unit})` : ''}
                                    </dt>
                                    <dd className="font-medium">
                                        {formatResultValue(value)}
                                    </dd>
                                </div>
                            );
                        })}
                    </dl>
                )}
                {exam && (
                    <Button asChild>
                        <Link href={route('exam.show', exam.id)}>
                            <ExternalLink />
                            Abrir exame
                        </Link>
                    </Button>
                )}
            </CardContent>
        </Card>
    );
}

interface ReviewFormProps {
    capture: LabCapture;
    examTypes: ExamType[];
    patients: PatientOption[];
    histories: HistoryOption[];
    samples: SampleOption[];
    linkableExams: Exam[];
}

function ReviewForm({
    capture,
    examTypes,
    patients,
    histories,
    samples,
    linkableExams,
}: ReviewFormProps): ReactElement {
    const analysis = capture.analysis;
    const suggestedTypeId = analysis?.exam_type_id ?? null;
    const [selectedType, setSelectedType] = useState<ExamType | undefined>(
        examTypes.find((type) => type.id === suggestedTypeId),
    );

    const { data, setData, post, processing, errors, transform } =
        useForm<LinkFormData>({
            mode: LINK_MODE_EXISTING,
            exam_type_id: suggestedTypeId ? String(suggestedTypeId) : '',
            exam_id: '',
            patient_id: '',
            patient_history_id: '',
            sample_id: '',
            date: defaultExamDate(capture),
            observation: '',
            results: initialResultsFor(selectedType, analysis),
        });

    const fields = selectedType?.fields ?? [];
    const confidence = analysis
        ? Math.round(analysis.exam_type_confidence * PERCENT)
        : null;

    function reloadPatientData(patientId: string, examTypeId: string): void {
        router.reload({
            only: ['histories', 'samples', 'linkableExams'],
            data: { patient_id: patientId, exam_type_id: examTypeId },
        });
    }

    function handleTypeChange(value: string): void {
        const examType = examTypes.find((type) => String(type.id) === value);
        setSelectedType(examType);
        setData((previous) => ({
            ...previous,
            exam_type_id: value,
            exam_id: '',
            results: initialResultsFor(examType, analysis),
        }));
        if (data.patient_id) {
            reloadPatientData(data.patient_id, value);
        }
    }

    function handlePatientChange(value: string): void {
        setData((previous) => ({
            ...previous,
            patient_id: value,
            patient_history_id: '',
            sample_id: '',
            exam_id: '',
        }));
        reloadPatientData(value, data.exam_type_id);
    }

    function setResult(name: string, value: ResultValue): void {
        setData('results', { ...data.results, [name]: value });
    }

    function submit(event: FormEvent): void {
        event.preventDefault();
        transform((formData) => ({
            ...formData,
            exam_id: formData.exam_id || null,
            sample_id: formData.sample_id || null,
            results: Object.fromEntries(
                Object.entries(formData.results).filter(
                    ([, value]) => value !== '',
                ),
            ),
        }));
        post(route('lab-captures.link', capture.id), { preserveScroll: true });
    }

    const hasPatient = data.patient_id !== '';
    const isNewExam = data.mode === LINK_MODE_NEW;

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Sparkles
                        className="size-4 text-primary"
                        aria-hidden="true"
                    />
                    Conferência do resultado
                </CardTitle>
                <CardDescription>
                    {analysis?.exam_type_reasoning ??
                        'Escolha o tipo de exame e preencha os campos a partir do texto bruto.'}
                    {confidence !== null && suggestedTypeId !== null && (
                        <Badge variant="outline" className="ml-2">
                            confiança {confidence}%
                        </Badge>
                    )}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form id="link-form" onSubmit={submit} className="space-y-6">
                    <FormField
                        id="exam_type_id"
                        label="Tipo de exame"
                        error={errors.exam_type_id}
                        required
                    >
                        <Select
                            value={data.exam_type_id}
                            onValueChange={handleTypeChange}
                        >
                            <SelectTrigger id="exam_type_id" className="w-full">
                                <SelectValue placeholder="Selecione o tipo" />
                            </SelectTrigger>
                            <SelectContent>
                                {examTypes.map((examType) => (
                                    <SelectItem
                                        key={examType.id}
                                        value={String(examType.id)}
                                    >
                                        {examType.name}
                                        {examType.id === suggestedTypeId
                                            ? ' (sugerido pela IA)'
                                            : ''}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>

                    <div className="space-y-3">
                        <h3 className="text-sm font-semibold">
                            Campos do exame
                        </h3>
                        {selectedType === undefined ? (
                            <p className="text-sm text-muted-foreground">
                                Escolha o tipo de exame para ver os campos.
                            </p>
                        ) : fields.length === 0 ? (
                            <p className="text-sm text-muted-foreground">
                                Este tipo não tem campos cadastrados; o exame
                                será vinculado sem resultados estruturados.
                            </p>
                        ) : (
                            <div className="grid gap-4 sm:grid-cols-2">
                                {fields.map((field) => (
                                    <ResultInput
                                        key={field.id}
                                        field={field}
                                        value={data.results[field.name] ?? ''}
                                        onChange={(value) =>
                                            setResult(field.name, value)
                                        }
                                        hint={sourceHintFor(
                                            field,
                                            analysis,
                                            selectedType.id,
                                        )}
                                    />
                                ))}
                            </div>
                        )}
                        {errors.results && (
                            <p className="text-sm text-destructive">
                                {errors.results}
                            </p>
                        )}
                    </div>

                    <div className="space-y-3">
                        <h3 className="text-sm font-semibold">
                            Vincular a qual exame?
                        </h3>
                        <RadioGroup
                            value={data.mode}
                            onValueChange={(value) =>
                                setData('mode', value as LinkMode)
                            }
                            className="grid gap-2 sm:grid-cols-2"
                        >
                            <label className="flex cursor-pointer items-center gap-2 rounded-md border p-3 text-sm">
                                <RadioGroupItem value={LINK_MODE_EXISTING} />
                                Exame já cadastrado
                            </label>
                            <label className="flex cursor-pointer items-center gap-2 rounded-md border p-3 text-sm">
                                <RadioGroupItem value={LINK_MODE_NEW} />
                                Criar um exame novo
                            </label>
                        </RadioGroup>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="patient_id"
                                label="Paciente"
                                error={errors.patient_id}
                                required
                            >
                                <Select
                                    value={data.patient_id}
                                    onValueChange={handlePatientChange}
                                >
                                    <SelectTrigger
                                        id="patient_id"
                                        className="w-full"
                                    >
                                        <SelectValue placeholder="Selecione um paciente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {patients.map((patient) => (
                                            <SelectItem
                                                key={patient.id}
                                                value={String(patient.id)}
                                            >
                                                {patient.user?.name ??
                                                    EMPTY_PLACEHOLDER}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </FormField>

                            {!isNewExam && (
                                <FormField
                                    id="exam_id"
                                    label="Exame"
                                    error={errors.exam_id}
                                    required
                                >
                                    <Select
                                        value={data.exam_id}
                                        onValueChange={(value) =>
                                            setData('exam_id', value)
                                        }
                                        disabled={!hasPatient}
                                    >
                                        <SelectTrigger
                                            id="exam_id"
                                            className="w-full"
                                        >
                                            <SelectValue
                                                placeholder={
                                                    hasPatient
                                                        ? linkableExams.length >
                                                          0
                                                            ? 'Selecione o exame'
                                                            : 'Nenhum exame pendente deste tipo'
                                                        : 'Selecione um paciente primeiro'
                                                }
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {linkableExams.map((exam) => (
                                                <SelectItem
                                                    key={exam.id}
                                                    value={String(exam.id)}
                                                >
                                                    #{exam.id} ·{' '}
                                                    {formatDate(exam.date)}
                                                    {exam.sample
                                                        ? ` · ${exam.sample.code}`
                                                        : ''}
                                                    {exam.results
                                                        ? ' · já tem resultados'
                                                        : ''}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormField>
                            )}

                            {isNewExam && (
                                <>
                                    <FormField
                                        id="patient_history_id"
                                        label="Anamnese"
                                        error={errors.patient_history_id}
                                        required
                                    >
                                        <Select
                                            value={data.patient_history_id}
                                            onValueChange={(value) =>
                                                setData(
                                                    'patient_history_id',
                                                    value,
                                                )
                                            }
                                            disabled={!hasPatient}
                                        >
                                            <SelectTrigger
                                                id="patient_history_id"
                                                className="w-full"
                                            >
                                                <SelectValue
                                                    placeholder={
                                                        hasPatient
                                                            ? 'Selecione uma anamnese'
                                                            : 'Selecione um paciente primeiro'
                                                    }
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {histories.map((history) => (
                                                    <SelectItem
                                                        key={history.id}
                                                        value={String(
                                                            history.id,
                                                        )}
                                                    >
                                                        {formatDate(
                                                            history.recorded_at,
                                                        )}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </FormField>

                                    <FormField
                                        id="sample_id"
                                        label="Amostra"
                                        error={errors.sample_id}
                                    >
                                        <Select
                                            value={data.sample_id}
                                            onValueChange={(value) =>
                                                setData('sample_id', value)
                                            }
                                            disabled={!hasPatient}
                                        >
                                            <SelectTrigger
                                                id="sample_id"
                                                className="w-full"
                                            >
                                                <SelectValue
                                                    placeholder={
                                                        hasPatient
                                                            ? 'Selecione uma amostra'
                                                            : 'Selecione um paciente primeiro'
                                                    }
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {samples.map((sample) => (
                                                    <SelectItem
                                                        key={sample.id}
                                                        value={String(
                                                            sample.id,
                                                        )}
                                                    >
                                                        {sample.code}
                                                        {sample.sample_type
                                                            ? ` - ${sample.sample_type.name}`
                                                            : ''}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </FormField>

                                    <FormField
                                        id="date"
                                        label="Data do exame"
                                        error={errors.date}
                                        required
                                    >
                                        <Input
                                            id="date"
                                            type="date"
                                            max={todayIsoDate()}
                                            value={data.date}
                                            onChange={(e) =>
                                                setData('date', e.target.value)
                                            }
                                        />
                                    </FormField>

                                    <FormField
                                        id="observation"
                                        label="Observações"
                                        error={errors.observation}
                                        className="sm:col-span-2"
                                    >
                                        <Textarea
                                            id="observation"
                                            value={data.observation}
                                            onChange={(e) =>
                                                setData(
                                                    'observation',
                                                    e.target.value,
                                                )
                                            }
                                            rows={2}
                                            maxLength={1000}
                                            placeholder="Observações sobre o exame..."
                                        />
                                    </FormField>
                                </>
                            )}
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={processing}>
                            {isNewExam
                                ? 'Criar exame com este resultado'
                                : 'Vincular ao exame'}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}

export default function LabCaptureShow(): ReactElement {
    const {
        capture,
        examTypes,
        patients,
        histories,
        samples,
        linkableExams,
        canReview: isReviewable,
        canDiscard,
        canRetry,
        auth,
    } = usePage<PageProps<LabCaptureShowProps>>().props;
    const isTeacher = auth.user?.role === 'teacher';
    const isInProgress =
        capture.status === 'received' || capture.status === 'analyzing';

    useEffect(() => {
        if (!isInProgress) {
            return;
        }

        const timer = setInterval(() => {
            router.reload({ only: ['capture', 'canReview', 'canRetry'] });
        }, POLL_INTERVAL_MS);

        return () => clearInterval(timer);
    }, [isInProgress]);

    return (
        <AppLayout
            title={`Captura #${capture.id}`}
            actions={
                <div className="flex flex-wrap items-center gap-2">
                    <BackButton href={route('lab-captures.index')} />
                    {canRetry && !isInProgress && (
                        <RetryButton captureId={capture.id} />
                    )}
                    {canDiscard && (
                        <ConfirmDeleteDialog
                            action={route('lab-captures.discard', capture.id)}
                            title="Descartar captura"
                            description="A captura ficará marcada como descartada e não aparecerá mais para conferência."
                            trigger={
                                <Button variant="destructive">Descartar</Button>
                            }
                        />
                    )}
                </div>
            }
        >
            <Card>
                <CardContent className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <p className="text-sm text-muted-foreground">Status</p>
                        <StatusBadge status={capture.status} />
                    </div>
                    <DetailItem
                        label="Máquina"
                        value={capture.machine?.name ?? ''}
                    />
                    <DetailItem
                        label="Capturado em"
                        value={formatDateTime(capture.captured_at)}
                    />
                    <DetailItem
                        label="Agente"
                        value={capture.lab_agent?.name ?? ''}
                    />
                    <DetailItem
                        label="Modelo de IA"
                        value={capture.ai_model ?? ''}
                    />
                </CardContent>
            </Card>

            {isInProgress && <ProcessingCard capture={capture} />}
            {capture.status === 'failed' && (
                <FailedCard capture={capture} canRetry={canRetry} />
            )}
            {capture.status === 'unrecognized' && (
                <UnrecognizedCard capture={capture} isTeacher={isTeacher} />
            )}

            <div className="grid gap-4 lg:grid-cols-2">
                <RawTextCard capture={capture} />
                <div className="space-y-4">
                    {capture.status === 'linked' && (
                        <LinkedCard capture={capture} />
                    )}
                    {isReviewable && (
                        <ReviewForm
                            capture={capture}
                            examTypes={examTypes}
                            patients={patients}
                            histories={histories}
                            samples={samples}
                            linkableExams={linkableExams}
                        />
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
