const CPF_MAX_DIGITS = 11;
const PHONE_MAX_DIGITS = 11;
const CEP_MAX_DIGITS = 8;

export function stripDigits(value: string): string {
    return value.replace(/\D/g, '');
}

export function formatCpf(value: string): string {
    const digits = stripDigits(value).slice(0, CPF_MAX_DIGITS);

    return digits
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

export function formatPhone(value: string): string {
    const digits = stripDigits(value).slice(0, PHONE_MAX_DIGITS);

    if (digits.length <= 10) {
        return digits
            .replace(/(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4})(\d{1,4})$/, '$1-$2');
    }

    return digits
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
}

export function formatCep(value: string): string {
    const digits = stripDigits(value).slice(0, CEP_MAX_DIGITS);

    return digits.replace(/(\d{5})(\d{1,3})$/, '$1-$2');
}

export interface ViaCepAddress {
    street: string;
    neighborhood: string;
    city: string;
    state: string;
}

export async function fetchAddressByCep(
    cep: string,
): Promise<ViaCepAddress | null> {
    const digits = stripDigits(cep);

    if (digits.length !== CEP_MAX_DIGITS) {
        return null;
    }

    try {
        const response = await fetch(
            `https://viacep.com.br/ws/${digits}/json/`,
        );

        if (!response.ok) {
            return null;
        }

        const data: {
            erro?: boolean;
            logradouro?: string;
            bairro?: string;
            localidade?: string;
            uf?: string;
        } = await response.json();

        if (data.erro) {
            return null;
        }

        return {
            street: data.logradouro ?? '',
            neighborhood: data.bairro ?? '',
            city: data.localidade ?? '',
            state: data.uf ?? '',
        };
    } catch {
        return null;
    }
}
