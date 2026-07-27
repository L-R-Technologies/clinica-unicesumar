import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { PageProps, SampleType } from '@/types';

interface SampleTypeEditProps extends Record<string, unknown> {
    sampleType: SampleType;
}

export default function SampleTypeEdit() {
    const { sampleType } = usePage<PageProps<SampleTypeEditProps>>().props;
    const { data, setData, put, processing, errors } = useForm({
        name: sampleType.name,
        description: sampleType.description ?? '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        put(route('sample-type.update', sampleType.id));
    }

    return (
        <AppLayout
            title="Editar Tipo de Amostra"
            actions={
                <>
                    <BackButton
                        href={route('sample-type.index')}
                        label="Cancelar"
                    />
                    <Button
                        type="submit"
                        form="resource-form"
                        disabled={processing}
                    >
                        Salvar alterações
                    </Button>
                </>
            }
        >
            <Card className="mx-auto w-full max-w-2xl">
                <CardContent>
                    <form
                        id="resource-form"
                        onSubmit={submit}
                        className="space-y-4"
                    >
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
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
