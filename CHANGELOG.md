# Changelog

This file lists the changes that are made with this template and how to migrate any projects that use it. 

## Unreleased

## 07/27/2024

PrimeVue 3 to PrimeVue 4

1. `npm remove primevue`
2. `npm install -D primevue`
3. `npm i -D tailwindcss-primeui`
4. Add `require('tailwindcss-primeui')` to tailwind plugins config.
5. Replace the `resources/css/theme.css` file
6. Download the [v4 presets](https://github.com/primefaces/primevue-tailwind/releases)
7. Copy or replace them into `resources/presets`
8. Remove all the custom attributes in tailwind config theme `colors`.
9. If you made custom theming, you will need to update those.
