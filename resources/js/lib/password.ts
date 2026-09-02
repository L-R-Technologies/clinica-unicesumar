const PASSWORD_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
const PASSWORD_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const PASSWORD_DIGITS = '0123456789';
const PASSWORD_ALPHABET =
    PASSWORD_LOWERCASE + PASSWORD_UPPERCASE + PASSWORD_DIGITS;
const GENERATED_PASSWORD_LENGTH = 12;

/**
 * Gera uma senha temporária que satisfaz as regras do backend
 * (Password::default() com mixedCase e numbers): garante ao menos uma
 * minúscula, uma maiúscula e um número, e embaralha as posições.
 */
export function generateTemporaryPassword(): string {
    const requiredSets = [
        PASSWORD_LOWERCASE,
        PASSWORD_UPPERCASE,
        PASSWORD_DIGITS,
    ];
    const randomValues = new Uint32Array(GENERATED_PASSWORD_LENGTH);
    crypto.getRandomValues(randomValues);

    const characters = Array.from(randomValues, (randomValue, index) =>
        index < requiredSets.length
            ? requiredSets[index][randomValue % requiredSets[index].length]
            : PASSWORD_ALPHABET[randomValue % PASSWORD_ALPHABET.length],
    );

    const shuffleValues = new Uint32Array(GENERATED_PASSWORD_LENGTH);
    crypto.getRandomValues(shuffleValues);
    for (let i = characters.length - 1; i > 0; i--) {
        const j = shuffleValues[i] % (i + 1);
        [characters[i], characters[j]] = [characters[j], characters[i]];
    }

    return characters.join('');
}
