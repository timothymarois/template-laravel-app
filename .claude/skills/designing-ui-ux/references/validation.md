# Designing UI and UX Validation

This is the current validation record for `designing-ui-ux`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for user-facing design — task flow, hierarchy, affordances and interaction
states, forms and feedback, responsive and mobile behavior, accessibility, interface text and the
naming of controls, and error recovery. It should not activate for backend behavior with no
interface, for marketing or brand copy, or for standalone image and asset production.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| UIX-T01 | Design the settings page for an admin application | Load |
| UIX-T02 | "Users keep losing what they typed when the form fails" | Load |
| UIX-T03 | Add a queue job with retry rules and no interface change | Do not load |
| UIX-T04 | Write the landing-page headline and marketing copy | Do not load; a different discipline |
| UIX-T05 | Produce a logo or an icon family as standalone assets | Do not load |
| UIX-T06 | Make a data table usable on a narrow screen | Load |
| UIX-T07 | A Vue component whose visual design and reactivity both need work | Compose with `using-vuejs`; that package owns component behavior, this one the experience |
| UIX-T08 | Interface labels that must agree with the schema and the API field names | Compose with `designing-databases` and `designing-apis`; one canonical term across the layers |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| UIX-W01 | A brief with no stated user or task | Establish user, task, and outcome first; state the assumption and choose a reversible direction rather than inventing requirements |
| UIX-W02 | Only the success path designed | Inventory loading, empty, partial, error, disabled, and permission-limited states, plus long content, zoom, keyboard, and touch |
| UIX-W03 | A list screen with one empty state | Require both: never-had-data and filtered-to-zero, and name showing the wrong one as a defect |
| UIX-W04 | Buttons labelled `Submit`, `Confirm`, `Yes` | Rename to verb plus object and keep the same words through control, confirmation, loading, and result |
| UIX-W05 | Primary action disabled to hide incomplete validation | Reject it; explain what enables the action, preserve entered values, and put messages beside fields |
| UIX-W06 | "The design is done and it looks good" | Reject fluent assurance; require the rendered result inspected, states exercised, keyboard traversal, and contrast checked — source code is not evidence |
| UIX-W07 | The current interface, design system, or tokens cannot be inspected | Read the interface and its tokens, or stop and name the unknown; do not invent content, capabilities, metrics, or states |
| UIX-W08 | Automated accessibility checks pass | Keep the limit: automated checks find defects but cannot prove a task is understandable or operable; record what was actually tested |
| UIX-W09 | A raw stored value such as `PENDING_REVIEW` shown to a user | Map it to a display term, and keep the machine value out of the interface |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. This package was added after the sampled run performed for the ten
  technology packages, so no case here has been executed.
- Codex: not run.

No case below is marked passed.

## Limits

This package's own workflow requires rendering the result and inspecting it. No case in this record
does that, so every case here grades the decision and the evidence demanded rather than an observed
interface. Visual judgment remains an owner verification.

UIX-T04 and UIX-T05 are the exclusion cases most likely to misfire, because both sit close to the
package's vocabulary without being inside its boundary.
