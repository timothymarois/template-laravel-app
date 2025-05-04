# Changelog

Any project using this template should follow the changelog here if its relevant to make the needed updates to be compatible. 

Note: once you update a project on that uses this template, be sure to copy this changelog so that project can also understand which version its currently using.

# Released 

## v2.0.2 - 05/04/2025

Around the release for new AtlasFrame and scroll control

- Replaces the `<PageMain>` component with new `<AtlasFrame page>`
- Replaces the `usePageTop` with the new atlas `useScroll` composable
- Updated `vite.config.js`

## v2.0.1 - 05/01/2025

Minor update, most of the changes are examples.

- Updated `vite.config.js`
- Updated `resources/js/setup.js`

## v2.0.0 - 04/26/2025

This update centralizes the base components into atlas ui repo

- Install package `npm install github:tmarois/atlas-ui#semver:^1.0.0`
- Updated `tailwind.config.js`
- Updated `vite.config.js` (new components and composables for autoloading)
- Updated `resources/js/setup.js`
- Updated `resources/css/app.css`
- Deleted all `/components/_volt/~` (now loading from custom atlas-ui package), this removes all the volt components.

# Unreleased 

## v1.1.0 - 04/04/2025

- Removed the lara-mod presets and moved to [PrimeVue Volt](https://volt.primevue.org/)
- Removes a lot of unneeded auto-loading
- Removes Pinia state management
- Added Laravel Solo command

## v1.0.0 - 07/27/2024

Updated this template from PrimeVue 3 to PrimeVue 4

1. `npm remove primevue`
2. `npm install -D primevue`
3. `npm i -D tailwindcss-primeui`
4. Add `require('tailwindcss-primeui')` to tailwind plugins config.
5. Replace the `resources/css/theme.css` file
6. Remove all the custom attributes in tailwind config theme `colors`.
7. If you made custom theming, you will need to update those.
8. Use the Lara-mod theme or Download the [v4 presets](https://github.com/primefaces/primevue-tailwind/releases)
