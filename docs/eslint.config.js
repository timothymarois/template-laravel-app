import tsParser from '@typescript-eslint/parser';

export default [
    {
        files: ['**/*.{js,ts,mts}'],
        languageOptions: {
            parser: tsParser,
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
            },
        },
        rules: {
            'indent': ['error', 4],
            'semi': ['error', 'always'],
            'quotes': ['error', 'single'],
        },
    },
];
