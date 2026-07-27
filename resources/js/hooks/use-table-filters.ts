import { router } from '@inertiajs/react';

const SEARCH_KEY = 'search';

interface UseTableFiltersOptions<TFilters extends Record<string, string>> {
    /** Nome da rota Ziggy do índice (ex.: 'samples.index'). */
    routeName: string;
    /** Filtros atuais vindos do servidor (inclui a chave `search`). */
    filters: TFilters;
    /** Valor de busca ao vivo mantido pelo useDebouncedSearch. */
    search: string;
    /** Reset da busca (useDebouncedSearch) chamado ao limpar os filtros. */
    resetSearch: () => void;
}

interface UseTableFiltersResult<TFilters extends Record<string, string>> {
    applyFilters: (next?: Partial<TFilters>) => void;
    clearFilters: () => void;
    hasActiveFilters: boolean;
    /** Monta os parâmetros de query limpos (sem valores vazios). */
    buildParams: (overrides?: Partial<TFilters>) => Record<string, string>;
}

/**
 * Encapsula o padrão de filtros server-side das páginas de índice: monta a query
 * a partir dos filtros atuais + busca ao vivo, remove valores vazios e dispara o
 * router.get preservando estado/scroll (padrão de índice do Inertia).
 */
export function useTableFilters<TFilters extends Record<string, string>>({
    routeName,
    filters,
    search,
    resetSearch,
}: UseTableFiltersOptions<TFilters>): UseTableFiltersResult<TFilters> {
    function buildParams(
        overrides: Partial<TFilters> = {},
    ): Record<string, string> {
        const merged: Record<string, string | undefined> = {
            ...filters,
            [SEARCH_KEY]: search,
            ...overrides,
        };

        return Object.fromEntries(
            Object.entries(merged).filter(
                (entry): entry is [string, string] =>
                    entry[1] !== '' && entry[1] !== undefined,
            ),
        );
    }

    function applyFilters(next: Partial<TFilters> = {}): void {
        router.get(route(routeName), buildParams(next), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    function clearFilters(): void {
        resetSearch();
        router.get(
            route(routeName),
            {},
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    const hasActiveFilters =
        search !== '' ||
        Object.entries(filters).some(
            ([key, value]) => key !== SEARCH_KEY && value !== '',
        );

    return { applyFilters, clearFilters, hasActiveFilters, buildParams };
}
