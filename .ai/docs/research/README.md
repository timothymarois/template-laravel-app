# research/ — what the world already figured out

Prior art. Notes on how *other* products, repos, papers, or articles solved a problem we're about to
face. This is **input, never truth** — a research note informs a design; it never dictates one, and it's
never the source of record for our behavior (that's `PRD/`).

Answers: *"What has the world already done about `<X>`, and what can we borrow or avoid?"*

## Rules

- **One source or topic per file.** Name it for what it covers: `research/<topic-or-source>.md`
  (e.g. `research/undo-redo-in-editors.md`, `research/competitor-auth-flows.md`).
- **Self-contained.** A reader gets the point from the note alone. Capture the *finding*, not a link
  dump — if the source vanishes, the note still teaches. Never cite an internal ticket or tracker.
- **Cited, not copied.** A `design/` or `PRD/` links to the note instead of re-explaining it. Keep the
  detail here once.
- **End with a verdict for us.** Every note says what *we* should take, skip, or watch out for — raw
  summary with no "so what" isn't done.
- **Distinct from `references/`.** Research = studied prior art you read to learn *how to build*.
  References = curated targets you look at to decide *what to build toward*.

Copy `TEMPLATE.md` to start a note.
