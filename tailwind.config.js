export default {
    content: [
        './resources/js/**/*.{js,ts,vue}',
        './node_modules/atlas-ui/src/**/*.{js,ts,vue}',
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
    darkMode: 'false',
    theme: {
        extend: {
            colors: {},
        },
    },
}
