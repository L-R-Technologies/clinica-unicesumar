import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import prettier from 'eslint-config-prettier';
import globals from 'globals';

export default tseslint.config(
    {
        ignores: [
            'node_modules',
            'public/build',
            'public/hot',
            'vendor',
            'storage',
            'bootstrap/cache',
            'resources/js/types/ziggy.d.ts',
        ],
    },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        ...react.configs.flat.recommended,
    },
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        ...react.configs.flat['jsx-runtime'],
    },
    {
        files: ['**/*.{ts,tsx}'],
        plugins: { 'react-hooks': reactHooks },
        languageOptions: {
            globals: {
                ...globals.browser,
                // Helper global do Ziggy disponível em runtime (ver types/ziggy.d.ts).
                route: 'readonly',
            },
        },
        settings: {
            react: { version: 'detect' },
        },
        rules: {
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',
            // Em TSX com TypeScript, prop-types e o React global são redundantes.
            'react/prop-types': 'off',
            'react/react-in-jsx-scope': 'off',
            '@typescript-eslint/no-explicit-any': 'error',
            '@typescript-eslint/explicit-function-return-type': 'warn',
        },
    },
    prettier,
);
