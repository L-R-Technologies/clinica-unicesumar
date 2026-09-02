import type { ReactElement } from 'react';
import { StatusBadge } from '@/components/status-badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatResultValue } from '@/lib/format';
import type { Exam, ExamTypeField, ResultReference } from '@/types';

const EMPTY_PLACEHOLDER = '—';

interface ExamResultsCardProps {
    exam: Exam;
    /** Referência aplicável a cada parâmetro (chave = nome do campo), já avaliada pelo backend. */
    resultReferences: Record<string, ResultReference>;
}

/**
 * Tabela de resultados do exame com os valores de referência do tipo de exame
 * (filtrados por sexo/idade do paciente) e a situação de cada parâmetro.
 * Compartilhada entre a visão da equipe e a visão do paciente.
 */
export function ExamResultsCard({
    exam,
    resultReferences,
}: ExamResultsCardProps): ReactElement {
    const fields: ExamTypeField[] = exam.exam_type?.fields ?? [];
    const fieldMap = new Map(fields.map((field) => [field.name, field]));
    const resultEntries = exam.results ? Object.entries(exam.results) : [];

    return (
        <Card>
            <CardHeader>
                <CardTitle>Resultados do Exame</CardTitle>
            </CardHeader>
            <CardContent>
                {resultEntries.length > 0 ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Parâmetro</TableHead>
                                <TableHead>Resultado</TableHead>
                                <TableHead>Valores de referência</TableHead>
                                <TableHead>Situação</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {resultEntries.map(([key, value]) => {
                                const field = fieldMap.get(key);
                                const reference: ResultReference | undefined =
                                    resultReferences[key];

                                return (
                                    <TableRow key={key}>
                                        <TableCell className="font-medium">
                                            {field?.label ?? key}
                                            {field?.unit && (
                                                <span className="ml-1 text-muted-foreground">
                                                    ({field.unit})
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {formatResultValue(value)}
                                        </TableCell>
                                        <TableCell>
                                            {reference ? (
                                                <>
                                                    <span>
                                                        {reference.range_label}
                                                    </span>
                                                    <span className="block text-xs text-muted-foreground">
                                                        {
                                                            reference.criteria_label
                                                        }
                                                    </span>
                                                </>
                                            ) : (
                                                EMPTY_PLACEHOLDER
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {reference?.status ? (
                                                <StatusBadge
                                                    status={reference.status}
                                                />
                                            ) : (
                                                EMPTY_PLACEHOLDER
                                            )}
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                ) : (
                    <p className="text-sm text-muted-foreground">
                        Este exame ainda não possui resultados cadastrados.
                    </p>
                )}
            </CardContent>
        </Card>
    );
}
