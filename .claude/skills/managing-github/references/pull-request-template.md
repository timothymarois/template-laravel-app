# Fallback GitHub pull-request template

Use this only when the target repository has no applicable pull-request template. Replace every
comment and placeholder, preserve the core headings, and check only outcomes proven by the exact
head. Add a conditional section only when it materially helps review.

````md
## Problem

<!-- One or two sentences: what is wrong now, and what it costs. -->

## Solution

<!-- 3-6 bullets, one line each. One decision per bullet, non-obvious rationale only. -->

-

## Evidence

<!-- 2-4 items the diff cannot show: numbers, output, before/after. -->

-

## Validation

- [ ] `<exact command or manual check>` — `<observed result>`
- [ ] Required GitHub checks pass for the exact head commit.

<!-- Use one standalone `Closes #<number>.` line per completed issue. Use `Refs` for partial work. -->
````

Add **Scope and compatibility** when a public contract, migration, dependency, permission, or
preserved behavior changes. Add **Risks and safeguards** for a material security, privacy, data,
billing, destructive-operation, or deployment risk. Add **Manual user path** when a short
representative path would help a reviewer observe the result.
