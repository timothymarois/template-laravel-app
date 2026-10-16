# Mobile and responsive UI/UX

Use these patterns for responsive web interfaces and mobile web apps. Do not treat “mobile” as one
screen size, one input method, or a slow network. Each pair is mapped to standards, maintained design
systems, or empirical practitioner research in [sources.md](sources.md).

## Preserve the task, not the desktop arrangement

Good: keep the same information, functions, and task order while recomposing them around available
space and content priority.

Bad: shrink the desktop canvas, hide useful content because it does not fit, or rearrange controls
without preserving reading and focus order.

Start narrow, then add layout only when the content needs room. Choose breakpoints from wrapping,
crowding, line length, and task failure—not device names or last year's phone widths. A responsive
codebase does not guarantee a usable mobile experience.

## Set the viewport and preserve zoom

Good:

```html
<meta name="viewport" content="width=device-width, initial-scale=1">
```

Bad: omit the viewport so the browser scales down a desktop-width page, or add `user-scalable=no` or
a restrictive `maximum-scale` that prevents needed zoom.

At 320 CSS pixels, ordinary page content must reflow without losing information or functionality or
requiring two-dimensional scrolling. Keep genuine two-dimensional content such as a data grid inside
its own labeled scroll region; the rest of the page still reflows.

## Keep controls clear of hardware and browser UI

Good: keep normal browser insetting unless edge-to-edge rendering is intentional. With
`viewport-fit=cover`, allow backgrounds to extend while keeping essential content and fixed controls
inside safe-area insets.

```css
.bottom-actions {
  padding: 1rem;
}

@supports (padding: max(0px)) {
  .bottom-actions {
    padding-right: max(1rem, env(safe-area-inset-right));
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
    padding-left: max(1rem, env(safe-area-inset-left));
  }
}
```

Bad: enable full-bleed rendering, then pin the primary action to `bottom: 0` where a home indicator,
rounded corner, or fold can obscure it.

Use user-agent safe-area and viewport-segment values rather than hard-coded device dimensions. Test
every fixed edge after rotation. Safe-area insets do not solve changing browser-bar or keyboard
height.

## Choose viewport height deliberately

Good: prefer document flow. For a true screen-height layout, choose `svh` for a stable height that
fits with browser UI visible or `dvh` when the layout must follow expanding and collapsing browser
bars; retain an appropriate fallback.

Bad: assume `100vh` always means the unobscured screen or use JavaScript window-height snapshots for
ordinary page layout.

Viewport units do not solve every keyboard behavior. Test short-height layouts and browser UI in
both expanded and collapsed states before adding measurement code.

## Preserve layout ancestry across responsive branches

Good: keep the same scroll container and containing-block geometry when a breakpoint or capability
changes wrappers; make any necessary wrapper span the region its sticky child must travel through.

Bad: add a shallow touch-only wrapper around a sticky header, then debug the header's `top` value when
it stops at that wrapper's edge.

Sticky positioning is bounded by its containing block and nearest ancestor with a scrolling
mechanism. Inspect the rendered ancestor chain in every structural branch; source review alone does
not prove scrolling behavior.

## Prove mobile behavior

Use emulation for breadth, then representative real devices from the supported platforms for
behavior it cannot reproduce faithfully. Always verify:

1. 320 CSS-pixel reflow, 200% text resize, zoom, long content, and translated content.
2. Portrait, landscape, a short-height viewport, split-screen or resized windows, and safe areas.
3. Touch, keyboard, and a fine pointer on the same responsive layout.
4. Mobile screen-reader traversal and meaningful reading and focus order.

When present, also verify edge-to-edge safe areas after rotation, screen-height layouts with browser
UI expanded and collapsed, sticky elements through their full scroll range, and fixed controls
without obscuring content. In server-rendered apps, hydrate both sides of responsive or capability
queries and keep the server and first client DOM compatible. The routed mobile input and data
references own their additional checks.

Record devices, browsers, viewport conditions, input modes, and network profiles actually tested.
“Responsive in DevTools” is not proof of the complete mobile task.
