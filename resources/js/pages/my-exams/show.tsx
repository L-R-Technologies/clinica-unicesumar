import { usePage, useForm } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ExamResultsCard } from '@/components/exam-results-card';
import { RatingScale, RatingScaleReadOnly } from '@/components/rating-scale';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { Exam, PageProps, ResultReference } from '@/types';

interface MyExamsShowProps extends Record<string, unknown> {
    exam: Exam;
    resultReferences: Record<string, ResultReference>;
}

const FEEDBACK_QUESTIONS = [
    {
        key: 'clarity',
        question: 'Como avalia a clareza das explicações do profissional?',
    },
    {
        key: 'cordiality',
        question: 'Como avalia a cordialidade da equipe?',
    },
    {
        key: 'waiting_time',
        question: 'Como avalia o tempo de espera?',
    },
    {
        key: 'result_speed',
        question: 'Como avalia a agilidade na entrega do resultado?',
    },
    {
        key: 'confidence',
        question: 'Como avalia a confiança transmitida durante o exame?',
    },
] as const;

function DetailItem({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value}</p>
        </div>
    );
}

export default function MyExamsShow() {
    const { exam, resultReferences } =
        usePage<PageProps<MyExamsShowProps>>().props;

    return (
        <AppLayout
            title="Detalhes do Exame"
            actions={
                <div className="flex flex-wrap items-center gap-2">
                    <BackButton href={route('patient-exams.index')} />
                    <a
                        href={route('patient-exams.pdf', exam.id)}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={cn(buttonVariants({ variant: 'outline' }))}
                    >
                        <FileText />
                        Exportar PDF
                    </a>
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
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Amostra</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 gap-4">
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

            <ExamResultsCard exam={exam} resultReferences={resultReferences} />

            <FeedbackSection exam={exam} />
        </AppLayout>
    );
}

function FeedbackSection({ exam }: { exam: Exam }) {
    if (exam.status !== 'approved') {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Feedback</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-sm text-muted-foreground">
                        O feedback estará disponível assim que o exame for
                        aprovado.
                    </p>
                </CardContent>
            </Card>
        );
    }

    const feedback = exam.feedback;

    if (feedback) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Seu Feedback</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    {FEEDBACK_QUESTIONS.map(({ key, question }) => (
                        <RatingScaleReadOnly
                            key={key}
                            question={question}
                            value={feedback[key]}
                        />
                    ))}
                    {feedback.observation && (
                        <div className="space-y-1.5">
                            <p className="text-sm text-muted-foreground">
                                Comentário
                            </p>
                            <p className="whitespace-pre-line">
                                {feedback.observation}
                            </p>
                        </div>
                    )}
                </CardContent>
            </Card>
        );
    }

    return <FeedbackForm examId={exam.id} />;
}

function FeedbackForm({ examId }: { examId: number }) {
    const { data, setData, post, processing, errors } = useForm({
        clarity: 0,
        cordiality: 0,
        waiting_time: 0,
        result_speed: 0,
        confidence: 0,
        observation: '',
    });

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('patient-exams.feedback.store', examId), {
            preserveScroll: true,
        });
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>Deixe seu Feedback</CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="space-y-6">
                    {FEEDBACK_QUESTIONS.map(({ key, question }) => (
                        <RatingScale
                            key={key}
                            id={key}
                            question={question}
                            value={data[key] || null}
                            onChange={(value) => setData(key, value)}
                            error={errors[key]}
                        />
                    ))}

                    <div className="space-y-2">
                        <Label htmlFor="observation">
                            Comentário (opcional)
                        </Label>
                        <Textarea
                            id="observation"
                            value={data.observation}
                            onChange={(e) =>
                                setData('observation', e.target.value)
                            }
                            rows={4}
                            placeholder="Conte um pouco mais sobre sua experiência..."
                        />
                        {errors.observation && (
                            <p className="text-sm text-destructive">
                                {errors.observation}
                            </p>
                        )}
                    </div>

                    <Button type="submit" disabled={processing}>
                        Enviar Feedback
                    </Button>
                </form>
            </CardContent>
        </Card>
    );
}
