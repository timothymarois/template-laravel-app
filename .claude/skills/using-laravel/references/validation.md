# Using Laravel Validation

This is the current validation record for `using-laravel`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for Laravel backend behavior — routing and controllers, form requests and
authorization, Eloquent and migrations, queues, caching, outbound HTTP, events, mail, scheduling,
Laravel-specific testing, and deployment-sensitive framework behavior. It should not activate for
non-Laravel PHP, for visual-only work, or for database-engine tuning that never touches Laravel
code.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| LAR-T01 | Add a controller action with a form request and policy check | Load |
| LAR-T02 | "The orders page runs hundreds of queries" in a Laravel repository | Load |
| LAR-T03 | Tune a MySQL index with no application change | Do not load; `using-mysql` owns it |
| LAR-T04 | Restyle a Blade template with no behavior change | Do not load |
| LAR-T05 | Write a plain PHP script with no framework | Do not load |
| LAR-T06 | Add a queued job with retry and uniqueness rules | Load |
| LAR-T07 | Laravel controller returning an Inertia page | Compose with `using-inertia`; the protocol seam is not owned here |
| LAR-T08 | Laravel application whose slow query needs both an Eloquent fix and an engine index | Compose with `using-mysql` or `using-postgres`; each keeps its own proof |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| LAR-W01 | The repository already uses a pattern this package would not choose | Follow the established pattern; deviate only for a correctness or security defect and say so explicitly |
| LAR-W02 | Validation rules written inline in a fat controller | Move to a form request, with the condition under which inline validation may remain |
| LAR-W03 | A view triggers queries during render | Eager-load at the source and keep database access out of the view; prove by query count |
| LAR-W04 | Guidance depends on a Laravel 11+ streamlined-configuration API | Check the installed version first; distinguish a new application from an upgraded one that retains older boundaries |
| LAR-W05 | A policy is enforced only by hiding a UI control | Require server-side authorization on the endpoint |
| LAR-W06 | "I refactored the job and it should work now" | Reject fluent assurance; require the narrowest relevant test run and observed results |
| LAR-W07 | Installed Laravel version cannot be determined | Inspect the framework or stop and name the unknown; do not apply version-gated guidance on assumption |
| LAR-W08 | A bug fix arrives with an unrequested architectural refactor | Keep the smallest coherent change and leave the architecture alone |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. Cases marked for the sampled run are LAR-T01, LAR-T03, LAR-W06, LAR-T07,
  and LAR-T08, plus a baseline comparison without the package on LAR-W03.
- Codex: not run.

No case below is marked passed. Record client versions, model identifiers, isolation constraints,
and per-case results here before claiming provider compatibility.

## Limits

LAR-T07 and LAR-T08 require the composing packages in the same workspace and test only that
ownership stays separate. No case runs a live Laravel application; workflow cases are graded on the
decision and the proof demanded.
