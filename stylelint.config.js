export default {
    extends: ['stylelint-config-standard'],
    plugins: ['@stylistic/stylelint-plugin'],
    rules: {
        // Enforce 4-space indentation
        '@stylistic/indentation': 4,

        // Tailwind CSS compatibility
        'at-rule-no-unknown': [true, {
            ignoreAtRules: ['tailwind', 'apply', 'variants', 'responsive', 'screen', 'layer', 'plugin', 'config', 'theme', 'source', 'custom-variant']
        }],
        'function-no-unknown': [true, {
            ignoreFunctions: ['theme', 'hsl']
        }],

        // @apply takes Tailwind utility names, not CSS values, so stylelint's
        // prelude validator flags every one of them as invalid.
        'at-rule-prelude-no-invalid': [true, {
            ignoreAtRules: ['apply', 'variant', 'custom-variant', 'theme', 'source']
        }],

        // Disable overly strict rules
        'import-notation': null,
        'no-descending-specificity': null,
        'declaration-block-single-line-max-declarations': null,
        'custom-property-empty-line-before': null,
        'rule-empty-line-before': null,
        'at-rule-empty-line-before': null,
        'length-zero-no-unit': null,
        'alpha-value-notation': null,
        'color-function-notation': null,
        'selector-class-pattern': null,
    }
};
