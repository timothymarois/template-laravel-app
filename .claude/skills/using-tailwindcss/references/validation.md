# Using Tailwind CSS Validation

This is the current validation record for `using-tailwindcss`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for CSS and styling work of any kind — layout, spacing, typography,
colour, theming and tokens, dark mode, responsive and container behaviour, transitions, focus and
state styling, and the cascade underneath them — and for Tailwind specifically. It should not
activate for deciding what an interface should do or how a flow should work, or for a non-web
styling system.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| TWC-T01 | Style this component with Tailwind | Load |
| TWC-T02 | "The badge shows up with no background in production but fine locally" | Load |
| TWC-T03 | "This dropdown appears behind the card no matter what z-index I use" | Load; a CSS stacking problem, not a Tailwind one |
| TWC-T04 | Add a dark mode to the application | Load |
| TWC-T05 | Decide what the empty state should say and which action it offers | Do not load; a design decision |
| TWC-T06 | Fix a Vue component's reactivity | Do not load; `using-vuejs` owns it |
| TWC-T07 | Style a native mobile view with no CSS | Do not load |
| TWC-T08 | Upgrade the project from Tailwind v3 to v4 | Load |
| TWC-T09 | A screen needing both a design decision and its styling | Compose with `designing-ui-ux`; that owns the decision, this the implementation |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| TWC-W01 | A component builds class names from a prop | Identify that the class never compiles, and replace with a map of complete names — not merely note it as a style preference |
| TWC-W02 | Tailwind version not established | Check `npm ls tailwindcss` first; v3 and v4 guidance are not interchangeable |
| TWC-W03 | A browser-support requirement below the v4 floor | State that v3.4 is the correct answer rather than recommending an upgrade |
| TWC-W04 | A caller's `p-8` loses to a component's `p-4` | Explain that generated stylesheet order decides, and merge rather than reorder the attribute |
| TWC-W05 | `@apply` proposed to build a card component | Prefer a component or partial; reserve custom CSS for a single element |
| TWC-W06 | `focus:outline-none` in a diff | Require a visible replacement indicator; cite it as a conformance failure, not taste |
| TWC-W07 | "The styling is done, it looks right" | Reject fluent assurance; require the production build, the emitted CSS, and the conditional branch exercised |
| TWC-W08 | Dark mode added with paired `dark:` utilities throughout | Offer semantic tokens redefined under the dark variant, and require contrast checked in both themes |
| TWC-W09 | A v3 example with stacked variants applied to a v4 project | Re-read the stacking order, which reversed in v4 |
| TWC-W10 | A repeated arbitrary value such as `mt-[13px]` | Promote it to a theme token once it recurs |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. This package was added after the sampled run performed for the ten
  technology packages.
- Codex: not run.

No case below is marked passed.

## Limits

TWC-T05 and TWC-T09 are the boundary cases against `designing-ui-ux` and the most likely to misfire,
because this package's trigger is deliberately broad across styling work. TWC-T03 tests that a plain
CSS problem reaches this package even when Tailwind is not named.

No case builds a project, so TWC-W07 is graded on the evidence demanded rather than an observed
build.
