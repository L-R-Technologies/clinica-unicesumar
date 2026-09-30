const TWO_DIGITS = 2;

function padTwoDigits(value: number): string {
    return String(value).padStart(TWO_DIGITS, '0');
}

/**
 * Data de hoje no fuso do navegador, no formato aceito por inputs date
 * (YYYY-MM-DD). Não usa toISOString() porque ela devolve a data em UTC e,
 * à noite no Brasil, já cairia no dia seguinte.
 */
export function todayIsoDate(): string {
    const now = new Date();

    return `${now.getFullYear()}-${padTwoDigits(now.getMonth() + 1)}-${padTwoDigits(now.getDate())}`;
}

/** Data e hora locais no formato aceito por inputs datetime-local (YYYY-MM-DDTHH:mm). */
export function nowLocalDateTime(): string {
    const now = new Date();

    return `${todayIsoDate()}T${padTwoDigits(now.getHours())}:${padTwoDigits(now.getMinutes())}`;
}
