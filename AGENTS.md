# AGENTS

Rules for every agent working in this repository. These rules are law; where they conflict with your general
habits, this file wins.

This is a **Laravel 13 + Inertia/Vue 3 starter template** (PHP 8.4, TailwindCSS 4, shadcn-vue, Redis/Horizon,
optional `stancl/tenancy`): a thin HTTP layer over service/action business logic, with a Vue 3 SPA rendered
through Inertia. The *what & why* lives in `.knowledge/BRIEF.md`; the knowledge map is `.knowledge/README.md`.
This file defines how you build here.

## Before you work

Load light; pull depth only when the task needs it.

1. **Always read first:** `.knowledge/BRIEF.md` (what & why), `.knowledge/CODEMAP.md` (where things are),
   `.knowledge/MEMORY.md` (current friction). `.knowledge/README.md` maps the rest.
2. **On demand, when the task enters an area:** `.knowledge/prd/` (ratified contracts — source of truth),
   `.knowledge/prd-drafts/` (proposals), `.knowledge/research/` + `.knowledge/references/` (prior art, visual
   targets), `.knowledge/guides/` (how to write each doc + project how-tos, incl. `stack-examples.md`).
3. **How work flows:** `research/` -> `prd-drafts/` -> `prd/`; a `prd/` contract never cites a draft. New
   guaranteed behavior is a `prd/` row backed by a test — cite its `R-<AREA>-<n>` in the code. Follow a doc's
   guide before writing or modifying it, and keep docs true in the same task. Run
   `python3 .knowledge/scripts/doc-lint .knowledge` before finishing; scratch -> `.knowledge/tmp/`.
4. Read every file before editing it; search before writing new logic — reuse, extend, refactor.
5. When the user raises a concern, investigate before contradicting — evidence, not a hunch.

> **Searching the repo:** the template's changelog + migration guides live in the hidden `.template/`
> directory, which default code search skips. Pass `--hidden`, use `grep -r` / `find`, or read the path
> directly when you need them.

## Hard gates — require explicit approval

- **Migrations / persisted state.** Any change to database schema, user data, or stored state is confirmed first.
- **Dependencies.** Do not add, remove, or version-bump a Composer or pnpm package (or a pinned engine) without approval.
- **Deletions.** Do not delete files outside the task's immediate scope without approval.
- **Commits.** Do not commit or push unless told to.
- **This file.** Never modify `AGENTS.md` without approval; when approved, follow
  `.knowledge/guides/docs-agents.md`.

## Never

- Never touch `.env` or commit credentials. Access env only through `config/` — never call `env()` outside `config/`.
- Never leave debug output (`dd()`, `dump()`, `console.log`), commented-out code, or disabled tests in completed work.
- Never validate on the frontend — Laravel Form Requests are the single source of truth; the frontend only
  displays server-returned errors.
- Never let backend and frontend drift — names, props, enums, and routes match between Laravel and Vue; rename
  one side, rename the other in the same task.
- Never add legacy fallbacks or polyfills unless asked — use the current, modern approach.

## Tech stack

- **Backend:** Laravel 13+ (PHP 8.4+), Redis via Horizon (queue + cache), Inertia server adapter, optional
  `stancl/tenancy` (inert by default).
- **Frontend:** Vue 3 (`<script setup>`), Inertia.js, TailwindCSS 4, shadcn-vue (Radix Vue primitives),
  Lucide icons, Ziggy named routes, vue-sonner (toasts).

## Architecture — the one rule that matters

The HTTP layer is thin; business logic lives in services and actions. Dependencies point **inward**, one-way.

| Layer | Owns | May depend on | Must not |
|---|---|---|---|
| Controllers | Receive a Form Request, delegate, return a response | Services, Actions | Hold business logic, validate inline, authorize inline |
| Form Requests | Validation + authorization (via Policies) | Policies | — |
| Services / Actions | Business processes / single-purpose ops | Models, other services (constructor-injected) | Touch the HTTP layer; use facades/helpers |
| Vue `ui/` -> `app/` -> `site/` | Stateless kit -> auth surfaces -> public pages | inner tiers only | `ui/` may not be app-aware (no Inertia/auth/routes) |

