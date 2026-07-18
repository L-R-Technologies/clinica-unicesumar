import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import {
    ExamTypeFieldsRepeater,
    type ExamTypeFieldRow,
} from '@/components/exam-type-fields-repeater';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { ExamType, PageProps } from '@/types';

interface ExamTypeEditProps extends Record<string, unknown> {
    examType: ExamType;
}

interface ExamTypeForm {
    name: string;
    description: string;
    fields: ExamTypeFieldRow[];
}

export default function ExamTypeEdit() {
    const { examType } = usePage<PageProps<ExamTypeEditProps>>().props;

    const { data, setData, put, processing, errors, transform } =
        useForm<ExamTypeForm>({
            name: examType.name,
            description: examType.description ?? '',
            fields: (examType.fields ?? []).map((field) => ({
                id: field.id,
                name: field.name,
                label: field.label,
                field_type: field.field_type,
                unit: field.unit ?? '',
            })),
        });

    function submit(event: FormEvent) {
        event.preventDefault();
        // Ignora linhas totalmente vazias antes de enviar.
        transform((current) => ({
            ...current,
            fields: current.fields.filter(
                (field) => field.name !== '' || field.label !== '',
            ),
        }));
        put(route('exam-type.update', examType.id));
    }

    return (
        <AppLayout title="Editar Tipo de Exame">
            <Card className="max-w-2xl">
                <CardContent>
                    <form onSubmit={submit} className="space-y-6">
                        <FormField
                            id="name"
                            label="Nome"
                            error={errors.name}
                            required
                        >
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                autoFocus
                            />
                        </FormField>

                        <FormField
                            id="description"
                            label="Descrição"
                            error={errors.description}
                        >
                            <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) =>
                                    setData('description', e.target.value)
                                }
                                rows={4}
                            />
                        </FormField>

                        <ExamTypeFieldsRepeater
                            fields={data.fields}
                            onChange={(fields) => setData('fields', fields)}
                            errors={errors}
                        />

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('exam-type.index')}>
                                    Cancelar
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Salvar alterações
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
