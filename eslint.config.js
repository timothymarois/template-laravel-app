import pluginVue from 'eslint-plugin-vue'
export default [
    // add more generic rulesets here, such as:
    // js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        rules: {
            // override/add rules settings here, such as:
            // 'vue/no-unused-vars': 'error',
            // "vue/multi-word-component-names": [
            //     "error", {
            //         "ignores": []
            //     }
            // ],
            "vue/html-indent": [
                "error",
                4, {
                    "attribute": 1,
                    "baseIndent": 1,
                    "closeBracket": 0,
                    "alignAttributesVertically": true,
                    "ignores": []
                }
            ]
        }
    }
]
