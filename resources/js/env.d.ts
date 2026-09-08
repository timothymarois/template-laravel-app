/// <reference types="vite/client" />

// No `declare module '*.vue'` shim here on purpose. `vue-tsc` resolves single-file
// components natively and infers their real props; a shim declaring them as
// `Record<string, never>` overrides that inference and reports every prop passed to
// every component as an error. The shim is only needed under plain `tsc`, which
// cannot read `.vue` at all — and a type gate that cannot read the components is
// not a gate.

import type { route as ziggyRoute } from 'ziggy-js';

declare module 'vue' {
    interface ComponentCustomProperties {
        /** Ziggy's route helper, registered globally in `app.js` for use in templates. */
        $route: typeof ziggyRoute;
    }
}

export {};
