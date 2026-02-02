export default {
    extends: ['stylelint-config-standard'],
    plugins: ['@stylistic/stylelint-plugin'],
    rules: {
        // Enforce 4-space indentation
        '@stylistic/indentation': 4,

        // Disable overly strict rules
        'no-descending-specificity': null,
        'no-duplicate-selectors': null,
        'custom-property-empty-line-before': null,
        'rule-empty-line-before': null,
        'at-rule-empty-line-before': null,
        'selector-class-pattern': null,
        'alpha-value-notation': null,
        'color-function-notation': null,
        'color-function-alias-notation': null,
    },
};
