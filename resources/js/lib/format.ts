const LOCALE = 'pt-BR';
const EMPTY_PLACEHOLDER = '—';

/**
 * Formata uma data (campo `date` do backend, ancorado em meia-noite UTC) como
 * dd/mm/aaaa. Usa timeZone UTC para ler o dia-calendário correto, evitando o
 * off-by-one que ocorre ao converter meia-noite UTC para o fuso local.
 */
export function formatDate(value: string | null): string {
    if (!value) {
        return EMPTY_PLACEHOLDER;
    }

    return new Date(value).toLocaleDateString(LOCALE, { timeZone: 'UTC' });
}

/**
 * Formata um instante (campo `datetime` do backend, serializado em UTC) como
 * dd/mm/aaaa HH:MM no fuso local do usuário.
 */
export function formatDateTime(value: string | null): string {
    if (!value) {
        return EMPTY_PLACEHOLDER;
    }

    return new Date(value).toLocaleString(LOCALE, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

/** Formata o valor de um resultado de exame para exibição. */
export function formatResultValue(
    value: string | number | boolean | null,
): string {
    if (typeof value === 'boolean') {
        return value ? 'Sim' : 'Não';
    }

    if (value === null || value === '') {
        return EMPTY_PLACEHOLDER;
    }

    return String(value);
}
