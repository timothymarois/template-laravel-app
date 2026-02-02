# Tech Stack

The complete technology stack and directory structure.

## Core Technologies

### Backend

| Package | Version | Purpose |
|---------|---------|---------|
| [Laravel](https://laravel.com/docs/12.x) | 12 | Backend framework |
| PHP | 8.4+ | Runtime |
| Redis | — | Queue and cache |
| [Horizon](https://laravel.com/docs/12.x/horizon) | — | Queue monitoring |
| [Reverb](https://laravel.com/docs/12.x/reverb) | — | WebSocket server |

### Frontend

| Package | Version | Purpose |
|---------|---------|---------|
| [Vue.js](https://vuejs.org/guide/introduction.html) | 3 | Frontend framework |
| [Inertia.js](https://inertiajs.com/) | 2 | SPA without API |
| [Tailwind CSS](https://tailwindcss.com/docs/installation) | 4 | Utility-first styling |
| [shadcn-vue](https://www.shadcn-vue.com/) | — | Component primitives |
| [Lucide Icons](https://lucide.dev/) | — | Icon library |
| [Ziggy](https://github.com/tighten/ziggy) | — | Laravel routes in JS |

### Development

| Tool | Purpose |
|------|---------|
| [Vite](https://vitejs.dev/) | Build tool |
| [TypeScript](https://www.typescriptlang.org/) | Type safety |
| [ESLint](https://eslint.org/) | JavaScript linting |
| [Stylelint](https://stylelint.io/) | CSS linting |
| [Laravel Pint](https://laravel.com/docs/12.x/pint) | PHP code style |
| [Larastan](https://github.com/larastan/larastan) | PHP static analysis |
| [Pest](https://pestphp.com/) | PHP testing |
| [Vitest](https://vitest.dev/) | JavaScript testing |

## Directory Structure

### Laravel (Backend)

```
app/
├── Console/Commands/   # Artisan commands
├── Enums/              # PHP backed enums
├── Http/
│   ├── Concerns/       # Reusable controller traits
│   ├── Controllers/    # Thin controllers
│   └── Requests/       # Form Request validation
├── Models/             # Eloquent models
├── Integrations/       # Third-party APIs, external services
├── Services/           # Business logic
│   ├── Models/         # Per-model services (extend ModelService)
│   └── <Domain>/       # Feature services grouped by domain
└── Support/            # Small helpers, traits, utilities
```

### Vue (Frontend)

```
resources/js/
├── components/         # Reusable components
│   ├── ui/             # Base components (shadcn + custom)
│   ├── app/            # Application components (authenticated)
│   └── site/           # Website components (public)
├── pages/              # Inertia pages (organized by route)
│   ├── Index.vue       # Home page
│   └── admin/          # /admin/* routes
├── composables/        # Vue composables
├── tests/              # Vitest unit tests
└── utils/              # Utilities
```

## Adding Components

### shadcn-vue

Install components directly into your project:

```bash
pnpm dlx shadcn-vue@latest add button
pnpm dlx shadcn-vue@latest add dialog
```

Components install to `resources/js/components/ui/`.

### Theming

Modify CSS custom properties in `resources/css/theme.css`:

```css
:root {
    --primary: 262.1 83.3% 57.8%;
    --primary-foreground: 210 20% 98%;
}

.dark {
    --primary: 263.4 70% 50.4%;
    --primary-foreground: 210 20% 98%;
}
```

## Configuration Files

| File | Purpose |
|------|---------|
| `vite.config.ts` | Vite build configuration |
| `tsconfig.json` | TypeScript configuration |
| `tailwind.config.ts` | Tailwind configuration |
| `eslint.config.js` | ESLint rules |
| `stylelint.config.js` | Stylelint rules |
| `vitest.config.ts` | Vitest test configuration |
| `phpstan.neon` | Larastan configuration |
| `pint.json` | Laravel Pint rules |
