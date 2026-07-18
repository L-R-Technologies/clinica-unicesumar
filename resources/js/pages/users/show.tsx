import { Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import type { ReactNode } from 'react';
import AppLayout from '@/layouts/app-layout';
import { ActiveBadge } from '@/components/status-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PageProps, Student, Teacher, User } from '@/types';

const ROLE_LABELS: Record<string, string> = {
    teacher: 'Professor',
    student: 'Estudante',
    patient: 'Paciente',
};

interface UserShowProps extends Record<string, unknown> {
    user: User & { teacher?: Teacher | null; student?: Student | null };
}

function InfoRow({ label, value }: { label: string; value: ReactNode }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <div className="font-medium">{value}</div>
        </div>
    );
}

export default function UserShow() {
    const { user } = usePage<PageProps<UserShowProps>>().props;

    return (
        <AppLayout
            title={user.name}
            actions={
                <Button asChild>
                    <Link href={route('user-management.edit', user.id)}>
                        <Pencil />
                        Editar
                    </Link>
                </Button>
            }
        >
            <Card className="max-w-2xl">
                <CardContent className="space-y-4">
                    <InfoRow label="Nome" value={user.name} />
                    <InfoRow label="Email" value={user.email} />
                    <InfoRow
                        label="Perfil"
                        value={
                            <Badge variant="secondary">
                                {ROLE_LABELS[user.role] ?? user.role}
                            </Badge>
                        }
                    />
                    <InfoRow
                        label="Status"
                        value={<ActiveBadge active={user.active} />}
                    />

                    {user.role === 'teacher' && user.teacher && (
                        <>
                            <InfoRow
                                label="Número de registro"
                                value={user.teacher.registration_number || '—'}
                            />
                            <InfoRow
                                label="Licença profissional"
                                value={user.teacher.professional_license || '—'}
                            />
                        </>
                    )}

                    {user.role === 'student' && user.student && (
                        <>
                            <InfoRow
                                label="RA"
                                value={user.student.ra || '—'}
                            />
                            <InfoRow
                                label="Curso"
                                value={user.student.course || '—'}
                            />
                            <InfoRow
                                label="Semestre"
                                value={user.student.semester || '—'}
                            />
                        </>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
