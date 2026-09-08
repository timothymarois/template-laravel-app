---
name: designing-ui-ux
description: Use when designing, implementing, or critiquing web UI and UX, including pages, applications, dashboards, forms, navigation, components, design systems, responsive and mobile behavior, interaction and focus states, accessibility, interface text and the naming of controls, error recovery, usability, or visual polish. It supplies an evidence-backed workflow for task flow, hierarchy, affordances, feedback, and rendered verification. Do not use for backend behavior with no interface, for marketing or brand copy, or for standalone image and asset production.
---

# Design UI and UX

Treat UI as product behavior, not decoration. Make the shortest important task understandable,
operable, and recoverable before making it memorable. Preserve the project's framework, design
system, content, and constraints.

## Ground the experience

Before implementation:

1. Identify the user, their primary task, the page or flow's outcome, and evidence behind the brief.
2. Read the current interface and trace the real data, permissions, navigation, terminology, and
   tests. Do not invent content, capabilities, metrics, or states.
3. Map the shortest successful path and the decisions, interruptions, and recovery points along it.
4. Inventory initial, loading, empty, partial, success, error, disabled, and permission-limited
   states. Include long content, translation, narrow viewports, zoom, keyboard, and touch.
5. Derive a visual thesis from the subject's real language, materials, artifacts, and environment.
   Turn it into typography roles, palette roles, composition, density, imagery, and motion choices.
   When appropriate, choose one signature element; let everything else support the task.

When evidence is missing, state the assumption and choose a reversible direction. Do not turn a
personal preference or current aesthetic trend into a user requirement.

## Make actions obvious and predictable

- Use a link for navigation and a button for an action. Give non-submit buttons an explicit
  `type="button"`; a button inside a form otherwise submits by default.
- Give each region one visually dominant action. Lower the emphasis of secondary actions and make
  destructive actions distinct without making them the default path.
- Label actions with specific verbs and objects: `Save changes`, not `Submit`; `Delete project`, not
  `Yes`. Keep the same name through the control, confirmation, loading state, and result.
- Design default, hover, keyboard-focus, pressed, selected, loading, disabled, success, and error
  states without moving the control or hiding its label unexpectedly.
- Make enabled click or tap targets look interactive before hover. Use control shape, label,
  contrast, placement, state changes, and cursor together.
- Apply `cursor: pointer` to every enabled activation target: buttons, links, clickable cards, menu
  triggers, icon actions, toggles, checkbox and radio labels, and equivalent controls. Disabled
  controls are not activation targets; drag, resize, text-selection, and similar interactions keep
  their truthful operation-specific cursors. A pointer cursor never substitutes for native
  semantics, keyboard behavior, focus, or a visible pressed state.
- Make the entire visible control activate the action. Use generous hit areas; provide a single-
  pointer alternative to dragging and never hide required behavior behind hover.

Read [naming.md](references/naming.md) whenever you write a title, label, button, column header,
empty state, error, or confirmation. What a control is called is part of whether it is
understandable, not a copy pass afterwards, and the same words must hold from the control through
the confirmation to the result.

Read [interaction-and-actions.md](references/interaction-and-actions.md) when building or reviewing
buttons, links, clickable cards, menus, dialogs, icon controls, destructive actions, cursor behavior,
pointer and keyboard states, touch targets, gestures, or mixed-input behavior.

## Build hierarchy before decoration

- Establish task order with composition, alignment, spacing, type, contrast, scale, and imagery.
- Make the visual thesis critiqueable: name what each type role, color role, layout move, and motion
  communicates. Do not substitute a font-and-color list for a direction tied to the subject.
- Reuse project tokens for color, type, spacing, radius, borders, elevation, and motion. Extend a
  shared primitive only when the behavior is genuinely shared.
- Use color to reinforce hierarchy and state, never as the only carrier of meaning. Do not rely on
  size alone for heading hierarchy.
- Use cards only when grouping or interaction needs a card. Avoid nested rounded panels, default
  dashboard mosaics, fake metrics, ornamental icon rows, and decorative gradients that dilute the
  primary task.
