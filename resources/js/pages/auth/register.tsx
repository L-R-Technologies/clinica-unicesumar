import { Link, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';
import {
    fetchAddressByCep,
    formatCep,
    formatCpf,
    formatPhone,
    stripDigits,
} from '@/lib/masks';

const STEP_FIELDS: Record<number, string[]> = {
    1: ['name', 'email', 'password', 'password_confirmation'],
    2: ['birthday', 'sex', 'cpf', 'rg', 'ethnicity', 'phone'],
    3: [
        'zip_code',
        'street',
        'number',
        'neighborhood',
        'complement',
        'city',
        'state',
        'country',
        'lgpd_consent',
    ],
};

const STEP_LABELS = ['Conta', 'Dados pessoais', 'Endereço'] as const;
const TOTAL_STEPS = STEP_LABELS.length;

function FieldError({ message }: { message?: string }) {
    if (!message) {
        return null;
    }

    return <p className="text-sm text-destructive">{message}</p>;
}

export default function Register() {
    const [step, setStep] = useState(1);
    const { data, setData, post, processing, errors, transform } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        birthday: '',
        sex: '',
        cpf: '',
        rg: '',
        ethnicity: '',
        phone: '',
        zip_code: '',
        street: '',
        number: '',
        neighborhood: '',
        complement: '',
        city: '',
        state: '',
        country: '',
        lgpd_consent: false,
    });

    function goToFirstStepWithError(fieldErrors: Record<string, string>) {
        for (let currentStep = 1; currentStep <= TOTAL_STEPS; currentStep++) {
            const hasError = STEP_FIELDS[currentStep].some(
                (field) => fieldErrors[field],
            );
            if (hasError) {
                setStep(currentStep);
                return;
            }
        }
    }

    async function handleCepBlur() {
        const address = await fetchAddressByCep(data.zip_code);
        if (address) {
            setData((previous) => ({
                ...previous,
                street: address.street || previous.street,
                neighborhood: address.neighborhood || previous.neighborhood,
                city: address.city || previous.city,
                state: address.state || previous.state,
                country: previous.country || 'Brasil',
            }));
        }
    }

    function submit(event: FormEvent) {
        event.preventDefault();

        transform((formData) => ({
            ...formData,
            cpf: stripDigits(formData.cpf),
            phone: stripDigits(formData.phone),
            zip_code: stripDigits(formData.zip_code),
        }));

        post(route('register'), {
            onError: (formErrors) => goToFirstStepWithError(formErrors),
        });
    }

    return (
        <AuthLayout
            title="Cadastro de Paciente"
            description={`Passo ${step} de ${TOTAL_STEPS} — ${STEP_LABELS[step - 1]}`}
        >
            <div className="mb-6 flex gap-2">
                {STEP_LABELS.map((label, index) => (
                    <div
                        key={label}
                        className={cn(
                            'h-1.5 flex-1 rounded-full',
                            index + 1 <= step ? 'bg-primary' : 'bg-muted',
                        )}
                    />
                ))}
            </div>

            <form onSubmit={submit} className="space-y-4">
                {step === 1 && (
                    <>
                        <div className="space-y-2">
                            <Label htmlFor="name">Nome completo</Label>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                autoFocus
                            />
                            <FieldError message={errors.name} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="email">E-mail</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />
                            <FieldError message={errors.email} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="password">Senha</Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) =>
                                    setData('password', e.target.value)
                                }
                            />
                            <FieldError message={errors.password} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="password_confirmation">
                                Confirmar senha
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                value={data.password_confirmation}
                                onChange={(e) =>
                                    setData(
                                        'password_confirmation',
                                        e.target.value,
                                    )
                                }
                            />
                        </div>
                    </>
                )}

                {step === 2 && (
                    <>
                        <div className="space-y-2">
                            <Label htmlFor="birthday">
                                Data de nascimento
                            </Label>
                            <Input
                                id="birthday"
                                type="date"
                                value={data.birthday}
                                onChange={(e) =>
                                    setData('birthday', e.target.value)
                                }
                            />
                            <FieldError message={errors.birthday} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="sex">Sexo</Label>
                            <Select
                                value={data.sex}
                                onValueChange={(value) => setData('sex', value)}
                            >
                                <SelectTrigger id="sex">
                                    <SelectValue placeholder="Selecione" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="male">
                                        Masculino
                                    </SelectItem>
                                    <SelectItem value="female">
                                        Feminino
                                    </SelectItem>
                                    <SelectItem value="other">Outro</SelectItem>
                                </SelectContent>
                            </Select>
                            <FieldError message={errors.sex} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="cpf">CPF</Label>
                            <Input
                                id="cpf"
                                inputMode="numeric"
                                value={data.cpf}
                                onChange={(e) =>
                                    setData('cpf', formatCpf(e.target.value))
                                }
                                placeholder="000.000.000-00"
                            />
                            <FieldError message={errors.cpf} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="rg">RG</Label>
                            <Input
                                id="rg"
                                value={data.rg}
                                onChange={(e) => setData('rg', e.target.value)}
                            />
                            <FieldError message={errors.rg} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="ethnicity">Etnia</Label>
                            <Input
                                id="ethnicity"
                                value={data.ethnicity}
                                onChange={(e) =>
                                    setData('ethnicity', e.target.value)
                                }
                            />
                            <FieldError message={errors.ethnicity} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="phone">Telefone</Label>
                            <Input
                                id="phone"
                                inputMode="numeric"
                                value={data.phone}
                                onChange={(e) =>
                                    setData(
                                        'phone',
                                        formatPhone(e.target.value),
                                    )
                                }
                                placeholder="(00) 00000-0000"
                            />
                            <FieldError message={errors.phone} />
                        </div>
                    </>
                )}

                {step === 3 && (
                    <>
                        <div className="space-y-2">
                            <Label htmlFor="zip_code">CEP</Label>
                            <Input
                                id="zip_code"
                                inputMode="numeric"
                                value={data.zip_code}
                                onChange={(e) =>
                                    setData(
                                        'zip_code',
                                        formatCep(e.target.value),
                                    )
                                }
                                onBlur={handleCepBlur}
                                placeholder="00000-000"
                            />
                            <FieldError message={errors.zip_code} />
                        </div>
                        <div className="grid grid-cols-3 gap-3">
                            <div className="col-span-2 space-y-2">
                                <Label htmlFor="street">Rua</Label>
                                <Input
                                    id="street"
                                    value={data.street}
                                    onChange={(e) =>
                                        setData('street', e.target.value)
                                    }
                                />
                                <FieldError message={errors.street} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="number">Número</Label>
                                <Input
                                    id="number"
                                    value={data.number}
                                    onChange={(e) =>
                                        setData('number', e.target.value)
                                    }
                                />
                                <FieldError message={errors.number} />
                            </div>
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="neighborhood">Bairro</Label>
                            <Input
                                id="neighborhood"
                                value={data.neighborhood}
                                onChange={(e) =>
                                    setData('neighborhood', e.target.value)
                                }
                            />
                            <FieldError message={errors.neighborhood} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="complement">
                                Complemento (opcional)
                            </Label>
                            <Input
                                id="complement"
                                value={data.complement}
                                onChange={(e) =>
                                    setData('complement', e.target.value)
                                }
                            />
                            <FieldError message={errors.complement} />
                        </div>
                        <div className="grid grid-cols-3 gap-3">
                            <div className="space-y-2">
                                <Label htmlFor="city">Cidade</Label>
                                <Input
                                    id="city"
                                    value={data.city}
                                    onChange={(e) =>
                                        setData('city', e.target.value)
                                    }
                                />
                                <FieldError message={errors.city} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="state">Estado</Label>
                                <Input
                                    id="state"
                                    value={data.state}
                                    onChange={(e) =>
                                        setData('state', e.target.value)
                                    }
                                />
                                <FieldError message={errors.state} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="country">País</Label>
                                <Input
                                    id="country"
                                    value={data.country}
                                    onChange={(e) =>
                                        setData('country', e.target.value)
                                    }
                                />
                                <FieldError message={errors.country} />
                            </div>
                        </div>
                        <label className="flex items-start gap-2 text-sm">
                            <Checkbox
                                checked={data.lgpd_consent}
                                onCheckedChange={(checked) =>
                                    setData('lgpd_consent', checked === true)
                                }
                                className="mt-0.5"
                            />
                            <span>
                                Li e aceito os termos da{' '}
                                <Link
                                    href={route('privacy-policy')}
                                    target="_blank"
                                    className="text-primary hover:underline"
                                >
                                    Política de Privacidade (LGPD)
                                </Link>
                                .
                            </span>
                        </label>
                        <FieldError message={errors.lgpd_consent} />
                    </>
                )}

                <div className="flex justify-between gap-2 pt-2">
                    {step > 1 ? (
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setStep((s) => s - 1)}
                        >
                            Voltar
                        </Button>
                    ) : (
                        <span />
                    )}

                    {step < TOTAL_STEPS ? (
                        <Button
                            type="button"
                            onClick={() => setStep((s) => s + 1)}
                        >
                            Próximo
                        </Button>
                    ) : (
                        <Button type="submit" disabled={processing}>
                            Finalizar cadastro
                        </Button>
                    )}
                </div>

                <p className="text-center text-sm text-muted-foreground">
                    Já tem conta?{' '}
                    <Link
                        href={route('login')}
                        className="text-primary hover:underline"
                    >
                        Entrar
                    </Link>
                </p>
            </form>
        </AuthLayout>
    );
}
