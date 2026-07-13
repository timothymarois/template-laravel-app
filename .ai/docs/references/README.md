# references/ — what we're building toward

**Mostly organized screenshots and visual assets** — competitor captures, screenshots of products we
admire, UI we want to feel like, concept art, generated studies — kept so we can **visually compare**
our work against the target. This is **input, never truth**: references set a *direction* and a bar;
they don't define behavior (that's `PRD/`) or how we build (that's `design/`).

Answers: *"What are we aiming at — the look, feel, and quality we want to match or beat?"* Its main job
is to anchor **visuals**: an agent generating or judging UI/art work puts its result next to these and
compares. So the assets themselves (the images) are the point — the notes just say what to look at.

## Structure

Store the actual images, grouped so they're easy to compare, alongside short notes that say *what to
look at*:

```
references/
  README.md
  <group>/              group by what you're comparing, e.g. competitors/  onboarding-screens/  concept-art/
    <asset>.png|jpg      the screenshot / capture / image
  <board>.md             a note curating a set of references for comparison (copy TEMPLATE.md)
```

Group by the aspect you're comparing (a screen, a flow, a visual style), so an agent can line the target
shots up against our result. Create groups as you need them — the names above are suggestions.

## Rules

- **Never an unannotated dump.** Every reference (or group) carries a line on *what inspires us here*
  and *what to avoid*. An image with no "why" teaches nothing.
- **Keep provenance.** Note where each came from (product, artist, generator + prompt). Respect
  licensing — references guide our own work; we don't ship someone else's asset.
- **Distinct from `research/`.** References = *what to build toward* (you look at them). Research = *how
  others built it* (you read it).
- **PII-free.** No private data in captures or notes; refer to people by role.

Copy `TEMPLATE.md` to start a reference board.
