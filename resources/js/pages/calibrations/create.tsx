import { Link, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Machine, PageProps } from '@/types';

interface CalibrationCreateProps extends Record<string, unknown> {
    machine: Machine;
}

function today(): string {
    return new Date().toISOString().slice(0, 10);
}

export default function CalibrationCreate() {
    const { machine } = usePage<PageProps<CalibrationCreateProps>>().props;

    const { data, setData, post, processing, errors } = useForm({
        machine_id: String(machine.id),
        calibration_date: today(),
        value: '',
        observation: '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        post(route('calibrations.store'));
    }

    return (
        <AppLayout title={`Nova Calibração: ${machine.name}`}>
            <Card className="max-w-2xl">
                <CardContent>
                    <p className="mb-4 text-sm text-muted-foreground">
                        O status (aprovada ou rejeitada) é calculado
                        automaticamente conforme a faixa de calibração do
                        equipamento.
                    </p>
                    <form onSubmit={submit} className="space-y-4">
                        <FormField
                            id="calibration_date"
                            label="Data da calibração"
                            error={errors.calibration_date}
                            required
                        >
                            <Input
                                id="calibration_date"
                                type="date"
                                value={data.calibration_date}
                                onChange={(e) =>
                                    setData('calibration_date', e.target.value)
                                }
                            />
                        </FormField>

                        <FormField
                            id="value"
                            label="Valor medido"
                            error={errors.value}
                            required
                        >
                            <Input
                                id="value"
                                type="number"
                                step="any"
                                value={data.value}
                                onChange={(e) =>
                                    setData('value', e.target.value)
                                }
                                autoFocus
                            />
                        </FormField>

                        <FormField
                            id="observation"
                            label="Observações"
                            error={errors.observation}
                        >
                            <Textarea
                                id="observation"
                                value={data.observation}
                                onChange={(e) =>
                                    setData('observation', e.target.value)
                                }
                                rows={4}
                            />
                        </FormField>

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('machines.show', machine.id)}>
                                    Cancelar
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Registrar calibração
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
