+++
title = "Goals"
subtitle = "every goal page's intent, in sidebar order"
status = "approved"
goals = false
intent = """
The goals page collects what each part of the template is for, so the whole purpose can be read in one
sitting and a change to any of it is visible in one place.
"""
+++

The goals page collects the intent of every approved page that has not said `goals = false`, when the
wiki is built, in the order the sidebar lists the pages.[^collect] Changing an intent changes what that
part of the template is trying to be, and that is the owner's decision.[^owner]

[^collect]: wiki-builder — `src/builder/build.py` — `goals_order()` walks the sections in order and keeps
    each approved page whose `goals` is not false, and `goals_page()` collects their intents.
[^owner]: `.claude/skills/writing-wiki-pages/SKILL.md` — the Owner approval section: changing an intent
    needs the owner's approval.
