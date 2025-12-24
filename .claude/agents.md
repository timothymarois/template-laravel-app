# Agent Guidelines

> Extends the main [AGENTS.md](/AGENTS.md) at the project root.

## Critical Rules

1. **PRDs override assumptions** - Never implement undefined behavior. PRDs live in `/docs/prd/`
2. **Ask before coding** - If requirements are unclear, request clarification first
3. **Run `pnpm check` before committing** - All checks must pass
4. **Review component showcase first** - Before implementing any UI, study examples in `pages/admin/components/`
5. **Reuse components first** - Search existing components before creating new ones
6. **Check shadcn-vue** - Before creating custom UI components
7. **Follow existing patterns** - Consistency over cleverness

## Component Showcase (REQUIRED)

**Before implementing ANY UI component, you MUST review the component showcase examples.**

Location: `resources/js/pages/admin/components/`

The showcase demonstrates correct usage patterns for all components. Study these examples to understand:
- How to structure props and handle states (empty, filled, invalid, disabled)
- Correct variant usage (sizes, styles, chip variants)
- Form integration patterns and validation states
- Consistent layout patterns (grid columns, labels, spacing)

**Categories:**
- `forms/` - Input, Select, Checkbox, Switch, TagsInput, Textarea, etc.
- `actions/` - Button, Dialog, Sheet, Menu, Command
- `display/` - Card, Badge, Alert, Avatar, Tabs, Toast, Tooltip
- `data/` - Table, Pagination, Actions
- `charts/` - Area, Bar, Line, Pie

**Always copy patterns from the showcase rather than inventing new ones.**

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
