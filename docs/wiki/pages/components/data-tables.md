+++
title = "Data tables"
subtitle = "search, sorting, page size, filters, selection and what a table remembers"
status = "approved"
goals = false
intent = """
Data tables exist so that a person searching, sorting and paging a list keeps their view between visits
without the table asking again, and never acts in bulk on rows they can no longer see. Typing narrows
the list without a button.
"""

[[infobox]]
group = "Defaults"
rows = [
  { label = "Rows a page", value = "15", note = "50 on the users list", cite = "defaults" },
  { label = "Order", value = "by id, ascending", note = "by name on the users list", cite = "defaults" },
  { label = "Search delay", value = "a quarter of a second", cite = "search" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Memory", value = "per list, for the session", cite = "session" },
  { label = "Selection", value = "cleared when the rows change", cite = "selection" },
]
+++

A data table is searched, sorted, paged and filtered from the browser, and the server remembers each
list's view for the rest of the session.[^defaults][^session] It is one part of the
[Component kit](../components.md); the single list shipped is [Users](../admin/users.md).

## Searching and sorting

Typing in the search box refreshes the list a quarter of a second after the last keystroke, so a word
typed at speed sends one request.[^search] Changing the page size, the sort column or its direction
refreshes at once, and so does changing a filter or the chosen columns.[^refresh] A list starts at 15
rows a page, ordered by id ascending; the users list starts at 50 rows ordered by name and offers 15,
25, 50 or 100.[^defaults]

## Memory

The server stores a list's search, filters, columns, page size and sort under that list's name in the
session, so returning to it in the same session restores the view.[^session] A value sent with the
request wins over the stored one, and the stored one over the default.[^session]

## Selection

Ticking rows, or `select all`, applies to the rows on screen.[^selection] The selection is cleared when
the search or a filter changes, because the rows on screen are no longer the rows that were ticked,
and when the person leaves the page.[^selection]

[^defaults]: `resources/js/composables/inertia/useDataTableOptions.js` — `useDataTableOptions()` starts
    the form with `perPage: 15`, `sortField: 'id'`, `sortOrder: 1`, an empty search and no filters;
    `app/Http/Controllers/Admin/UserController.php` — `$indexDefaults` sets `perPage` 50 and
    `sortField` `name`; `resources/js/pages/admin/users/Index.vue` — `perPageOptions` lists 15, 25, 50
    and 100.
[^search]: `resources/js/composables/inertia/useDataTableOptions.js` — the watcher on `search` calls
    a version of `fetchData` debounced by `debounceMs`, 250 milliseconds when unset.
[^refresh]: `resources/js/composables/inertia/useDataTableOptions.js` — the watchers on `perPage`,
    `sortField`, `sortOrder`, `viewFields` and `filters` call `fetchData` directly.
[^session]: `app/Http/Concerns/InertiaDataTableOptions.php` — `resolveIndexOptions()` merges the
    defaults, then the session's `options.NAME` entry, then the request, and `storeSessionData()`
    writes `search`, `filters`, `viewFields`, `perPage`, `sortField` and `sortOrder` under that key.
[^selection]: `resources/js/composables/inertia/useDataTableOptions.js` — `resetSelection()` clears
    `selectAll` and `selected`; the watchers on `search` and `filters` call it, and
    `resetSelectionOnPathChange()` clears both when the page's path changes.
