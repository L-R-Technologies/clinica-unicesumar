import { useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Machine, PageProps } from '@/types';

interface MachineEditProps extends Record<string, unknown> {
    machine: Machine;
    statusOptions: Record<string, string>;
}

function numberToString(value: number | null): string {
    return value === null ? '' : String(value);
}

export default function MachineEdit() {
    const { machine, statusOptions } =
        usePage<PageProps<MachineEditProps>>().props;

    const { data, setData, put, processing, errors } = useForm({
        name: machine.name,
        model: machine.model ?? '',
        serial_number: machine.serial_number ?? '',
        location: machine.location ?? '',
        status: machine.status,
        calibration_range_min: numberToString(machine.calibration_range_min),
        calibration_range_max: numberToString(machine.calibration_range_max),
    });

    function submit(event: FormEvent) {
        event.preventDefault();
        put(route('machines.update', machine.id));
    }

    return (
        <AppLayout
            title="Editar Máquina"
            actions={
                <>
                    <BackButton
                        href={route('machines.index')}
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
                                maxLength={255}
                                autoFocus
                            />
                        </FormField>

                        <FormField
                            id="model"
                            label="Modelo"
                            error={errors.model}
                            required
                        >
                            <Input
                                id="model"
                                value={data.model}
                                onChange={(e) =>
                                    setData('model', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>

                        <FormField
                            id="serial_number"
                            label="Número de série"
                            error={errors.serial_number}
                            required
                        >
                            <Input
                                id="serial_number"
                                value={data.serial_number}
                                onChange={(e) =>
                                    setData('serial_number', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>

                        <FormField
                            id="location"
                            label="Localização"
                            error={errors.location}
                            required
                        >
                            <Input
                                id="location"
                                value={data.location}
                                onChange={(e) =>
                                    setData('location', e.target.value)
                                }
                                maxLength={255}
                            />
                        </FormField>

                        <FormField
                            id="status"
                            label="Status"
                            error={errors.status}
                            required
                        >
                            <Select
                                value={data.status}
                                onValueChange={(value) =>
                                    setData(
                                        'status',
                                        value as Machine['status'],
                                    )
                                }
                            >
                                <SelectTrigger id="status">
                                    <SelectValue placeholder="Selecione o status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.entries(statusOptions).map(
                                        ([value, label]) => (
                                            <SelectItem
                                                key={value}
                                                value={value}
                                            >
                                                {label}
                                            </SelectItem>
                                        ),
                                    )}
                                </SelectContent>
                            </Select>
                        </FormField>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="calibration_range_min"
                                label="Faixa de calibração (mín.)"
                                error={errors.calibration_range_min}
                            >
                                <Input
                                    id="calibration_range_min"
                                    type="number"
                                    step="any"
                                    value={data.calibration_range_min}
                                    onChange={(e) =>
                                        setData(
                                            'calibration_range_min',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id="calibration_range_max"
                                label="Faixa de calibração (máx.)"
                                error={errors.calibration_range_max}
                            >
                                <Input
                                    id="calibration_range_max"
                                    type="number"
                                    step="any"
                                    value={data.calibration_range_max}
                                    onChange={(e) =>
                                        setData(
                                            'calibration_range_max',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
