# Mobile input and navigation

Use these patterns when an on-screen keyboard, mobile form, compact navigation, or reachable action
placement affects the task. Apply the universal target, gesture, and mixed-input rules from
[interaction-and-actions.md](interaction-and-actions.md); this file owns only mobile consequences.

## Keep the task visible above the keyboard

Good: let native focus scrolling work, then verify that the focused field, persistent label, nearby
instruction or error, and next action remain visible as the keyboard reduces the visual viewport.

Bad: let a fixed footer cover the field, trigger a surprise keyboard with autofocus, or leave the
only instruction above content that disappears when the keyboard opens.

Use `VisualViewport` only for complex overlays. Treat the VirtualKeyboard API as progressive
enhancement because support is limited. Feature-detect either path and test real keyboards in
portrait and landscape.

## Separate overlay coverage from keyboard clearance

Good: keep a full-screen modal and backdrop tied to the layout viewport; use visual-viewport overlap
only to inset its scrollable content or pinned action area. Lock background scrolling while open,
restore the prior position on close, and remove listeners and temporary styles on every exit path.

Bad: shrink the whole modal to the visual viewport, exposing the page around it, or hide overflow
without proving that iOS cannot drift the background document beneath the modal.

Overlay coverage, visible content height, and document scroll ownership are separate contracts. Test
them with the keyboard opening, changing size, closing, and reopening, including component unmount.

## Reduce input effort without lying about data

Good: use persistent labels, the truthful input `type`, a precise `autocomplete` token, and
`inputmode` as a keyboard hint. Use `enterkeyhint` when the next action is not obvious.

```html
<label for="phone">Phone number</label>
<input id="phone" name="phone" type="tel" autocomplete="tel" enterkeyhint="next">
```

Bad: use `type="number"` for phone, card, or account identifiers; split one value across avoidable
fields; force keyboard switching; or rely on a placeholder that vanishes during entry.

`inputmode` does not validate data. Keep format hints and errors beside the field, accept harmless
format variation, preserve input after failure, and let users submit to discover remaining errors.

## Keep navigation discoverable

Good: keep the highest-value destinations visible when space permits; put the remainder behind a
labeled `Menu` button with expanded state, current-location cues, and predictable focus. Keep the
primary task action available without covering content.

Bad: move destinations unpredictably between breakpoints, put commands in navigation, or bury every
destination and recovery action in an unlabeled hamburger or overflow menu.

Use overflow for lower-priority actions. If the menu is modal, follow the dialog focus contract; an
ordinary disclosure must not trap focus. Both need exposed expanded state and a clear close path.

## Test reach; do not invent a universal thumb zone

Good: test frequent and time-sensitive actions with left- and right-handed users, changing grips,
safe areas, browser UI, and the keyboard present.

Bad: hard-code every primary action to one corner because a thumb-zone diagram implies all people
hold every device the same way.

Keep destructive actions away from easy accidental activation. Reach evidence is observational and
varied; it supports testing placement, not one mandatory navigation layout.

## Prove mobile input

When present, verify the real keyboard and autofill on the first, middle, invalid, and last fields;
menu open/close and focus behavior; fixed actions with browser UI and keyboard visible; long and
translated labels; and touch, keyboard, pointer, voice, and screen-reader completion of the same task.
