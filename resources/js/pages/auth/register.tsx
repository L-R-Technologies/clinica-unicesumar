import { Link, useForm } from '@inertiajs/react';
import { useState, type FormEvent, type ReactElement } from 'react';
import AuthLayout from '@/layouts/auth-layout';
import { AddressFields } from '@/components/address-fields';
import { FormField } from '@/components/form-field';
import { MaskedInput } from '@/components/masked-input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { fetchAddressByCep, stripDigits } from '@/lib/masks';

type RegisterForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    birthday: string;
    sex: string;
    cpf: string;
    rg: string;
    ethnicity: string;
    phone: string;
    zip_code: string;
    street: string;
    number: string;
    neighborhood: string;
    complement: string;
    city: string;
    state: string;
    country: string;
    lgpd_consent: boolean;
};

type RegisterFieldName = keyof RegisterForm;
type RegisterErrors = Partial<Record<RegisterFieldName, string>>;
type SetRegisterField = <K extends RegisterFieldName>(
    field: K,
    value: RegisterForm[K],
) => void;

const STEP_FIELDS: Record<number, RegisterFieldName[]> = {
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

interface StepProps {
    data: RegisterForm;
    errors: RegisterErrors;
    setField: SetRegisterField;
}

function AccountStep({ data, errors, setField }: StepProps): ReactElement {
    return (
        <>
            <FormField id="name" label="Nome completo" error={errors.name}>
                <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setField('name', e.target.value)}
                    autoFocus
                />
            </FormField>
            <FormField id="email" label="E-mail" error={errors.email}>
                <Input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setField('email', e.target.value)}
                />
            </FormField>
            <FormField id="password" label="Senha" error={errors.password}>
                <Input
                    id="password"
                    type="password"
                    value={data.password}
                    onChange={(e) => setField('password', e.target.value)}
                />
            </FormField>
            <FormField
                id="password_confirmation"
                label="Confirmar senha"
                error={errors.password_confirmation}
            >
                <Input
                    id="password_confirmation"
                    type="password"
                    value={data.password_confirmation}
                    onChange={(e) =>
                        setField('password_confirmation', e.target.value)
                    }
                />
            </FormField>
        </>
    );
}

function PersonalDataStep({ data, errors, setField }: StepProps): ReactElement {
    return (
        <>
            <FormField
                id="birthday"
                label="Data de nascimento"
                error={errors.birthday}
            >
                <Input
                    id="birthday"
                    type="date"
                    value={data.birthday}
                    onChange={(e) => setField('birthday', e.target.value)}
                />
            </FormField>
            <FormField id="sex" label="Sexo" error={errors.sex}>
                <Select
                    value={data.sex}
                    onValueChange={(value) => setField('sex', value)}
                >
                    <SelectTrigger id="sex">
                        <SelectValue placeholder="Selecione" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="male">Masculino</SelectItem>
                        <SelectItem value="female">Feminino</SelectItem>
                        <SelectItem value="other">Outro</SelectItem>
                    </SelectContent>
                </Select>
            </FormField>
            <FormField id="cpf" label="CPF" error={errors.cpf}>
                <MaskedInput
                    id="cpf"
                    mask="cpf"
                    value={data.cpf}
                    onValueChange={(value) => setField('cpf', value)}
                    placeholder="000.000.000-00"
                />
            </FormField>
            <FormField id="rg" label="RG" error={errors.rg}>
                <Input
                    id="rg"
                    value={data.rg}
                    onChange={(e) => setField('rg', e.target.value)}
                />
            </FormField>
            <FormField id="ethnicity" label="Etnia" error={errors.ethnicity}>
                <Input
                    id="ethnicity"
                    value={data.ethnicity}
                    onChange={(e) => setField('ethnicity', e.target.value)}
                />
            </FormField>
            <FormField id="phone" label="Telefone" error={errors.phone}>
                <MaskedInput
                    id="phone"
                    mask="phone"
                    value={data.phone}
                    onValueChange={(value) => setField('phone', value)}
                    placeholder="(00) 00000-0000"
                />
            </FormField>
        </>
    );
}

interface AddressStepProps extends StepProps {
    onCepBlur: () => void;
}

function AddressStep({
    data,
    errors,
    setField,
    onCepBlur,
}: AddressStepProps): ReactElement {
    return (
        <>
            <AddressFields
                values={{
                    zip_code: data.zip_code,
                    street: data.street,
                    number: data.number,
                    complement: data.complement,
                    neighborhood: data.neighborhood,
                    city: data.city,
                    state: data.state,
                    country: data.country,
                }}
                errors={errors}
                onChange={(field, value) => setField(field, value)}
                onCepBlur={onCepBlur}
            />
            <label className="flex items-start gap-2 text-sm">
                <Checkbox
                    checked={data.lgpd_consent}
                    onCheckedChange={(checked) =>
                        setField('lgpd_consent', checked === true)
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
            {errors.lgpd_consent && (
                <p className="text-sm text-destructive">
                    {errors.lgpd_consent}
                </p>
            )}
        </>
    );
}

export default function Register(): ReactElement {
    const [step, setStep] = useState(1);
    const { data, setData, post, processing, errors, transform } =
        useForm<RegisterForm>({
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

    const setField: SetRegisterField = (field, value) =>
        setData((previous) => ({ ...previous, [field]: value }));

    function goToFirstStepWithError(fieldErrors: Record<string, string>): void {
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

    async function handleCepBlur(): Promise<void> {
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

    function submit(event: FormEvent): void {
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
                    <AccountStep
                        data={data}
                        errors={errors}
                        setField={setField}
                    />
                )}

                {step === 2 && (
                    <PersonalDataStep
                        data={data}
                        errors={errors}
                        setField={setField}
                    />
                )}

                {step === 3 && (
                    <AddressStep
                        data={data}
                        errors={errors}
                        setField={setField}
                        onCepBlur={handleCepBlur}
                    />
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
