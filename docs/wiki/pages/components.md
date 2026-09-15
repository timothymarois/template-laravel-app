+++
title = "Component kit"
subtitle = "the shared interface parts, the barrel they are imported from, and the live showcase"
status = "approved"
intent = """
The component kit exists so that a screen is assembled from parts that already handle their own
states, found in a live showcase, rather than rebuilt for each page. Whether a part exists, and what
it can do, should be answerable from the showcase alone.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Showcase", value = "/admin/components", cite = "showcase" },
  { label = "Groups", value = "49", cite = "groups" },
  { label = "Import", value = "@/components/ui", cite = "barrel" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Primitive", value = "the Base-suffixed part", cite = "base" },
  { label = "Imported by path only", value = "carousel, chart, resizable, sidebar, editor", cite = "barrel" },
  { label = "Showcase access", value = "operators only", cite = "showcase" },
]
+++

The component kit is 49 groups of interface parts, each in its own folder, imported from one place and
demonstrated on a showcase that renders every variant and state.[^groups][^barrel][^showcase] Three of
its behaviours have pages of their own: [Confirmation dialogs](components/dialogs.md),
[Data tables](components/data-tables.md) and [Dates](components/dates.md).

## Parts

Each group holds a primitive whose name ends in `Base`, such as `ButtonBase`, and the part built on it,
such as `Button`, which is what a screen uses.[^base] Most groups are imported from the barrel
`@/components/ui`; `carousel`, `chart`, `resizable` and `editor` are left out of it because each pulls
an optional dependency into every page, and `sidebar` because its name clashes with the application's
own, so those five are imported by their folder path.[^barrel]

## Showcase

An operator opens the showcase at `/admin/components`, and a visitor without the operator role is
refused with `403`.[^showcase] It is arranged in five groups, each with an `Overview` page and one page
per part:[^nav]

| Group | Pages |
|---|---|
| `Actions` | `Button`, `Command`, `Dialog`, `Menu`, `Sheet`[^nav] |
| `Charts` | `Area`, `Bar`, `Line`, `Pie & Donut`[^nav] |
| `Data` | `Actions`, `Pagination`, `Table`[^nav] |
| `Display` | `Accordion`, `Alert`, `Avatar`, `Badge`, `Card`, `Carousel`, `Code Block`, `Loading`, `Popover`, `Resizable`, `Tabs`, `Toast`, `Tooltip`, `View Toggle`[^nav] |
| `Forms` | `Calendar`, `Checkbox`, `Combobox`, `Editor`, `Fields`, `Input`, `Pin Input`, `Select`, `Slider`, `Switch`, `Textarea`, `Upload`[^nav] |

The `Input` page has a second page for masked input, and `Calendar` one each for a date and a date
range.[^showcase]

[^groups]: `resources/js/components/ui/` — 49 folders, one group each, beside `index.ts`.
[^barrel]: `resources/js/components/ui/index.ts` — exports every group but `carousel`, `chart`,
    `resizable`, `sidebar` and `editor`, with the comment giving each reason.
[^base]: `resources/js/components/ui/button/` — `ButtonBase.vue` beside `Button.vue`, the pattern
    every group follows.
[^showcase]: `routes/web.php` — the `components` prefix sits inside the `admin` group, whose
    `admin` middleware refuses a user outside the operator role with `403`;
    `routes/components.php` — 47 `GET` routes, including `forms.input.masks`,
    `forms.calendar.date-input` and `forms.calendar.date-range-input`, each rendered by
    `app/Http/Controllers/Admin/ComponentController.php`.
[^nav]: `resources/js/pages/admin/components/_composables/useShowcaseNav.ts` — `useShowcaseNav()`
    lists the five groups and their pages, each opening with `Overview`.
