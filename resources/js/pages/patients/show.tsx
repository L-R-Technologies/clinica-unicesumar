import { Link, usePage } from '@inertiajs/react';
import { FileText, Pencil } from 'lucide-react';
import type { ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { Badge } from '@/components/ui/badge';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatDate, formatDateTime } from '@/lib/format';
import { formatCep, formatCpf, formatPhone } from '@/lib/masks';
import { cn } from '@/lib/utils';
import type { PageProps, Patient } from '@/types';

const EMPTY_PLACEHOLDER = '—';

interface PatientsShowProps extends Record<string, unknown> {
    patient: Patient;
    sexOptions: Record<string, string>;
}

function DetailItem({
    label,
    value,
}: {
    label: string;
    value: string | null | undefined;
}): ReactElement {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value || EMPTY_PLACEHOLDER}</p>
        </div>
    );
}

export default function PatientsShow(): ReactElement {
    const { patient, sexOptions } =
        usePage<PageProps<PatientsShowProps>>().props;
    const address = patient.address;
    const hasLgpdTerm = patient.lgpd_term_path !== null;

    return (
        <AppLayout
            title={patient.user?.name ?? 'Paciente'}
            actions={
                <>
                    <BackButton href={route('patients.index')} />
                    <Button asChild>
                        <Link href={route('patients.edit', patient.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                </>
            }
        >
            <div className="mx-auto w-full max-w-3xl space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados pessoais</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <DetailItem label="Nome" value={patient.user?.name} />
                        <DetailItem
                            label="E-mail"
                            value={patient.user?.email}
                        />
                        <DetailItem
                            label="Data de nascimento"
                            value={formatDate(patient.birthday)}
                        />
                        <DetailItem
                            label="Sexo"
                            value={patient.sex ? sexOptions[patient.sex] : null}
                        />
                        <DetailItem
                            label="CPF"
                            value={patient.cpf ? formatCpf(patient.cpf) : null}
                        />
                        <DetailItem label="RG" value={patient.rg} />
                        <DetailItem label="Etnia" value={patient.ethnicity} />
                        <DetailItem
                            label="Telefone"
                            value={
                                patient.phone
                                    ? formatPhone(patient.phone)
                                    : null
                            }
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Endereço</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <DetailItem
                            label="CEP"
                            value={
                                address?.zip_code
                                    ? formatCep(address.zip_code)
                                    : null
                            }
                        />
                        <DetailItem label="Rua" value={address?.street} />
                        <DetailItem label="Número" value={address?.number} />
                        <DetailItem
                            label="Complemento"
                            value={address?.complement}
                        />
                        <DetailItem
                            label="Bairro"
                            value={address?.neighborhood}
                        />
                        <DetailItem label="Cidade" value={address?.city} />
                        <DetailItem label="Estado" value={address?.state} />
                        <DetailItem label="País" value={address?.country} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Termo de consentimento (LGPD)</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <DetailItem
                            label="Consentimento registrado em"
                            value={formatDateTime(patient.lgpd_consent_at)}
                        />
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Termo assinado
                            </p>
                            <Badge
                                variant={
                                    hasLgpdTerm ? 'secondary' : 'destructive'
                                }
                            >
                                {hasLgpdTerm ? 'Anexado' : 'Não anexado'}
                            </Badge>
                        </div>
                        {hasLgpdTerm && (
                            <div className="sm:col-span-2">
                                <a
                                    href={route(
                                        'patients.lgpd-term',
                                        patient.id,
                                    )}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className={cn(
                                        buttonVariants({ variant: 'outline' }),
                                    )}
                                >
                                    <FileText />
                                    Ver termo assinado
                                </a>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
