# PageContent

Scrollable main content area.

## Example
```ts
import { PageContent } from '@atlas/ui';
```

```vue
<PageContent :offset="64" :footerHeight="48">
  <p>Main content</p>
</PageContent>
```

## Props
- `offset: number | null` – top offset for fixed headers. Default `null`.
- `footerHeight: number` – height of a sticky footer to offset. Default `0`.
- `widthClass: string` – max width container class. Default `'max-w-screen-2xl'`.
- `containerClass: string` – additional container classes. Default `'mx-auto p-4'`.

## Slots
- `default` – page body.

## Events
- None
