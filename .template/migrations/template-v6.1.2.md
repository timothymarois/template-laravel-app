# template-laravel-app v6.1.2 — skills in .agents/skills

**Every fork acts.** The vendored agent skills move from `.claude/skills` to `.agents/skills`, and
every agent CLI reads them through a link. The skills themselves are refreshed. Nothing in the
application changes.

## Prerequisites

```sh
jq -r .version template-manifest.json     # 6.1.1
pnpm check                                # green before you start
```

## Part A — Move the folder and link it (every fork)

```sh
rm .agents/skills                         # the old link to .claude/skills
git mv .claude/skills .agents/skills
ln -s ../.agents/skills .claude/skills
ln -s ../.agents/skills .codex/skills     # mkdir -p .codex .grok first if they do not exist
ln -s ../.agents/skills .grok/skills
```

Then take the refreshed skills: copy each catalog skill's folder from the template's `.agents/skills`
over yours. **A skill copy is never edited in a fork.** If yours carries rules about your product, move
those rules into your own docs first — a guide under `docs/`, routed from the skill table in
`AGENTS.md` — and only then replace the copy. Leave `writing-wiki-pages` alone; `wiki sync` owns it.

Point every path that names `.claude/skills` at `.agents/skills`: `git grep -n "\.claude/skills"`
finds them. In a wiki page, correct both the sentence and its reference, then run
`./scripts/dev-wiki.sh build` so the page's date moves. If you run Prettier over markdown, add
`.agents/skills` to `.prettierignore` — a skill copy is never reformatted either.

## Verify

```sh
readlink .claude/skills .codex/skills .grok/skills    # ../.agents/skills, three times
test -d .agents/skills && ! test -L .agents/skills && echo ok
git grep -n "\.claude/skills" -- . ':!.agents'        # nothing
./scripts/dev-wiki.sh check                           # ends "0 problems"
pnpm check                                            # exit 0
```

## Finally

Bump the manifest:

```diff
-    "version": "6.1.1",
+    "version": "6.1.2",
```
