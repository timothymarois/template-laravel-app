# Agent Guidelines

> Extends the main [AGENTS.md](/AGENTS.md) at the project root.

## Critical Rules

1. **PRDs override assumptions** - Never implement undefined behavior. PRDs live in `/docs/prd/`
2. **Ask before coding** - If requirements are unclear, request clarification first
3. **Run `pnpm check` before committing** - All checks must pass
4. **Reuse components first** - Search existing components before creating new ones
5. **Check shadcn-vue** - Before creating custom UI components
6. **Follow existing patterns** - Consistency over cleverness

## Component Guidelines

See [Component Architecture](/AGENTS.md#component-architecture) in the main AGENTS.md for full details.

**Quick Reference:**
- `ui/` = Reusable, stateless, no app logic (buttons, inputs, cards)
- `app/` = Application-specific with Inertia/auth (modals, navigation)
- `site/` = Public website specific (marketing, landing pages)

**Before creating components:**
1. Search `ui/`, `app/`, `site/` for existing components
2. Check [shadcn-vue](https://www.shadcn-vue.com/docs/components)
3. Install if available: `pnpm dlx shadcn-vue@latest add <component>`
4. Only create new if nothing exists

## Button Placement in Form Footers

| Context                 | Primary Button | Justification    |
|-------------------------|----------------|------------------|
| Sheet (side panel)      | Left           | `justify-start`  |
| Dialog (centered modal) | Right          | `justify-end`    |
| Single-button alert     | Center         | `justify-center` |

Primary action goes closer to the user's focus: left for side panels (toward content), right for centered dialogs (reading flow).
