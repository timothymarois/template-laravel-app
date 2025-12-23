import pluginVue from 'eslint-plugin-vue';
import tsParser from '@typescript-eslint/parser';
export default [
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['**/*.{js,ts,vue,tsx}'],
        languageOptions: {
            parserOptions: {
                parser: tsParser,
                ecmaVersion: 'latest',
                sourceType: 'module',
                extraFileExtensions: ['.vue'],
            },
        },
        rules: {
            'vue/attribute-hyphenation': 'off',
            'vue/no-unused-vars': 'warn',
            'vue/max-attributes-per-line': 'off',
            'vue/multi-word-component-names': 'off',
            'vue/singleline-html-element-content-newline': 'off',
            'vue/html-indent': ['error', 4],
            'vue/html-closing-bracket-newline': 'off',
            'vue/html-self-closing': 'off',
            'vue/attributes-order': 'off',
            'vue/require-default-prop': 'off',
            'vue/no-v-html': 'off',
            'vue/v-slot-style': 'off',
            'vue/v-on-event-hyphenation': 'off',
            'vue/no-template-shadow': 'warn',
            'vue/first-attribute-linebreak': 'off',
            'vue/no-v-text-v-html-on-component': 'off',
            'vue/no-multi-spaces': 'off',
            'quotes': 'off',
            'semi': ['error', 'always'],
            'vue/script-indent': ['error', 4, { baseIndent: 0 }],
        }
    },
    {
        files: ['**/*.js', '**/*.ts', '**/*.tsx'],
        rules: {
            'indent': ['error', 4],
        }
    },
    {
        files: ['**/*.vue'],
        rules: {
            'indent': 'off',
        }
    }
]
