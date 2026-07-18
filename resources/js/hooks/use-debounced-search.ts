import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const DEFAULT_DELAY_MS = 300;

interface UseDebouncedSearchOptions {
    /** Nome da rota Ziggy para onde a busca é enviada (ex.: 'samples.index'). */
    routeName: string;
    /** Valor inicial vindo dos filtros do servidor. */
    initialValue?: string;
    /** Demais filtros preservados na query string a cada busca. */
    extraParams?: Record<string, string | number | undefined>;
    delay?: number;
}

/**
 * Mantém o estado local do campo de busca e dispara um router.get com debounce,
 * preservando o restante da página (padrão de índice server-side do Inertia).
 */
export function useDebouncedSearch({
    routeName,
    initialValue = '',
    extraParams = {},
    delay = DEFAULT_DELAY_MS,
}: UseDebouncedSearchOptions) {
    const [search, setSearch] = useState(initialValue);
    const isFirstRender = useRef(true);
    const skipNextRequest = useRef(false);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        if (skipNextRequest.current) {
            skipNextRequest.current = false;
            return;
        }

        const timeout = setTimeout(() => {
            const params: Record<string, string | number> = {};
            for (const [key, value] of Object.entries(extraParams)) {
                if (value !== undefined && value !== '') {
                    params[key] = value;
                }
            }
            if (search) {
                params.search = search;
            }

            router.get(route(routeName), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, delay);

        return () => clearTimeout(timeout);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    /** Zera o campo de busca sem disparar a requisição debounced (evita corrida com um clear manual). */
    function reset(): void {
        skipNextRequest.current = true;
        setSearch('');
    }

    return { search, setSearch, reset };
}
