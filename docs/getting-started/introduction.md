# Introduction

This starter kit provides everything you need to build production-grade Laravel applications with a Vue frontend.

## What's Included

Production-grade applications require more than just code—they need authentication, real-time updates, background jobs, testing, monitoring, security hardening, SEO, and deployment pipelines. Setting all of this up correctly takes weeks.

**This starter kit handles all of it.** Every layer has been considered, implemented, and wired together so you can focus entirely on your application logic.

### Core Stack

- **Laravel 12 + Vue 3 + Inertia 2** — SPA experience without API complexity
- **Tailwind 4 + shadcn-vue** — Beautiful, accessible components you own and customize
- **50+ UI components** — Production-grade, fully customizable, ready out of the box

### Production Ready

- **SSR enabled by default** — SEO-friendly, fast first paint
- **Security headers, CORS, rate limiting** — Hardened out of the box
- **Sanctum authentication** — Session-based auth with CSRF protection

### Real-Time & Background Jobs

- **Reverb WebSockets + Echo** — Real-time events, zero external dependencies
- **Horizon queues** — Redis-powered job processing with dashboard

### Developer Experience

- **Solo dev runner** — All processes in one terminal
- **Ziggy routes** — Use Laravel named routes directly in Vue
- **Hot reload** — Instant feedback during development

### Testing & Code Quality

- **Pest + Vitest** — Full test coverage, backend and frontend
- **Larastan + ESLint + Stylelint + Pint** — Static analysis on both stacks
- **Single command checks** — `pnpm check` runs everything before you commit

### Monitoring & Debugging

- **Log Viewer** — Browse application logs in the browser
- **Horizon dashboard** — Monitor queues and failed jobs
- **Sentry-ready** — Error tracking integration configured
- **Health check endpoint** — Load balancer and uptime monitoring

### SEO & Social

- **Open Graph + Twitter Cards** — Social sharing just works
- **Sitemap generation** — Auto-generates from your routes
- **Meta tag management** — Per-page title, description, images

## Next Steps

- [Installation](/getting-started/installation) — Get up and running in 5 minutes
- [Configuration](/getting-started/configuration) — Environment and settings
- [Architecture Overview](/architecture/overview) — Understand the system design
