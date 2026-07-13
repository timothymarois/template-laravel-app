# research/ — prior art and what we're aiming at

External input. Notes on how *other* products, repos, papers, or articles solved a problem we're about
to face — plus the visual targets we want to match. This is **input, never truth**: research informs a
draft; it never dictates one, and it's never the source of record for our behavior (that's `PRD/`).

Answers: *"What has the world already done about `<X>`, and what are we aiming at?"*

## Rules

- **One source or topic per file.** `research/<topic-or-source>.md`
  (e.g. `research/undo-redo-in-editors.md`, `research/competitor-auth-flows.md`).
- **Self-contained.** A reader gets the point from the note alone — capture the *finding*, not a link
  dump. Never cite an internal ticket or tracker.
- **Cited, not copied.** A `PRD-drafts/` doc or `PRD/` links to the note instead of re-explaining it.
- **End with a verdict for us.** Say what *we* should take, skip, or watch out for — a raw summary with
  no "so what" isn't done.

## Visual references — `research/references/`

Screenshots, competitor captures, concept art, and UI we want to feel like go in the `references/`
subfolder — **organized so we can visually compare** our result against the target. The images are the
point; a short note beside them says what inspires us and what to avoid. Group by what you're comparing
(a screen, a flow, a style). Keep provenance, respect licensing, stay PII-free.

Copy `TEMPLATE.md` to start a note.
