// Stand-in for the `ziggy` alias during unit tests.
//
// `ziggy` resolves to vendor/tightenco/ziggy/src/js — delivered by Composer, not npm.
// The Vitest CI job is deliberately node-only (no PHP, no composer, no vendor/), so the
// real library is not on disk there and Vite cannot even resolve the specifier, let
// alone let a test mock it. vite.config.js points the alias here for `test` only.
//
// URL generation is Ziggy's own concern and is tested by that library. What our tests
// pin is the contract the plugin owns: how it forwards name, params, absolute and config.
export const route = (name) => `/${String(name).replace(/\./g, '/')}`;
