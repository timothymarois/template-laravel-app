# Interaction and actions

Use these patterns when a control's purpose, state, or activation is unclear. Each pair is mapped to
the standards and practitioner evidence in

## Match element to behavior

Good: navigation uses a real link; an in-place command uses a real button.

```html
<a href="/reports">View reports</a>
<button type="button">Refresh report</button>
```

Bad: generic elements and fake links require the application to recreate navigation, keyboard,
focus, and assistive-technology behavior.

```html
<div onclick="openReports()">View reports</div>
<a href="#" onclick="refreshReport()">Refresh report</a>
```

Use custom ARIA widgets only when no native element supplies the required behavior. A role changes
what assistive technology is told; it does not add browser behavior.

## Make clickability visible

Good: the control has an action label, stable shape, adequate hit area, visible hover/focus/pressed
states, and a pointer cursor when enabled.

```html
<button type="button" class="button">Add member</button>
```

Apply the hand cursor consistently to enabled activation targets:

```css
.button:not(:disabled) { cursor: pointer; }
```

Bad: a cursor utility is the only evidence that an arbitrary region can be activated.

```html
<div class="cursor-pointer" onclick="addMember()">Add member</div>
```

## Expose every state

Good: one component defines default, hover, focus, pressed, selected or expanded, loading, disabled,
success, and error behavior. Its size and label remain stable as state changes.

Bad: only `:hover` is designed; keyboard focus disappears, pressing gives no response, and the
loading spinner replaces the label with no indication of what is running.

Hover, focus, and pressed are different signals. Focus must remain visible; a hover treatment cannot
be the only clue because touch and keyboard users may never see it. If hover or focus reveals a
tooltip or popover, make it available on both, dismissible, hoverable, and persistent long enough to
use.

## Keep targets forgiving

Good: the visible control and its full padded area activate the same action; adjacent compact targets
have enough size or separation; drag interactions also offer click or tap controls.

Bad: only the icon path or label text is clickable, adjacent actions overlap, or reordering is
possible only through precise drag movement.

Treat WCAG's 24 by 24 CSS pixel AA target as a floor with exceptions, not the design goal. Prefer a
larger touch target such as 44 to 48 CSS pixels when layout permits. Do not assume a narrow viewport
means touch or a wide viewport means a mouse.

For drag-to-reorder, prefer a dedicated handle and tune touch intent so a scroll flick does not arm a
drag. Give each touch sequence one gesture owner: a context-menu long press and a reorder long press
must not compete. Keep explicit tap controls for the same operation. Verify tap, scroll, deliberate
drag, cancellation, and mixed-input use; timings are product and device tuning, not universal values.

## Keep one interactive owner

Good: a navigational card has one link covering the card, while separate actions sit outside that
link and remain independent targets.

Bad: a clickable card contains another link, menu, or button, creating nested targets and ambiguous
activation.

If a container has several independent actions, make the container non-interactive and expose each
action separately. Do not attach one click handler to a row that also contains checkboxes, menus, or
links.

## Label icons and disclosure

Good: every icon button has an accessible name. When an icon's meaning is unclear in context, use a
visible text label. A menu button indicates that it opens choices.

Bad: an unlabeled icon relies on recognition or hover-only tooltip text; an ellipsis performs an
immediate destructive action.

Do not make tooltips carry essential instructions. When a tooltip is useful, show it on focus and
hover, associate it with the trigger, keep it dismissible, and use a non-modal dialog instead when
the revealed content must itself be interactive.

## Manage dialog focus

Good: opening a modal moves focus inside; `Tab` stays within it; `Escape` closes when safe; closing
returns focus to the opener or the next logical element.

Bad: focus remains behind the overlay, the background still operates, or closing sends focus to the
start of the page.

Use a modal only when the interruption is necessary. Keep the title, consequence, primary action,
and cancellation path concise; long workflows belong on a page.

When a child popover is portaled outside the modal DOM, coordinate its ownership with the parent.
Selecting the child must not be misread as outside dismissal merely because focus or a trailing touch
event lands after the child unmounts. Suppress parent dismissal only for the reproduced child
interaction window; preserve normal outside and `Escape` behavior, nesting, and focus return.

## Scale friction to consequence

Good: a reversible removal happens immediately and offers undo; an irreversible deletion names the
resource, explains the loss, and requires an explicit confirmation proportional to impact.

Bad: every low-risk action opens an “Are you sure?” dialog, while a catastrophic delete uses the
same generic confirmation and a preselected destructive action.

Confirmation cannot repair a vague label. Say `Delete project` and identify the project and
irreversible consequence before the final action.
