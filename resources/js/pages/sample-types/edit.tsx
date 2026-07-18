import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
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
        <AppLayout title="Editar Tipo de Amostra">
            <Card className="max-w-2xl">
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
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

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('sample-type.index')}>
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
