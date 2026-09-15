+++
title = "Dates"
subtitle = "calendar dates, instants, the viewer's time zone and the fallback for a bad zone"
status = "approved"
goals = false
intent = """
Dates exist so that a calendar date such as a birthday reads the same in every time zone, an instant is
shown in the viewer's own zone, and a stale or empty zone on an account never blanks or crashes a
page. A date the server sends is never read as anything but UTC.
"""

[[infobox]]
group = "Values"
rows = [
  { label = "Default zone", value = "UTC", cite = "zone" },
  { label = "Date format", value = "7/22/2024", note = "en-US", cite = "format" },
  { label = "Date and time format", value = "7/22/2025, 4:13 PM", cite = "format" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Calendar date", value = "shown as stored, in every zone", cite = "dateonly" },
  { label = "Instant", value = "converted to the viewer's zone", cite = "instant" },
  { label = "Unknown zone", value = "UTC", cite = "zone" },
  { label = "Unreadable value", value = "shown as nothing", cite = "invalid" },
]
+++

A value the server sends is shown either as a calendar date, which never moves, or as an instant,
which is converted into the viewer's zone.[^dateonly][^instant] An account stores a zone or none, and the
formatting is one part of the [Component kit](../components.md).[^account]

## Calendar dates

A value written as a date alone, such as `2026-01-01`, is a day on a wall calendar, not a moment, so it
is shown as `1/1/2026` in New York, Tokyo and Sydney alike.[^dateonly] Read as midnight and converted
westward it would become the day before, which a birthday or a due date must never do.[^dateonly]

## Instants

A value carrying a time is a moment, and is shown in the viewer's zone: `2026-01-01T23:30:00Z` is
`1/1/2026` in New York and `1/2/2026` in Tokyo.[^instant] A time with no zone stated is read as UTC,
which is how the server stores it; a time that states its zone or offset is read as written.[^parse] A
date is written as `7/22/2024`, and a date with time as `7/22/2025, 4:13 PM`; another locale changes
the order, so `en-GB` writes `22/07/2024`.[^format]

## Zones

With no zone given, or a zone the browser does not recognise, a value is shown in UTC rather than the
page failing, because the zone comes from stored account data and can be stale.[^zone] A value that
cannot be read as a date is shown as nothing.[^invalid] Every example above is a case the test suite
runs.[^tests]

[^dateonly]: `resources/js/utils/format/timezone.ts` — `isDateOnly()` matches `YYYY-MM-DD`, with the
    comment on why such a value is rendered in UTC; `resources/js/utils/format/formatDate.ts` and
    `formatDatetime.ts` — both format a date-only value in UTC whatever zone is given.
[^instant]: `resources/js/utils/format/formatDate.ts` — `formatDate()` formats any other value in the
    zone `resolveTimeZone()` returns; `resources/js/tests/utils/format/formatDate.test.ts` — the
    case that shifts `2026-01-01T23:30:00Z` to `1/2/2026` in `Asia/Tokyo`.
[^parse]: `resources/js/utils/format/parseUtcDate.ts` — `parseUtcDate()` appends `Z` to a string that
    states no zone and leaves one ending in `Z` or an offset such as `+00:00` as it is.
[^format]: `resources/js/utils/format/formatDate.ts` — `toLocaleDateString()` with the locale,
    `en-US` when unset; `formatDatetime.ts` — `toLocaleString()` with numeric date parts, hour and
    minute, twelve-hour clock; the samples are from the functions' own doc comments and the
    `formatDate.test.ts` cases for `en-US` and `en-GB`.
[^zone]: `resources/js/utils/format/timezone.ts` — `resolveTimeZone()` returns `UTC` for an empty
    value and for a name `Intl.DateTimeFormat` rejects, with the comment on stored account data;
    `formatDate.ts` and `formatDatetime.ts` — the zone parameter defaults to `UTC`.
[^invalid]: `resources/js/utils/format/parseUtcDate.ts` — `parseUtcDate()` returns null for a value
    that does not parse; `formatDate.ts` and `formatDatetime.ts` — return an empty string for it.
[^account]: `database/migrations/0001_01_01_000000_create_users_table.php` — the `users` table has a
    nullable `timezone` column; `app/Models/User.php` — `timezone` is fillable.
[^tests]: `resources/js/tests/utils/format/formatDate.test.ts` and `timezone.test.ts` — 45 cases,
    all passing on a run of `pnpm exec vitest run` against those two files.
