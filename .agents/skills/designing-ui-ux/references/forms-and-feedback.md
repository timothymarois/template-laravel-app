# Forms and feedback

Use these patterns when a flow collects data, waits on work, reports a result, or recovers from a
failure.

## Ask only what the task needs

Good: keep persistent labels and concise hints, and accept harmless format variation.

Bad: use placeholders as labels or reject harmless punctuation and spacing differences.

Use the user's language, not database or implementation names. Preserve familiar values and choices
when the form reloads.

## Validate for recovery

Good: after submission, retain every safe value, identify each invalid field beside the control,
summarize errors for a long form, and move focus deliberately to the summary or first error.

Bad: clear the form, show `Invalid input`, use color alone, or place an error toast far from the
field with no way to reach it.

```text
Good: Enter an end date on or after 7 August 2026.
Bad:  Validation failed: date_range_invalid.
```

Accept unambiguous variations before reporting an error. Validate on submit by default; add earlier
validation only when evidence shows it helps, and do not report an error while the user is still
entering the value.

## Do not hide the route to validation

Good: the primary action remains available and submission explains every missing or invalid value.

Bad: the submit button stays disabled with no explanation, forcing the user to hunt for the field
that would enable it.

Disable an action only when it truly cannot operate. Put the reason and the step that enables it
nearby. Do not use low contrast as the only disabled signal.

## Show pending work without creating duplicates

Good: activation immediately preserves the action label, adds a progress cue, prevents accidental
repeat activation, and leaves cancellation available only when cancellation is real. When repeated
submission can duplicate an effect, guard it at the owning system boundary as well as in the UI.

Bad: nothing changes after activation, so users click again; or the button disappears and the page
provides no current state, outcome, or recovery.

```text
Good: [Saving changes…] → Changes saved
Bad:  [Save]            → no visible response
```

Use determinate progress only when it reflects measured completion. Do not animate a fabricated
percentage. For background work, say that the request was accepted, where its state can be checked,
and what happens next.

## Put feedback where the effect happened

Good: update the affected content and announce a concise status without unexpectedly moving focus.
Keep a durable receipt or reference for consequential transactions.

Bad: use an ephemeral toast as the only evidence that payment, deletion, publication, or another
high-impact action completed.

Status feedback answers: what happened, to what, whether it is complete, and what the user can do
next. Error feedback adds a recovery action. Do not expose stack traces, transport codes, or internal
identifiers as the explanation.

## Design empty and partial states as workflow states

Good: distinguish “no items yet,” “no matches,” “not permitted,” and “failed to load,” preserving
filters and offering the relevant next action.

Bad: show one generic blank panel for every cause or manufacture sample data and fake metrics to make
the screen look complete.

## Confirm completion and consequence

Good: after a transaction, state that it completed, include a reference when one exists, describe
what happens next, and provide the likely continuation.

Bad: return to the initial form with no confirmation or show `Success` without naming what succeeded.

Before an irreversible action, give the user a chance to verify the target and consequence. After a
reversible action, prefer a visible undo path over repeated confirmation friction.
