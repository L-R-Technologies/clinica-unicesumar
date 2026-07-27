const MAIN_CONTENT_ID = 'main-content';

/**
 * Link "pular para o conteúdo": invisível até receber foco por teclado,
 * permite que usuários de teclado/leitor de tela saltem a navegação repetida.
 */
export function SkipLink() {
    return (
        <a
            href={`#${MAIN_CONTENT_ID}`}
            className="sr-only rounded-md bg-primary px-4 py-2 font-medium text-primary-foreground focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50"
        >
            Pular para o conteúdo
        </a>
    );
}

export { MAIN_CONTENT_ID };
