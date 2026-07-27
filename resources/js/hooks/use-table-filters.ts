import { router } from '@inertiajs/react';

const SEARCH_KEY = 'search';

interface UseTableFiltersOptions<TFilters extends Record<string, string>> {
    routeName: string;
    filters: TFilters;
    search: string;
    resetSearch: () => void;
}

interface UseTableFiltersResult<TFilters extends Record<string, string>> {
    applyFilters: (next?: Partial<TFilters>) => void;
    clearFilters: () => void;
    hasActiveFilters: boolean;
    buildParams: (overrides?: Partial<TFilters>) => Record<string, string>;
}

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
