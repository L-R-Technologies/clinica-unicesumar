import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { Paginated, PaginationLink } from '@/types';

/** Decodifica as entidades HTML que o Laravel coloca nos labels de paginação. */
function decodeLabel(label: string): string {
    return label
        .replace(/&laquo;/g, '')
        .replace(/&raquo;/g, '')
        .replace(/&hellip;/g, '…')
        .replace(/&amp;/g, '&')
        .trim();
}

function isPreviousLink(label: string): boolean {
    const normalized = label.toLowerCase();
    return (
        label.includes('&laquo;') ||
        normalized.includes('anterior') ||
        // Cobre a chave de tradução crua "pagination.previous"
        normalized.includes('previous')
    );
}

function isNextLink(label: string): boolean {
    const normalized = label.toLowerCase();
    return (
        label.includes('&raquo;') ||
        normalized.includes('próximo') ||
        normalized.includes('proximo') ||
        // Cobre "next" e a chave crua "pagination.next"
        normalized.includes('next')
    );
}

function PageLink({ link }: { link: PaginationLink }) {
    const text = decodeLabel(link.label);
    const previous = isPreviousLink(link.label);
    const next = isNextLink(link.label);

    const content = (
        <>
            {previous && <ChevronLeft className="size-4" />}
            {!previous && !next && text}
            {next && <ChevronRight className="size-4" />}
        </>
    );

    const baseClasses = cn(
        'inline-flex h-9 min-w-9 items-center justify-center rounded-md px-3 text-sm',
        link.active
            ? 'bg-primary text-primary-foreground'
            : 'hover:bg-accent hover:text-accent-foreground',
        !link.url && 'pointer-events-none opacity-50',
    );

    if (!link.url) {
        return (
            <span className={baseClasses} aria-disabled>
                {content}
            </span>
        );
    }

    return (
        <Link
            href={link.url}
            className={baseClasses}
            preserveScroll
            preserveState
        >
            {content}
        </Link>
    );
}

export function TablePagination<T>({ paginator }: { paginator: Paginated<T> }) {
    if (paginator.last_page <= 1) {
        return null;
    }

    return (
        <nav className="flex flex-wrap items-center justify-between gap-3 pt-2">
            <p className="text-sm text-muted-foreground">
                Mostrando {paginator.from ?? 0}–{paginator.to ?? 0} de{' '}
                {paginator.total}
            </p>
            <div className="flex flex-wrap items-center gap-1">
                {paginator.links.map((link, index) => (
                    <PageLink key={`${link.label}-${index}`} link={link} />
                ))}
            </div>
        </nav>
    );
}
