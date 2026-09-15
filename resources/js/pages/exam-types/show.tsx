import { Link, usePage } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import type { ReactElement } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BackButton } from '@/components/back-button';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DataTable, type Column } from '@/components/data-table';
import { ActiveBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type {
    ExamFieldType,
    ExamType,
    ExamTypeField,
    ExamTypeFieldReference,
    PageProps,
} from '@/types';

interface ExamTypeShowProps extends Record<string, unknown> {
    examType: ExamType;
}

const FIELD_TYPE_LABELS: Record<ExamFieldType, string> = {
    int: 'Inteiro',
    float: 'Decimal',
    string: 'Texto',
    boolean: 'Booleano',
};

function ReferenceList({
    references,
}: {
    references: ExamTypeFieldReference[];
}): ReactElement {
    if (references.length === 0) {
        return <span className="text-muted-foreground">—</span>;
    }

    return (
        <ul className="space-y-1">
            {references.map((reference) => (
                <li key={reference.id}>
                    <span className="font-medium">{reference.range_label}</span>
                    <span className="ml-1 text-muted-foreground">
                        ({reference.criteria_label})
                    </span>
                </li>
            ))}
        </ul>
    );
}

export default function ExamTypeShow() {
    const { examType } = usePage<PageProps<ExamTypeShowProps>>().props;

    const fieldColumns: Column<ExamTypeField>[] = [
        { header: 'Nome', cell: (row) => row.name },
        { header: 'Rótulo', cell: (row) => row.label },
        {
            header: 'Tipo',
            cell: (row) => FIELD_TYPE_LABELS[row.field_type],
        },
        {
            header: 'Unidade',
            cell: (row) => row.unit || '—',
            className: 'text-muted-foreground',
        },
        {
            header: 'Valores de referência',
            cell: (row) => <ReferenceList references={row.references ?? []} />,
        },
    ];

    return (
        <AppLayout
            title={examType.name}
            actions={
                <>
                    <BackButton href={route('exam-type.index')} />
                    <Button asChild>
                        <Link href={route('exam-type.edit', examType.id)}>
                            <Pencil />
                            Editar
                        </Link>
                    </Button>
                    <ConfirmDeleteDialog
                        action={route('exam-type.destroy', examType.id)}
                        title="Excluir tipo de exame"
                        description="O tipo de exame será removido permanentemente."
                        trigger={
                            <Button variant="destructive">
                                <Trash2 />
                                Excluir
                            </Button>
                        }
                    />
                </>
            }
        >
            <div className="space-y-6">
                <Card className="mx-auto w-full max-w-2xl">
                    <CardContent className="space-y-4">
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Nome
                            </p>
                            <p className="font-medium">{examType.name}</p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Descrição
                            </p>
                            <p className="font-medium">
                                {examType.description || '—'}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Status
                            </p>
                            <ActiveBadge active={examType.is_active} />
                        </div>
                    </CardContent>
                </Card>

                <div className="space-y-2">
                    <h2 className="text-sm font-medium text-muted-foreground">
                        Campos personalizados
                    </h2>
                    <DataTable
                        columns={fieldColumns}
                        rows={examType.fields ?? []}
                        getRowKey={(row) => row.id}
                        emptyMessage="Nenhum campo personalizado cadastrado."
                    />
                </div>
            </div>
        </AppLayout>
    );
}
