# Coordination

## Risk responses

Name the trigger and add only its evidence. Do not route every change through a fixed sequence of
specialists.

| Trigger | Required response |
|---|---|
| Authentication, authorization, secrets, privacy | Trace trust boundaries, redaction and refusal paths. |
| Money, entitlement, irreversible business state | Preserve invariants and define reconciliation. |
| Schema, migration, deletion, stored data | Define compatibility, recovery and post-change checks. |
| Production, infrastructure, deployment | Separate implementation from rollout; define rollback and health proof. |
| Public API, format, integration | Identify the actual consumers and prove compatibility. |
| Concurrency, performance, broad blast radius | Measure a representative baseline and the relevant limit. |

## When the boundary must grow

If the outcome genuinely needs a larger boundary, stop before changing it. Present the smallest
viable expansion, why the original boundary fails, the contracts affected, the risks, alternatives
and proof. Sunk effort and passing tests do not authorize new scope. For an explicit refactor,
characterize preserved behavior first and change it in reversible steps.

## Delegating

Keep decisions, dependency order, integration and the final report with one responsible agent. Use
one implementer per phase; parallelize only phases with separate edit boundaries, when the value beats
the coordination cost. Work across repositories or across more than one material risk becomes
separate, ordered phases, each leaving a coherent result with its own proof.

A handoff states one testable outcome and why; the files or boundary, permissions and prohibited
changes; size, compatibility, risk and authority limits; the repository rules and skills that apply;
the expected artifacts and exact proof; and when to stop. Send implementation to an implementer and
review to a reviewer only once the integrated change is inspectable, naming the exact base and head,
the acceptance criteria and the few invariants most at risk.

## Integrating

A returned summary is not integration. Inspect the full diff, the surrounding contracts and the
reported evidence; rerun the narrowest meaningful checks and then the repository's gate on the
integrated result; exercise a safe representative path. Allow a focused correction for a contained
miss; if the correction reveals a different design, re-scope instead of layering patches.
