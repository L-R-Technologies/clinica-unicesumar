import { FileText } from 'lucide-react';
import type { ReactElement } from 'react';
import { AddressFields } from '@/components/address-fields';
import { FormField } from '@/components/form-field';
import { MaskedInput } from '@/components/masked-input';
import { buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { fetchAddressByCep } from '@/lib/masks';
import { cn } from '@/lib/utils';
import type {
    PatientFormData,
    PatientFormErrors,
    SetPatientFormField,
} from './types';

const LGPD_TERM_ACCEPT = '.jpg,.jpeg,.png,.webp,.pdf';
const LGPD_TERM_HINT =
    'Foto ou PDF do termo assinado pelo paciente (JPG, PNG, WEBP ou PDF, até 5 MB).';
const DEFAULT_COUNTRY = 'Brasil';

interface PatientFormFieldsProps {
    data: PatientFormData;
    errors: PatientFormErrors;
    setField: SetPatientFormField;
    sexOptions: Record<string, string>;
    isEditing?: boolean;
    /** URL do termo já anexado (apenas na edição). */
    currentTermUrl?: string | null;
}

export function PatientFormFields({
    data,
    errors,
    setField,
    sexOptions,
    isEditing = false,
    currentTermUrl = null,
}: PatientFormFieldsProps): ReactElement {
    async function handleCepBlur(): Promise<void> {
        const address = await fetchAddressByCep(data.zip_code);

        if (!address) {
            return;
        }

        if (address.street) {
            setField('street', address.street);
        }
        if (address.neighborhood) {
            setField('neighborhood', address.neighborhood);
        }
        if (address.city) {
            setField('city', address.city);
        }
        if (address.state) {
            setField('state', address.state);
        }
        if (!data.country) {
            setField('country', DEFAULT_COUNTRY);
        }
    }

    return (
        <>
            <Card>
                <CardHeader>
                    <CardTitle>Conta de acesso</CardTitle>
                    <CardDescription>
                        {isEditing
                            ? 'O paciente usa este e-mail para acessar os próprios exames.'
                            : 'Uma senha temporária será gerada e enviada para este e-mail assim que o cadastro for salvo.'}
                    </CardDescription>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <FormField
                        id="name"
                        label="Nome completo"
                        error={errors.name}
                        required
                    >
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setField('name', e.target.value)}
                            autoFocus
                        />
                    </FormField>

                    <FormField
                        id="email"
                        label="E-mail"
                        error={errors.email}
                        required
                    >
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setField('email', e.target.value)}
                        />
                    </FormField>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Dados pessoais</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-4 sm:grid-cols-2">
                    <FormField
                        id="birthday"
                        label="Data de nascimento"
                        error={errors.birthday}
                        required
                    >
                        <Input
                            id="birthday"
                            type="date"
                            value={data.birthday}
                            onChange={(e) =>
                                setField('birthday', e.target.value)
                            }
                        />
                    </FormField>

                    <FormField
                        id="sex"
                        label="Sexo"
                        error={errors.sex}
                        required
                    >
                        <Select
                            value={data.sex}
                            onValueChange={(value) => setField('sex', value)}
                        >
                            <SelectTrigger id="sex" className="w-full">
                                <SelectValue placeholder="Selecione" />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.entries(sexOptions).map(
                                    ([value, label]) => (
                                        <SelectItem key={value} value={value}>
                                            {label}
                                        </SelectItem>
                                    ),
                                )}
                            </SelectContent>
                        </Select>
                    </FormField>

                    <FormField id="cpf" label="CPF" error={errors.cpf} required>
                        <MaskedInput
                            id="cpf"
                            mask="cpf"
                            value={data.cpf}
                            onValueChange={(value) => setField('cpf', value)}
                            placeholder="000.000.000-00"
                        />
                    </FormField>

                    <FormField id="rg" label="RG" error={errors.rg} required>
                        <Input
                            id="rg"
                            value={data.rg}
                            onChange={(e) => setField('rg', e.target.value)}
                        />
                    </FormField>

                    <FormField
                        id="ethnicity"
                        label="Etnia"
                        error={errors.ethnicity}
                    >
                        <Input
                            id="ethnicity"
                            value={data.ethnicity}
                            onChange={(e) =>
                                setField('ethnicity', e.target.value)
                            }
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
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Endereço</CardTitle>
                </CardHeader>
                <CardContent>
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
                        onCepBlur={handleCepBlur}
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Termo de consentimento (LGPD)</CardTitle>
                    <CardDescription>{LGPD_TERM_HINT}</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    {currentTermUrl && (
                        <a
                            href={currentTermUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className={cn(
                                buttonVariants({ variant: 'outline' }),
                            )}
                        >
                            <FileText />
                            Ver termo atual
                        </a>
                    )}

                    <FormField
                        id="lgpd_term"
                        label={
                            isEditing
                                ? 'Substituir termo assinado'
                                : 'Termo assinado'
                        }
                        error={errors.lgpd_term}
                        required={!isEditing}
                    >
                        <Input
                            id="lgpd_term"
                            type="file"
                            accept={LGPD_TERM_ACCEPT}
                            onChange={(e) =>
                                setField(
                                    'lgpd_term',
                                    e.target.files?.[0] ?? null,
                                )
                            }
                        />
                    </FormField>
                </CardContent>
            </Card>
        </>
    );
}
