import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const DEFAULT_DELAY_MS = 300;

interface UseDebouncedSearchOptions {
    routeName: string;
    initialValue?: string;
    extraParams?: Record<string, string | number | undefined>;
    delay?: number;
}

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

    function reset(): void {
        skipNextRequest.current = true;
        setSearch('');
    }

    return { search, setSearch, reset };
}
