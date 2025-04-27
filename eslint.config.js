import pluginVue from 'eslint-plugin-vue'
export default [
    ...pluginVue.configs['flat/recommended'],
    {
        rules: {
            'vue/no-unused-vars': 'warn',
            'vue/max-attributes-per-line': ['error', { singleline: 5, multiline: 1 }],
            'vue/multi-word-component-names': "off",
            'vue/singleline-html-element-content-newline': 'off',
            'vue/multi-word-component-names': 'off',
            'vue/html-indent': [
                "error",
                4, {
                    "attribute": 1,
                    "baseIndent": 1,
                    "closeBracket": 0,
                    "alignAttributesVertically": true,
                    "ignores": []
                }
            ],
            'vue/html-closing-bracket-newline': [
                'error',
                {
                    multiline: 'always',
                    singleline: 'never',
                },
            ],
            'quotes': ['error', 'single'],
            'semi': ['error', 'always'],
            'indent': ['error', 4],
        }
    }
]
