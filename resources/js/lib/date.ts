/** Data de hoje no formato aceito por inputs date (YYYY-MM-DD). */
export function todayIsoDate(): string {
    return new Date().toISOString().slice(0, 10);
}
