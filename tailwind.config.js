export default {
    content: [
        './resources/js/**/*.{js,ts,vue}',
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