- **Placement:** no app logic -> `ui/`; needs Inertia/auth -> `app/`; public marketing -> `site/`. Search
  the component showcase (`resources/js/pages/admin/components/`) before building a new one — it likely exists.
- **shadcn primitives use the `*Base` suffix**; enhanced `ui/` versions wrap them. `ui/` requires `lang="ts"`
  with typed props. Barrel imports (`index.ts`) are `ui/`-only; `app/` and `site/` use direct file imports.

## Best practices — do / don't

The enforceable conventions of this stack. Worked `✅`/`❌` galleries for both languages live in
[`.knowledge/guides/stack-examples.md`](.knowledge/guides/stack-examples.md); the rules are here.

### Backend

- **Do** `declare(strict_types=1)`, return types on every method, constructor injection, modern PHP (enums,
  DTOs, `readonly`), and run **Pint** (PSR-12). **Don't** use facades/helpers inside services, or
  `use function` imports.
- **Do** keep controllers to: Form Request in, Service/Action call, response out — logic in Services
  (processes) or Actions (single-purpose ops). **Don't** put logic, inline validation, or role checks there.
- **Every state-changing route is authorized — no exceptions.** With input, authorize in the Form Request
  via a Policy. Without input (toggle, delete, action), call `$this->authorize()` in the controller against
  the same Policy. **A route under an `admin` prefix is not authorization** — that group carries
  `auth:sanctum` only, so an unauthorized action there is reachable by any logged-in user.
- **Do** keep models lightweight (heavy logic -> Services), avoid N+1 with eager loading, and write
  migrations idempotent and reversible with indexes. **Do** cite the `R-<AREA>-<n>` a method implements in
  its doc-block, so code ↔ contract ↔ test stay linked.

```php
✅ public function store(StoreUserRequest $request): RedirectResponse {
       $this->userService->create($request->validated());
       return redirect()->route('admin.users.index');
   }
❌ public function store(Request $request) {                       // inline validation + logic + auth
       if (! $request->user()->isAdmin()) abort(403);
       $request->validate(['name' => 'required']);
       User::create([...]);
   }
```

### Frontend

- **Do** put `<template>` above `<script setup>`, PascalCase components, 4-space indent, `lang="ts"` only for
  reusable `ui/` components. **`useForm` for anything with fields; `router.<verb>(route(...))` for an
  input-less action.** **Don't** hand-roll refs for fields/errors/processing, or reach for fetch/axios.
- **Do** prefer `computed` over watchers, props-down/events-up (avoid `provide`/`inject` and `defineExpose`),
  and `async`/`await`. **Do** `:key` every `v-for`; never `v-if` + `v-for` on one element. **Don't** use
  inline `:style` (Tailwind classes only) or leave a `console.log`.
- **Do** keep a component's `<script setup>` under ~100 lines and free of data transformation or business
  logic — extract to `composables/` (stateful, `use`-prefixed) or `utils/` (pure). **Don't** let the same
  logic live in 2+ components.
- **Do** `route()` in scripts / `$route()` in templates, Lucide icons, `cursor-pointer` + hover/focus on
  interactive elements, Sonner for toasts. **Don't** hardcode URLs or add another icon library.

```vue
✅ <script setup> const form = useForm({ name: '' }); function submit(){ form.post(route('admin.users.store')); } </script>
❌ <script setup> const name = ref(''); const errors = ref({}); /* manual + client validation */ </script>

✅ <Button class="cursor-pointer" @click="router.patch(route('admin.users.suspend', user.id))">Suspend</Button>
❌ <Button @click="router.patch(`/admin/users/${user.id}/suspend`)">Suspend</Button>   <!-- no cursor, hardcoded URL -->
```

## Code documentation

Document the non-obvious — *why* a method exists, its contract, which requirement it satisfies. Trivial
controllers and accessors get nothing; a comment that restates the code is noise.

- **Complex Services/Actions get a PHPDoc block** (intent · contract · edge cases); non-trivial
  composables/utils get TSDoc. Explain *why*, not *what*.
- **Cite the requirement** — a method implementing a `.knowledge/prd/` requirement names its `R-<AREA>-<n>`
  in the doc-block. **Keep it true** — update a stale doc-block in the same change.

