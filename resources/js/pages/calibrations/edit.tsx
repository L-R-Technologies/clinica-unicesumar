import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Calibration, PageProps } from '@/types';

interface CalibrationEditProps extends Record<string, unknown> {
    calibration: Calibration;
}

/** Data/hora local no formato aceito pelo input datetime-local (YYYY-MM-DDTHH:mm). */
function nowLocal(): string {
    const now = new Date();
    const pad = (value: number): string => String(value).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
}

export default function CalibrationEdit() {
    const { calibration } = usePage<PageProps<CalibrationEditProps>>().props;
    const maxDateTime = nowLocal();

    const { data, setData, put, processing, errors } = useForm({
        calibration_date: calibration.calibration_date.slice(0, 16),
        value: String(calibration.value),
        observation: calibration.observation ?? '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        put(route('calibrations.update', calibration.id));
    }

    return (
        <AppLayout
            title="Editar Calibração"
            actions={
                <>
                    <BackButton
                        href={route('calibrations.show', calibration.id)}
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
                    <p className="mb-4 text-sm text-muted-foreground">
                        O status é recalculado automaticamente conforme a faixa
                        de calibração do equipamento.
                    </p>
                    <form
                        id="resource-form"
                        onSubmit={submit}
                        className="space-y-4"
                    >
                        <FormField
                            id="calibration_date"
                            label="Data e hora da calibração"
                            error={errors.calibration_date}
                            required
                        >
                            <Input
                                id="calibration_date"
                                type="datetime-local"
                                max={maxDateTime}
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
                                maxLength={1000}
                            />
                        </FormField>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