- Let structure encode real meaning: numbering for order, badges for status, dividers for grouping,
  and motion for continuity or state change.
- Spend boldness in one place. Remove any decoration that does not clarify content, brand, state, or
  action.

Make aesthetic choices from the subject and audience. A familiar style is valid when it fits; it is
weak when it appears only because no decision was made.

## Give feedback and recovery

- Respond visibly to every action. Keep the initiating control and affected content understandable
  while work is pending, then show the stored result or a specific recovery path.
- Do not disable a primary action merely to conceal incomplete validation. When an action is truly
  unavailable, explain what enables it; when submission begins, preserve the label, show progress,
  and prevent accidental duplicate effects at the system boundary.
- Preserve entered values after validation failure. Put a specific message beside each field and,
  for longer forms, provide a linked summary and deliberate focus placement.
- Match interruption to consequence. Prefer undo for cheap reversible actions; require explicit
  confirmation for irreversible or high-impact actions and name the object and consequence.
- Confirm completion, not just receipt of a click. State what changed, what happens next, and how the
  user can recover, revisit, or continue.

Read [forms-and-feedback.md](references/forms-and-feedback.md) when designing forms, validation,
loading and submission behavior, notifications, confirmations, empty states, or error recovery.

Read [mobile-and-responsive.md](references/mobile-and-responsive.md) for responsive layout, reflow,
zoom, safe areas, dynamic viewport height, or real-device verification. Read
[mobile-input-and-navigation.md](references/mobile-input-and-navigation.md) for on-screen keyboards,
mobile forms, hidden or fixed navigation, and action reach. Read
[responsive-data-and-connectivity.md](references/responsive-data-and-connectivity.md) for narrow-screen
tables, dense data, slow or interrupted requests, and offline transitions.

## Make accessibility part of quality

- Prefer semantic elements and native controls; add ARIA only for semantics HTML cannot express.
- Preserve logical heading, reading, and focus order. Keep focus visible and unobscured, and return
  it to a sensible location after dialogs or removed content.
- Give every control an accessible name and every input a persistent label. Add instructions for
  required formats and programmatically associated errors when needed.
- Make the complete flow work by keyboard, touch, pointer, voice, zoom, and reflow. Do not infer a
  user's input method from viewport width.
- Verify text, control, focus, and status contrast; never encode meaning with color alone.
- Provide text alternatives for meaningful images, empty alternatives for decoration, live status
  announcements where needed, and reduced-motion behavior.
- Use practical touch targets above the WCAG minimum when space permits; dense layouts still need
  sufficient size or separation to prevent adjacent activation.

Accessibility is a floor, not a visual style. Automated checks can find defects but cannot prove the
task is understandable or operable.

## Implement and prove the experience

Use real or representative content with realistic lengths. Build responsive behavior from content
pressure rather than arbitrary device names. Do not add a dependency or replace the design system to
recreate a primitive the project already has.

Render the result whenever tooling permits and inspect it rather than trusting source code. Verify:

1. The first viewport communicates purpose, hierarchy, current state, and the primary action.
2. Every interactive element exposes its purpose, enabled state, hover or pointer cue, visible focus,
   keyboard behavior, pressed feedback, and result.
3. Mobile, desktop, zoomed, long-content, loading, empty, error, and permission states preserve task
   order without clipping, overflow, layout shift, or dead ends.
4. Forms retain input, identify errors, focus the right place, prevent duplicate effects, and confirm
   the stored outcome.
5. Destructive actions provide recovery or consequence-appropriate confirmation.
6. Automated accessibility checks, keyboard traversal, and a representative screen-reader path pass;
   record what was tested rather than claiming general conformance.
7. Repeated elements share components and tokens, and the result remains specific to this product.

Compare approved references at the same viewport. Iterate while a difference affects hierarchy,
usability, responsiveness, accessibility, interaction feedback, or the chosen visual direction.

The evidence and lesson mapping for this package are in [sources.md](references/sources.md).

*Portions of this package are modified from Anthropic's Apache-2.0 `frontend-design` Agent Skill.
The notice, the license, and the modifications are recorded in
[references/sources.md](references/sources.md).*
