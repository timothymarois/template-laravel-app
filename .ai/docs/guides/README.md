# guides/ — how to do a recurring task here

Operational how-to for procedures that repeat in *this* repo: the steps to run, in order, to get a known
result. A guide is a recipe, not a rationale — it tells you *how*, not *why* (`design/`/`PRD/` cover why).

Answers: *"How do I `<do the recurring task>` in this repo?"*

## Rules

- **One task per file.** Name it for the action: `guides/<verb-noun>.md`
  (e.g. `guides/add-a-migration.md`, `guides/generate-icons.md`).
- **Steps, in order.** A reader follows top to bottom and succeeds. Include the actual commands and the
  check that confirms it worked.
- **Keep it current.** A guide that no longer matches reality is worse than none — fix it in the task
  that changes the procedure.
- **Self-contained.** Everything needed is in the guide or linked within the repo; never a ticket.

Copy `TEMPLATE.md` to start a guide.
