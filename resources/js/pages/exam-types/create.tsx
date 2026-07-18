import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import {
    ExamTypeFieldsRepeater,
    createEmptyExamTypeField,
    type ExamTypeFieldRow,
} from '@/components/exam-type-fields-repeater';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

interface ExamTypeForm {
    name: string;
    description: string;
    fields: ExamTypeFieldRow[];
}

export default function ExamTypeCreate() {
    const { data, setData, post, processing, errors, transform } =
        useForm<ExamTypeForm>({
            name: '',
            description: '',
            fields: [createEmptyExamTypeField()],
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
        post(route('exam-type.store'));
    }

    return (
        <AppLayout title="Novo Tipo de Exame"
            actions={
                <>
                    <BackButton
                        href={route('exam-type.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        Salvar
                    </Button>
                </>
            }>
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form id="resource-form" onSubmit={submit} className="space-y-6">
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

                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