```php
✅ /** Provisions a tenant database and seeds its owner (R-TENANT-2). Idempotent: a re-run on a
    *  half-provisioned tenant resumes, never duplicates. @throws ProvisioningException on unreachable central. */
   public function provision(Tenant $tenant): void
❌ // provision the tenant      ← restates the name; teaches nothing
```

## Directory structure

```
app/
├── Console/Commands/  Enums/  Jobs/  Models/  Notifications/  Providers/  Support/
├── Http/{Controllers,Requests,Middleware,Concerns}/   # thin controllers, Form Request validation
├── Services/{Models,<Domain>}/                         # business logic; Actions/ + DataTransferObjects/ on first use
resources/js/
├── components/{ui,app,site}/   pages/   composables/   utils/   tests/
├── pages/                      # Inertia pages by route (Index.vue = public home, admin/ = /admin/*)
routes/{web,api,tenant,channels}.php
database/migrations/            # central; tenant/ = per-tenant when tenancy is enabled
.knowledge/                     # agent docs: BRIEF, CODEMAP, MEMORY, OVERVIEW, prd/, guides/ — see README
```

## Build, test & run

```bash
pnpm check         # the gate: check:php + check:js (parallel), then check:build
pnpm check:tenancy # Pest against the tenancy suite (phpunit.tenancy.xml) — when tenancy is enabled
pnpm check:all     # check + check:tenancy
pnpm dev           # local dev server (Herd serves the app)
```

`pnpm check` runs Pint, Larastan (level 5), Pest, ESLint, Stylelint, tsc, Vitest, the `.knowledge/` doc
linter, and the client + SSR builds. Auto-fix: `pnpm lint:fix`, `pnpm lint:css:fix`. **Who runs the app:**
build to prove it compiles, then hand off — the **owner runs the UI** and provides screenshots for visual
sign-off. "Compiles + wired" is not "done".

## Optional stacks

- **Multi-tenancy.** `stancl/tenancy` ships installed but **inert** (`TENANCY_ENABLED=false`). While
  disabled, treat tenancy code as nonexistent — don't import `Tenant`/`Domain`, run `tenants:*`, or add
  tenant routes. Usage and the disabled-state contract: `.knowledge/guides/tenancy-usage.md`; adopting it on
  a fork with existing data: `.knowledge/guides/tenancy-migrations.md`.
- **Docker / deployment.** An optional Coolify setup lives in `docker/` + the root `Dockerfile`. A fork
  changes only the documented **knobs**; the managed core tracks this template and new Docker capabilities
  originate here, never in a fork. Setup, knobs, drift + versioning policy: `docker/README.md`.

## Documentation duties

Keep docs true in the same task that changes reality. Before creating or editing a doc, read its home
`README.md` and follow its `guides/docs-*.md`.

- Moved/restructured files -> update `.knowledge/CODEMAP.md`.
- Hit friction — **anything that cost you a failed attempt**: an env var or flag you had to discover, a guard
  you had to satisfy, a command that only worked the second way -> **write the line into `.knowledge/MEMORY.md`
  the moment you find the workaround, before you carry on** — by the end of the task it will feel too small to
  mention, which is exactly how the next agent loses the same hour. Delete it once solved.
- Owner ratifies a draft (**the whole file**, not one row) -> `git mv` it into `prd/`; IDs carry over and the
  conformance review then sets glyphs. **Approval moves a draft, not proof** — proof is the glyph column.
  Behavior and its requirement row change in the same commit.
- Scratch -> `.knowledge/tmp/` (git-ignored).

## Definition of done

1. `pnpm check` passes (Pint, Larastan level 5, Pest, ESLint, Stylelint, tsc, Vitest, doc-lint, client + SSR build).
2. Every rule here held — thin controllers, validation server-side only, no backend/frontend drift.
3. New guaranteed behavior is proven by a `.knowledge/prd/` requirement and its test.
4. **Friction you hit is in `.knowledge/MEMORY.md`, not only in your reply** — the next agent reads the file,
   not this conversation. Hit none? Say that in your reply. **Never write "no friction" into the file** —
   `MEMORY.md` records traps, never their absence.
