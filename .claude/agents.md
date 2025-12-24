# Agent Guidelines

> Extends the main [AGENTS.md](/AGENTS.md) at the project root.

## Critical Rules

1. **PRDs override assumptions** - Never implement undefined behavior. PRDs live in `/docs/prd/`
2. **Ask before coding** - If requirements are unclear, request clarification first
3. **Run `pnpm check` before committing** - All checks must pass
4. **Check shadcn-vue first** - Before creating custom UI components
5. **Follow existing patterns** - Consistency over cleverness

## Button Placement in Form Footers

| Context                 | Primary Button | Justification    |
|-------------------------|----------------|------------------|
| Sheet (side panel)      | Left           | `justify-start`  |
| Dialog (centered modal) | Right          | `justify-end`    |
| Single-button alert     | Center         | `justify-center` |

Primary action goes closer to the user's focus: left for side panels (toward content), right for centered dialogs (reading flow).
