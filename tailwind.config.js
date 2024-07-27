
export default {
    content: [
        './storage/framework/views/*.php',
        './node_modules/primevue/**/*.{vue,js,ts,jsx,tsx}',
        './resources/presets/**/*.{js,vue,ts}',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,vue}',
    ],
    plugins: [
        require('tailwindcss-primeui'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
    darkMode: 'selector',
    theme: {
        mode: 'jit',
        extend: {
            colors: {

            }
        }
    },
}
