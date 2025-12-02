/**
 * Determines if the current execution context is running in the browser (client-side).
 *
 * This is useful to safely access DOM-related APIs like `window` or `document` in
 * environments that support Server-Side Rendering (SSR), such as Nuxt or Laravel Inertia.
 *
 * @constant {boolean} isClient - `true` if running in the browser, otherwise `false`.
 */
export const isClient = typeof window !== 'undefined' && typeof document !== 'undefined';
