# Changelog

This file lists the changes that are made with this template and how to migrate any projects that use it. 

## Unreleased

## 07/27/2024

Updated this template from PrimeVue 3 to PrimeVue 4

1. `npm remove primevue`
2. `npm install -D primevue`
3. `npm i -D tailwindcss-primeui`
4. Add `require('tailwindcss-primeui')` to tailwind plugins config.
5. Replace the `resources/css/theme.css` file
6. Remove all the custom attributes in tailwind config theme `colors`.
7. If you made custom theming, you will need to update those.
8. Use the Lara-mod theme or Download the [v4 presets](https://github.com/primefaces/primevue-tailwind/releases)
