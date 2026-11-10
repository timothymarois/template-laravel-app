# template-laravel-app v6.1.4 — catalog skills leave git

**Every fork acts.** The catalog skills in `.agents/skills` stop being committed. Rundesk adds the
ones a project needs from the `rundesk-ai/skills` package, rewrites them when the package releases,
and lists them in `.git/info/exclude`, so a fork no longer carries copies that fall behind. Nothing in
the application changes.

## Prerequisites

```sh
jq -r .version template-manifest.json     # 6.1.3
```

The project is in Rundesk, and its catalog skills are added there (`skills:<name>`).

## Part A — Untrack the catalog copies (every fork)

In Rundesk, set the project's skills to be ignored by git, or remove `skills_git: track` from
`desk.yml`. Then untrack every folder Rundesk wrote, which carries a `.rundesk` file:

```sh
for d in .agents/skills/*/; do
  [ -f "$d.rundesk" ] && git rm -r --cached "${d%/}"
done
```

Keep committed what the project owns: `writing-wiki-pages`, which `wiki sync` writes, and any skill
of the fork's own. A catalog skill copy without a `.rundesk` file is an old copy: delete it and add the
skill in Rundesk instead.

Replace the `.gitignore` comment above `/.claude/settings.local.json` with the template's.

## Verify

```sh
git ls-files .agents/skills | cut -d/ -f3 | sort -u                      # only the fork's own skills
git check-ignore -q .agents/skills/testing-code/SKILL.md && echo ignored # a Rundesk copy is ignored
./scripts/dev-wiki.sh check                                              # ends "0 problems"
```
