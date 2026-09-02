import { useForm, usePage } from '@inertiajs/react';
import { Loader2, ShieldCheck, Sparkles } from 'lucide-react';
import type { FormEvent, ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { ExamType, PageProps } from '@/types';

interface HealthCampaignsCreateProps extends Record<string, unknown> {
    examTypes: Pick<ExamType, 'id' | 'name'>[];
    maxPatients: number;
}

type CampaignFiltersForm = {
    date_from: string;
    date_to: string;
    exam_type_ids: number[];
};

export default function HealthCampaignsCreate(): ReactElement {
    const { examTypes, maxPatients } =
        usePage<PageProps<HealthCampaignsCreateProps>>().props;

    const { data, setData, post, processing, errors } =
        useForm<CampaignFiltersForm>({
            date_from: '',
            date_to: '',
            exam_type_ids: [],
        });

    function toggleExamType(examTypeId: number, checked: boolean): void {
        setData(
            'exam_type_ids',
            checked
                ? [...data.exam_type_ids, examTypeId]
                : data.exam_type_ids.filter((id) => id !== examTypeId),
        );
    }

    function submit(event: FormEvent): void {
        event.preventDefault();
        post(route('health-campaigns.store'));
    }

    return (
        <AppLayout
            title="Gerar Campanha de Saúde"
            actions={
                <>
                    <BackButton
                        href={route('health-campaigns.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        {processing ? (
                            <Loader2 className="animate-spin" />
                        ) : (
                            <Sparkles />
                        )}
                        {processing ? 'Gerando...' : 'Gerar com IA'}
                    </Button>
                </>
            }
        >
            <form
                id="resource-form"
                onSubmit={submit}
                className="mx-auto w-full max-w-3xl space-y-6"
            >
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <ShieldCheck className="size-5 text-emerald-600 dark:text-emerald-400" />
                            Como funciona
                        </CardTitle>
                        <CardDescription>
                            A IA recebe apenas dados anonimizados dos exames
                            aprovados: sexo, faixa etária e os resultados com
                            seus valores de referência. Nome, CPF, e-mail,
                            endereço e datas exatas nunca são enviados. Com isso
                            ela identifica o principal problema de saúde da
                            comunidade e propõe uma campanha. A base é limitada
                            a {maxPatients} pacientes por análise.
                        </CardDescription>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Recorte dos exames</CardTitle>
                        <CardDescription>
                            Deixe em branco para usar todos os exames aprovados.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="date_from"
                                label="Exames a partir de"
                                error={errors.date_from}
                            >
                                <Input
                                    id="date_from"
                                    type="date"
                                    value={data.date_from}
                                    onChange={(e) =>
                                        setData('date_from', e.target.value)
                                    }
                                />
                            </FormField>
                            <FormField
                                id="date_to"
                                label="Exames até"
                                error={errors.date_to}
                            >
                                <Input
                                    id="date_to"
                                    type="date"
                                    value={data.date_to}
                                    onChange={(e) =>
                                        setData('date_to', e.target.value)
                                    }
                                />
                            </FormField>
                        </div>

                        <div className="space-y-2">
                            <Label>Tipos de exame (opcional)</Label>
                            {errors.exam_type_ids && (
                                <p className="text-sm text-destructive">
                                    {errors.exam_type_ids}
                                </p>
                            )}
                            <div className="grid gap-2 sm:grid-cols-2">
                                {examTypes.map((examType) => (
                                    <label
                                        key={examType.id}
                                        className="flex items-center gap-2 text-sm"
                                    >
                                        <Checkbox
                                            checked={data.exam_type_ids.includes(
                                                examType.id,
                                            )}
                                            onCheckedChange={(checked) =>
                                                toggleExamType(
                                                    examType.id,
                                                    checked === true,
                                                )
                                            }
                                        />
                                        {examType.name}
                                    </label>
                                ))}
                            </div>
                        </div>

                        {processing && (
                            <p className="text-sm text-muted-foreground">
                                A geração consulta a IA duas vezes (análise e
                                campanha) e pode levar até um minuto. Mantenha
                                esta página aberta.
                            </p>
                        )}
                    </CardContent>
                </Card>
            </form>
        </AppLayout>
    );
}
