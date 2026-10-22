# Sources

These sources support the general delivery safeguards synthesized in
`SKILL.md`. They do not prescribe this skill's names, numeric thresholds, role model, or one
universal development ceremony.

## Small, coherent scope

- Google's [Small CLs](https://google.github.io/eng-practices/review/developer/small-cls.html)
  recommends self-contained changes that address one issue and explains that smaller changes are
  easier to review, validate, and roll back. It also rejects splitting necessary tests away from the
  behavior they prove. This supports one-outcome increments and keeping required proof with a change.
- The [Principles behind the Agile Manifesto](https://agilemanifesto.org/principles) favors working
  software, frequent delivery, and simplicity measured by work not done. This skill applies that
  principle by requiring planning, delegation, and additional gates only when dependencies or risk
  justify them.

## Plans, acceptance, and changing evidence

- NASA's [SWE-013 Software Plans](https://swehb.nasa.gov/spaces/SWEHBVD/pages/102695397/SWE-013+-+Software+Plans)
  says software plans should identify work, commitments, and risks and remain complete, workable,
  consistent, and verifiable as conditions change. Its scope is aerospace assurance; this skill
  borrows plan completeness and revision when facts invalidate an approach, not NASA's assurance
  level.
- NASA's [SWE-034 Acceptance Criteria](https://swehb.nasa.gov/spaces/SWEHBVD/pages/102695413/SWE-034+-+Acceptance+Criteria)
  recommends documented, measurable criteria that give stakeholders a shared basis for readiness.
  This supports defining observable proof before implementation and distinguishing completion from
  a plausible report. Its scope is aerospace assurance, not a universal software process.

## Risk and independent evidence

- NIST [SP 800-218, Secure Software Development Framework 1.1](https://doi.org/10.6028/NIST.SP.800-218)
  organizes secure development around risk-based practices, review or analysis, and retained
  evidence. This skill generalizes that principle into named risk triggers; NIST does not prescribe
  the six-row taxonomy in `SKILL.md`.
- Anthropic's engineering report,
  [How we built our multi-agent research system](https://www.anthropic.com/engineering/multi-agent-research-system),
  reports that clear objectives, output formats, tool guidance, and task boundaries reduce duplicated
  work and gaps, while multi-agent coordination has substantial overhead and performs poorly on
  highly dependent work. It is a field report about one research system, not a controlled study of
  software teams. This skill uses it only to support bounded handoffs and limited parallelism.

## Shaping and slicing

- Ryan Singer's [Shape Up](https://basecamp.com/shapeup) (Basecamp, 2019). Chapter 3,
  [Set Boundaries](https://basecamp.com/shapeup/1.2-chapter-03), separates an appetite, which
  starts from what the work is worth and shapes the design to fit, from an estimate; narrows a
  request to the problem behind it; compares against the customer's baseline; and names
  "redesign" and "2.0" requests as grab-bags to split. Chapter 11,
  [Get One Piece Done](https://basecamp.com/shapeup/3.2-chapter-11), integrates one vertical slice
  early, warns that "lots of things are done but nothing is really done", and picks the first piece
  as core, small, and novel. Chapter 12, [Map the Scopes](https://basecamp.com/shapeup/3.3-chapter-12),
  defines scopes as integrated slices finished independently, rejects grab-bag names like
  "front-end", and marks nice-to-haves with `~`. Chapter 14,
  [Decide When to Stop](https://basecamp.com/shapeup/3.5-chapter-14), says scope grows as details
  emerge and supplies the questions used here to keep or cut an addition.
- Richard Lawrence and Peter Green's
  [Humanizing Work Guide to Splitting User Stories](https://www.humanizingwork.com/the-humanizing-work-guide-to-splitting-user-stories/)
  supplies the ordered patterns in `slicing.md` (workflow steps, operations, rule and data
  variations, data entry, major effort, simple/complex, defer performance, spike last), the
  meta-pattern of reducing variations through the core complexity, the two tests for choosing a
  split, the warning against splitting by architectural layer or one workflow step at a time, the
  rule to merge tasks posing as stories, and the caution that enumerating every story in a complex
  domain "only gives you the illusion of predictability".
- METR's [Measuring AI Ability to Complete Long Tasks](https://metr.org/blog/2025-03-19-measuring-ai-ability-to-complete-long-tasks/)
  (March 2025) found models succeed almost always on tasks that take people under four minutes and
  under 10% of the time on tasks over about four hours. The horizon keeps growing, so the skill
  states the direction — size slices for reliability — rather than a fixed duration.
- Anthropic's [Effective harnesses for long-running agents](https://www.anthropic.com/engineering/effective-harnesses-for-long-running-agents)
  (November 26, 2025) reports agents trying to one-shot a whole application, running out of context
  with half-built features, declaring the job done early, and marking features complete without
  end-to-end testing; working one feature at a time against a fixed feature list, verified as a user
  would, and ending each session in a clean state corrected them. This supports one slice at a time,
  a proof per slice, and resuming from the first unproven slice.

## Operational conclusions

- Engagement mode is selected from dependency, design, and proof boundaries rather than a mandatory
  lifecycle. The source set supports small coherent changes, measurable readiness, explicit risk,
  and bounded handoffs; it does not supply the mode names.
- The six risk triggers are a local operational taxonomy. Their responses synthesize NIST's
  risk-based evidence principle with common compatibility and recovery boundaries.
- GitHub delivery is a separate authority boundary because externally stored mutations have a
  different target, account, and verification contract from local development. This separation is
  a design conclusion, not a claim made by the sources above.
- Repository and material-risk boundaries are treated as verification boundaries, so dependent
  implementation phases remain separate even when they cannot run in parallel. The atomic-outcome
  exception preserves Google's guidance to keep behavior with the proof that establishes it; its
  wording and application are operational conclusions.
- Implementation and completed-change review require different role fit and evidence. The exact
  finished-change review brief is an operational conclusion derived from measurable acceptance, retained
  evidence, and the team's separation of implementation from independent judgment.
- The slice-size rule — one outcome, its own proof, one working session — and the doubling
  threshold for re-slicing are operational conclusions. The sources establish small integrated
  slices and falling agent reliability with task length; they do not supply these thresholds.

## The least-work ladder

- Dietrich Gebert's [Ponytail](https://github.com/DietrichGebert/ponytail) (MIT) is an agent prompt
  that takes, after reading the code a change touches, the first option that fully works: whether
  the code needs to exist, then the codebase, the standard library or platform, an installed
  dependency, one line, and only then the minimum new code; it never cuts trust-boundary validation,
  data-loss handling, security or accessibility, leaves one small test for non-trivial logic, marks
  deliberate shortcuts, and ends by naming what was skipped. Its benchmark — the same agent with and
  without the prompt, 39 tasks, five runs each — reports roughly half the code and a higher share of
  risky logic shipped with a test; it is the author's own benchmark. This skill adopts the method,
  adds compliance to the never-cut list, and applies it at scoping time as well as coding time.
- Martin Fowler's [Yagni](https://martinfowler.com/bliki/Yagni.html) explains why building a
  capability before it is needed costs build, delay, carry and repair. This supports dropping
  options and flexibility nobody asked for, the ladder's first rung.

## Working briefs

- Ken Schwaber and Jeff Sutherland's [Scrum Guide](https://scrumguides.org/scrum-guide.html) (2020)
  treats the Sprint Backlog as a current, actionable picture updated as work is learned, and
  requires work to meet its Definition of Done before it counts. Applied here, a brief records
  current state rather than history, stays subordinate to the ticket, and closes only on proof.
- NASA's [R053 Traceability Completion](https://swehb.nasa.gov/spaces/SITE/pages/215777594/R053+-+Traceability+Completion)
  links missing requirement-to-verification traces to scope gaps and late rework, supporting the
  brief's Done when and proof per slice.
