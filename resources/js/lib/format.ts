const LOCALE = 'pt-BR';
const EMPTY_PLACEHOLDER = '—';

export function formatDate(value: string | null): string {
    if (!value) {
        return EMPTY_PLACEHOLDER;
    }

    return new Date(value).toLocaleDateString(LOCALE, { timeZone: 'UTC' });
}

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
