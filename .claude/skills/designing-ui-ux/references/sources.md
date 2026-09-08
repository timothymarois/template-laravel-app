# Sources

Accessed 7 August 2026. Standards establish minimum behavior; maintained design systems and
practitioner research show how teams apply those contracts and where users encounter failures.
External cursor conventions disagree. This catalog therefore records `cursor: pointer` for every
enabled activation target as its own universal UI standard, not as a WCAG or CSS requirement.

## Upstream adaptation

- [Anthropic `frontend-design` at commit `2235be7c`](https://github.com/anthropics/skills/tree/2235be7c60b551f5de82ade908fd3816455afcda/skills/frontend-design)
  supplies the Apache-2.0 base for subject-specific visual direction, deliberate composition, and a
  memorable signature element. This adaptation retains those ideas while adding the sourced UI/UX
  workflow, interaction rules, failure patterns, and verification criteria in this package.
  The notice, the full license, and the modifications are recorded under `## Attribution` below.

## Core UX workflow

- [Jakob Nielsen, “10 Usability Heuristics for User Interface Design”](https://www.nngroup.com/articles/ten-usability-heuristics/)
  was last reviewed in 2024 and traces the heuristics to a factor analysis of 249 usability problems.
  It supports visible system status, user control and undo, internal and external consistency, error
  prevention, recognition over recall, focused visual design, and actionable error recovery.
- [Carbon Design System: Button usage](https://carbondesignsystem.com/components/button/usage/)
  supports one clear action hierarchy, specific action labels, distinct interactive states, keyboard
  activation, and inline loading feedback. It provides the source pattern for the good/bad label and
  state examples.

## Semantics, keyboard, and focus

- [WAI-ARIA Authoring Practices: Button pattern](https://www.w3.org/WAI/ARIA/apg/patterns/button/)
  distinguishes commands from links, requires accessible naming, and defines `Enter`, `Space`, and
  post-action focus behavior. It supports the semantic control and dialog-focus examples.
- [WAI-ARIA Authoring Practices: Link pattern](https://www.w3.org/WAI/ARIA/apg/patterns/link/)
  recommends native links and warns that adding `role="link"` does not create navigation, context
  menu, or keyboard behavior.
- [WAI-ARIA Authoring Practices: Modal dialog pattern](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/)
  defines contained tab order, `Escape`, initial focus, and focus return. It supports the modal
  good/bad pair.
- [MDN: `<button>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/button)
  documents multi-modal activation, the form-submit default, explicit `type="button"`, accessible
  names for icon buttons, and target-size considerations.
- [WAI-ARIA Authoring Practices: Tooltip pattern](https://www.w3.org/WAI/ARIA/apg/patterns/tooltip/)
  defines a tooltip as non-interactive descriptive content shown on hover or keyboard focus and
  directs interactive popups to a non-modal dialog pattern.

## Affordance, cursors, states, and targets

- [MDN: `cursor`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/cursor)
  maps cursors to operations and describes `pointer` as indicating a link. It supports treating a
  cursor as contextual feedback rather than semantics.
- [Adobe Spectrum: Button](https://spectrum.adobe.com/page/button/) and
  [States](https://spectrum.adobe.com/page/states/) deliberately use the default arrow for command
  buttons, reserve the hand for link buttons, and define default, hover, down, keyboard-focus, and
  disabled states.
- [SAP Fiori: Cursors](https://www.sap.com/design-system/fiori-design-web/v1-136/foundations/interaction/cursors)
  uses the pointer over clickable elements. Spectrum and MDN establish that external conventions
  differ; SAP demonstrates the activation-cursor convention this catalog adopts universally.
- [WCAG 2.2](https://www.w3.org/TR/WCAG22/) establishes keyboard operation, visible and unobscured
  focus, status messages, orientation independence, concurrent input, pointer-gesture and dragging
  alternatives, pointer cancellation, and a 24-by-24 CSS-pixel AA target-size minimum with
  exceptions. Its 44-by-44 enhanced target and
  [Spectrum platform scale](https://spectrum.adobe.com/page/platform-scale/), which targets 48-pixel
  touch areas where possible, support the larger practical default.
- [Carbon Design System: Tile usage](https://carbondesignsystem.com/components/tile/usage/)
  documents whole-tile activation, separate targets when a tile contains independent controls, and
  the ambiguity caused by multiple click targets. It supports the clickable-container pair.
- [W3C: Understanding Content on Hover or Focus](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus.html)
  requires additional content to be dismissible, hoverable, and persistent, and explains why a
  pointer-triggered disclosure must also work through keyboard focus.

## Forms, feedback, and recovery

- [GOV.UK Design System: Button](https://design-system.service.gov.uk/components/button/) supports
  specific action labels, one primary action, avoiding disabled buttons without research, prompt
  feedback on slow operations, and protection against duplicate submission. Its documented Notify
  case found duplicate invitations caused by double-clicking, and it requires server-side protection
  in addition to a JavaScript double-click guard.
- [GOV.UK Design System: Recover from validation errors](https://design-system.service.gov.uk/patterns/validation/)
  supports accepting harmless format variation, preserving entered values, inline errors, a focused
  summary, and submit-time validation unless research establishes a need for earlier feedback.
- [WAI Forms Tutorial: Labeling controls](https://www.w3.org/WAI/tutorials/forms/labels/) and
  [WCAG: Labels or Instructions](https://www.w3.org/WAI/WCAG22/Understanding/labels-or-instructions.html)
  support persistent visible labels, programmatic association, and instructions for required input
  formats. They provide the source for the placeholder-only good/bad pair.
- [WCAG: Error Identification](https://www.w3.org/WAI/WCAG22/Understanding/error-identification.html)
  requires errors to identify the affected item and describe the error in text. It supports the
  specific corrective-message example.
- [GOV.UK Design System: Error summary](https://design-system.service.gov.uk/components/error-summary/)
  requires consistent linked error messages and deliberate focus. It supports the long-form recovery
  pattern.
- [GOV.UK Design System: Confirmation pages](https://design-system.service.gov.uk/patterns/confirmation-pages/)
  supports naming the completed transaction, references, next steps, contact or recovery routes, and
  durable records for consequential work.
- [Carbon Design System: Remove pattern](https://carbondesignsystem.com/community/patterns/remove-pattern/)
  scales confirmation to impact: undo or no modal for low-impact reversible removal, consequences
  and explicit confirmation for irreversible deletion, and stronger identity checks for catastrophic
  loss. It supports the destructive-action pair.
- [Carbon Design System: Empty states](https://carbondesignsystem.com/patterns/empty-states-pattern/)
  distinguishes first-use/no-data, no-results, permission, system, and configuration states and
  routes each to a contextual explanation and next action. It supports the empty-state good/bad pair.

## Mobile and responsive behavior

- [web.dev: Responsive web design basics](https://web.dev/articles/responsive-web-design-basics)
  supports the viewport declaration, preserving zoom, flexible layouts, content-led breakpoints,
  capability media queries, and not hiding content merely because the screen is small.
- [WCAG: Understanding Reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html) establishes
  the 320 CSS-pixel AA reflow requirement, its two-dimensional-content exception, and the rule that
  the exception applies only to the content that requires it.
- [web.dev: Accessible responsive design](https://web.dev/articles/accessible-responsive-design)
  connects responsive layout to zoom accessibility and requires testing reading and focus order at
  each breakpoint.
- [MDN: Layout and the containing block](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Display/Containing_block)
  and [CSS `position`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/position)
  establish how a sticky element's containing block and nearest scrolling ancestor bound its travel.
- [MDN: CSS `env()`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/env) and the
  [CSS Environment Variables specification](https://www.w3.org/TR/css-env-1/) define safe-area,
  keyboard-inset, and viewport-segment variables. They support the edge-to-edge good/bad pair without
  hard-coded device dimensions.
- Timothy Horton's WebKit article [“Designing Websites for iPhone X”](https://webkit.org/blog/7929/designing-websites-for-iphone-x/)
  demonstrates why `viewport-fit=cover` requires safe-area padding and why `max()` preserves a normal
  minimum inset. It supports this technique, not adding full-bleed rendering to every page.
- [web.dev: The large, small, and dynamic viewport units](https://web.dev/blog/viewport-units)
  distinguishes `lvh`, `svh`, and `dvh`, records their browser-UI tradeoffs, and notes that the
  on-screen keyboard does not generally affect these units.
- [MDN: Visual Viewport](https://developer.mozilla.org/en-US/docs/Web/API/VisualViewport) explains
  that on-screen keyboards and pinch zoom can shrink the visible viewport without changing the
  layout viewport.
- [MDN: VirtualKeyboard API](https://developer.mozilla.org/en-US/docs/Web/API/VirtualKeyboard_API)
  documents browser-default keyboard resizing, keyboard geometry and inset values, and the API's
  limited availability. It supports default browser handling plus progressive enhancement.
- [web.dev: Form attributes in depth](https://web.dev/learn/forms/attributes/) supports semantic
  input types, `inputmode`, `enterkeyhint`, avoiding `type="number"` for identifiers, avoiding
  surprise autofocus, and leaving submit available for validation.
- [MDN: `autocomplete`](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Attributes/autocomplete)
  defines field-purpose tokens and explains how they let user agents offer appropriate form-filling
  assistance. It supports the precise-token example.
- Jamie Holst's 2015 [Baymard mobile checkout and form study](https://baymard.com/blog/mobile-ecommerce-checkout-forms)
  reports 5,200 manually assigned usability scores across 50 major mobile commerce sites and observed
  reduced context with keyboards, placeholder-label failures, nearby-instruction needs, redundant
  entry, and keyboard mismatches. Its e-commerce scope supports the mobile form examples but not a
  universal checkout layout.
- Kara Pernice and Raluca Budiu's [NN/g hidden-navigation study](https://www.nngroup.com/articles/hamburger-menus/)
  tested 179 participants on six live sites and found discoverability and task costs from fully
  hidden navigation. Its strongest mobile result favors partially visible or combined navigation;
  it does not prove every destination must remain visible.
- Steven Hoober's [field observations of mobile grip](https://www.uxmatters.com/mt/archives/2013/02/how-do-users-really-hold-mobile-devices.php)
  recorded 1,333 naturalistic observations, including 780 active screen interactions. The varied
  grips support testing action reach and accidental activation, not a universal thumb-zone layout.
- [U.S. Web Design System: Table](https://designsystem.digital.gov/components/table/) recommends
  minimizing columns, horizontally scrolling wide numerical comparisons, and stacking directory-like
  rows with programmatic labels. It supports choosing a mobile data representation by comparison task.
- [Carbon Design System: Data table](https://carbondesignsystem.com/components/data-table/usage/)
  documents keeping overflow actions persistent when hover is unavailable on mobile and touch.
- [web.dev: Adaptive loading](https://web.dev/articles/adaptive-loading-cds-2019) supports a fast
  baseline and optional enhancement for network or hardware constraints; it also records limited
  browser coverage for several capability signals.
- [MDN: Save-Data](https://developer.mozilla.org/docs/Web/HTTP/Reference/Headers/Save-Data) defines an
  explicit reduced-data preference and warns that support remains limited.
- [web.dev: Offline UX design](https://web.dev/articles/offline-ux-design-guidelines) supports making
  connectivity state visible, preserving useful cached work, and designing explicit slow, offline,
  reconnection, and synchronization states.
- [MDN: Page Visibility API](https://developer.mozilla.org/en-US/docs/Web/API/Page_Visibility_API)
  establishes the visible/hidden lifecycle signal, and [web.dev: Back/forward cache](https://web.dev/articles/bfcache)
  documents persisted `pageshow` after a cached page is restored. They support reconciliation after
  browser suspension; they do not imply that every page must refetch everything.
- [Apple Human Interface Guidelines: Layout](https://developer.apple.com/design/human-interface-guidelines/layout)
  provides platform-practitioner evidence for safe areas, orientation, resizing, text changes, and
  locale changes. This native guidance reinforces the test matrix; it does not define web behavior.
- [Chrome DevTools: Device Mode](https://developer.chrome.com/docs/devtools/device-mode) explicitly
  describes device emulation as a first-order approximation and directs teams to real devices when
  in doubt. It supports the final verification boundary.

## Anonymized production field evidence

These reproductions were audited 7 August 2026. Private implementation and owner identifiers are
omitted; the public sources above define the auditable platform and accessibility contracts.

- A touch-only wrapper became a sticky header's shallow containing block, so the header stopped at
  the wrapper edge. Rendered scroll testing proved the repaired ancestry; the MDN sources generalize
  the contract without prescribing the implementation's `display: contents` fix.
- Three iterations on a full-screen mobile dialog separated keyboard-clear inner content, complete
  overlay coverage, and background scroll ownership. Unit tests covered changing visual-viewport
  overlap, close, unmount, scroll restoration, and cleanup.
- A backgrounded realtime view missed invalidations, while a restored page retained incomplete
  deferred state. Visibility return and persisted `pageshow` reconciliation recovered canonical
  state; the skill omits the implementation's unproven watchdog timing.
- A portaled suggestion popup closed its parent modal when trailing mobile pointer/focus events arrived
  after the child unmounted. Explicit nested-overlay ownership fixed the reproduction; the skill does
  not retain the implementation's tuned grace duration.
- Immediate touch drag activation stole scroll flicks, and a separate card reproduction assigned
  competing long-press recognition to context-menu and reorder behavior. A dedicated handle,
  one gesture owner, intent tolerance, and explicit menu controls fixed the paths. Later retuning
  confirmed that exact time and distance thresholds are project/device choices.

## Scope limits

- These sources do not establish `cursor: pointer` on command buttons as a WCAG requirement. It is a
  deliberate catalog standard. Button order, validation timing, and confirmation patterns remain
  context-dependent and should be tested with representative users when consequential.
- Web target sizes use CSS pixels; do not substitute native-platform units. Safe-area handling is
  for intentional edge-to-edge layouts, and viewport units do not solve every keyboard behavior.
- WCAG conformance cannot be inferred from source inspection or automated checks alone. Verify the
  rendered task with keyboard and representative assistive technology, and report the tested scope.

## Attribution

**This package contains Apache-2.0 licensed material.** The rest of this catalog is MIT; this
package is the exception, and the notice below travels with it.

Portions are modified from Anthropic's `frontend-design` Agent Skill, at
<https://github.com/anthropics/skills/tree/2235be7c60b551f5de82ade908fd3816455afcda/skills/frontend-design>,
licensed under the Apache License, Version 2.0. Those portions supply the base for subject-specific
visual direction, deliberate composition, and choosing one signature element.

That material reached this package by way of the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, path `skills/frontend-design/`, which had already
adapted it and added the sourced UI/UX workflow, interaction rules, failure patterns, and
verification criteria recorded above.

### Statement of modifications

As required by section 4(b) of the license, the files in this package are modified from the original
work. The modifications are:

- the package is renamed `designing-ui-ux` and its routing description rewritten for this catalog;
- the sourced UI/UX workflow, interaction and cursor rules, forms and feedback guidance, responsive
  and mobile references, accessibility criteria, and rendered-verification steps were added by
  Rundesk AI in the intermediate catalog and are carried forward here;
- `references/naming.md` was added in this catalog, adapted from the MIT-licensed naming and grammar
  guidance described below;
- the in-package `LICENSE.txt` and the pointer to a repository-level third-party notices file were
  removed, because this catalog's package contract permits only `SKILL.md` and `references/`; the
  notice and the full license are reproduced here instead; and
- a maintainer validation record was added.

### Apache License 2.0

```text
Apache License
Version 2.0, January 2004
http://www.apache.org/licenses/

TERMS AND CONDITIONS FOR USE, REPRODUCTION, AND DISTRIBUTION

1. Definitions.

"License" shall mean the terms and conditions for use, reproduction, and
distribution as defined by Sections 1 through 9 of this document.

"Licensor" shall mean the copyright owner or entity authorized by the
copyright owner that is granting the License.

"Legal Entity" shall mean the union of the acting entity and all other
entities that control, are controlled by, or are under common control with
that entity. For the purposes of this definition, "control" means (i) the
power, direct or indirect, to cause the direction or management of such
entity, whether by contract or otherwise, or (ii) ownership of fifty percent
(50%) or more of the outstanding shares, or (iii) beneficial ownership of
such entity.

"You" (or "Your") shall mean an individual or Legal Entity exercising
permissions granted by this License.

"Source" form shall mean the preferred form for making modifications,
including but not limited to software source code, documentation source, and
configuration files.

"Object" form shall mean any form resulting from mechanical transformation
or translation of a Source form, including but not limited to compiled object
code, generated documentation, and conversions to other media types.

"Work" shall mean the work of authorship, whether in Source or Object form,
made available under the License, as indicated by a copyright notice that is
included in or attached to the work (an example is provided in the Appendix
below).

"Derivative Works" shall mean any work, whether in Source or Object form,
that is based on (or derived from) the Work and for which the editorial
revisions, annotations, elaborations, or other modifications represent, as a
whole, an original work of authorship. For the purposes of this License,
Derivative Works shall not include works that remain separable from, or merely
link (or bind by name) to the interfaces of, the Work and Derivative Works
thereof.

"Contribution" shall mean any work of authorship, including the original
version of the Work and any modifications or additions to that Work or
Derivative Works thereof, that is intentionally submitted to Licensor for
inclusion in the Work by the copyright owner or by an individual or Legal
Entity authorized to submit on behalf of the copyright owner. For the purposes
of this definition, "submitted" means any form of electronic, verbal, or
written communication sent to the Licensor or its representatives, including
but not limited to communication on electronic mailing lists, source code
control systems, and issue tracking systems that are managed by, or on behalf
of, the Licensor for the purpose of discussing and improving the Work, but
excluding communication that is conspicuously marked or otherwise designated
in writing by the copyright owner as "Not a Contribution."

"Contributor" shall mean Licensor and any individual or Legal Entity on behalf
of whom a Contribution has been received by Licensor and subsequently
incorporated within the Work.

2. Grant of Copyright License. Subject to the terms and conditions of this
License, each Contributor hereby grants to You a perpetual, worldwide,
non-exclusive, no-charge, royalty-free, irrevocable copyright license to
reproduce, prepare Derivative Works of, publicly display, publicly perform,
sublicense, and distribute the Work and such Derivative Works in Source or
Object form.

3. Grant of Patent License. Subject to the terms and conditions of this
License, each Contributor hereby grants to You a perpetual, worldwide,
non-exclusive, no-charge, royalty-free, irrevocable (except as stated in this
section) patent license to make, have made, use, offer to sell, sell, import,
and otherwise transfer the Work, where such license applies only to those
patent claims licensable by such Contributor that are necessarily infringed by
their Contribution(s) alone or by combination of their Contribution(s) with
the Work to which such Contribution(s) was submitted. If You institute patent
litigation against any entity (including a cross-claim or counterclaim in a
lawsuit) alleging that the Work or a Contribution incorporated within the Work
constitutes direct or contributory patent infringement, then any patent
licenses granted to You under this License for that Work shall terminate as of
the date such litigation is filed.

4. Redistribution. You may reproduce and distribute copies of the Work or
Derivative Works thereof in any medium, with or without modifications, and in
Source or Object form, provided that You meet the following conditions:

(a) You must give any other recipients of the Work or Derivative Works a copy
of this License; and

(b) You must cause any modified files to carry prominent notices stating that
You changed the files; and

(c) You must retain, in the Source form of any Derivative Works that You
distribute, all copyright, patent, trademark, and attribution notices from the
Source form of the Work, excluding those notices that do not pertain to any
part of the Derivative Works; and

(d) If the Work includes a "NOTICE" text file as part of its distribution,
then any Derivative Works that You distribute must include a readable copy of
the attribution notices contained within such NOTICE file, excluding those
notices that do not pertain to any part of the Derivative Works, in at least
one of the following places: within a NOTICE text file distributed as part of
the Derivative Works; within the Source form or documentation, if provided
along with the Derivative Works; or, within a display generated by the
Derivative Works, if and wherever such third-party notices normally appear.
The contents of the NOTICE file are for informational purposes only and do not
modify the License. You may add Your own attribution notices within Derivative
Works that You distribute, alongside or as an addendum to the NOTICE text from
the Work, provided that such additional attribution notices cannot be
construed as modifying the License.

You may add Your own copyright statement to Your modifications and may provide
additional or different license terms and conditions for use, reproduction, or
distribution of Your modifications, or for any such Derivative Works as a
whole, provided Your use, reproduction, and distribution of the Work otherwise
complies with the conditions stated in this License.

5. Submission of Contributions. Unless You explicitly state otherwise, any
Contribution intentionally submitted for inclusion in the Work by You to the
Licensor shall be under the terms and conditions of this License, without any
additional terms or conditions. Notwithstanding the above, nothing herein
shall supersede or modify the terms of any separate license agreement you may
have executed with Licensor regarding such Contributions.

6. Trademarks. This License does not grant permission to use the trade names,
trademarks, service marks, or product names of the Licensor, except as required
for reasonable and customary use in describing the origin of the Work and
reproducing the content of the NOTICE file.

7. Disclaimer of Warranty. Unless required by applicable law or agreed to in
writing, Licensor provides the Work (and each Contributor provides its
Contributions) on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, either express or implied, including, without limitation, any warranties
or conditions of TITLE, NON-INFRINGEMENT, MERCHANTABILITY, or FITNESS FOR A
PARTICULAR PURPOSE. You are solely responsible for determining the
appropriateness of using or redistributing the Work and assume any risks
associated with Your exercise of permissions under this License.

8. Limitation of Liability. In no event and under no legal theory, whether in
tort (including negligence), contract, or otherwise, unless required by
applicable law (such as deliberate and grossly negligent acts) or agreed to in
writing, shall any Contributor be liable to You for damages, including any
direct, indirect, special, incidental, or consequential damages of any
character arising as a result of this License or out of the use or inability
to use the Work (including but not limited to damages for loss of goodwill,
work stoppage, computer failure or malfunction, or any and all other commercial
damages or losses), even if such Contributor has been advised of the
possibility of such damages.

9. Accepting Warranty or Additional Liability. While redistributing the Work
or Derivative Works thereof, You may choose to offer, and charge a fee for,
acceptance of support, warranty, indemnity, or other liability obligations
and/or rights consistent with this License. However, in accepting such
obligations, You may act only on Your own behalf and on Your sole
responsibility, not on behalf of any other Contributor, and only if You agree
to indemnify, defend, and hold each Contributor harmless for any liability
incurred by, or claims asserted against, such Contributor by reason of your
accepting any such warranty or additional liability.

END OF TERMS AND CONDITIONS
```

### Naming guidance

`references/naming.md` is adapted from the naming and grammar guidance in the Rundesk skills catalog
at <https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, path `skills/naming-grammar-conventions/`, published by
Rundesk AI under the MIT License. Its interface-text sections — capitalization and punctuation,
column headers, field labels, placeholders, buttons and menu items, enum display values, metrics and
charts, empty states, validation, conflict, system and partial-failure errors, and success
confirmation — are carried here and rewritten for a design audience, so an interface can be named
correctly without a second package installed. That package's wider scope, covering code identifiers,
stored data, and the product lexicon, is deliberately not reproduced.
