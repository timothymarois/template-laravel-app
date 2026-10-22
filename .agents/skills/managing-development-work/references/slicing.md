# Slicing

Read this when a slice resists splitting, when the obvious split looks wrong, or when a slice list
feels done but nothing in it works yet.

## The one move behind every split

Find the part most likely to surprise you — the core complexity — and the variations that pass
through it: rules, data types, operations, roles, platforms. Reduce the variations to
one, and make the first slice one complete path through the hard part. Every pattern below is that
move applied to a particular kind of variation.

## Patterns, tried in this order

| Pattern | Split | Example |
|---|---|---|
| Workflow steps | The simplest path from first step to last, then the middle steps and special cases. The first and last steps usually carry most of the value. | Checkout: one product, card payment, confirmation email. Coupons, saved addresses, and guest checkout come later. |
| Operations | One operation at a time. A "manage" or "handle" in the request is the signal. | "Manage team members": invite first, then remove, then change role. |
| Rule variations | One rule end to end, then each alternative as its own slice. | Pricing: flat rate first; volume tiers and regional tax each follow. |
| Data variations | A good-enough model for one kind of data, then each variant. | Import CSV first; XLSX and the API feed later. One language before localization. |
| Interface | The plainest input that works, then the polished one. These slices are not independent; say so. | A text field for the date before the date picker. |
| Major effort | Do the first variant, which carries the shared groundwork, and defer choosing which comes next. | The first payment provider builds the integration layer; the second is small. |
| Simple, then complex | The simplest version as its own slice, then every complication as its own. | Search by exact name before fuzzy match, filters, and ranking. |
| Defer quality attributes | Make it work, then make it fast, secure, or scalable — and never call the deferred part done. | Correct report on a sample, then the paginated version for the full dataset. |
| Spike | Last resort, when no split is possible until a question is answered. Time-box it, name the question, stop when it is answered, then slice for real. | "Can the engine render 10,000 tiles at 60 fps?" — a throwaway test scene, one day. |

When several patterns fit, prefer the split that lets the low-value part be dropped, then the one
that gives slices of similar size.

## Traps

| Symptom | Cause | Replacement | Proof it is fixed |
|---|---|---|---|
| Every slice is "done" and nothing can be used | Split by layer or discipline | Re-cut so each slice crosses every layer for one path | The first slice can be demonstrated alone |
| A slice has no observable result | A task posing as a slice | Merge it into the slice it serves | Each slice names a check someone can run or see |
| The first slice is the easy setup work | Ordered by comfort, not risk | Start with the core, novel piece and stub around it | The riskiest unknown is resolved by slice one or two |
| Twenty detailed slices for uncertain work | False precision | Detail the next two or three; name the rest as outcomes | The list changes as work teaches, without rewriting history |
| The current slice keeps growing | New scope absorbed into work in progress | Make each addition a `~` or a later slice | The slice closes against its original proof |
| A spike turns into a week of research | No question or time box | One question, one time box, stop when answered | The spike ends with a decision, not a report |
| Steps split one at a time — "step 1 of the form" | The obvious split, which delivers nothing usable | Take one thin path through every step first | A person can finish the journey on the first slice |
