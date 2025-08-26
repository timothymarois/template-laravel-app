# PageContent

Scrollable main content area.

```ts
import { PageContent } from '@atlas/ui';
```

## Usage

```vue
<PageContent :offset="64" :footerHeight="48">
  <p>Main content</p>
</PageContent>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `offset` | `number \| null` | `null` | Top offset for fixed headers. |
| `footerHeight` | `number` | `0` | Height of a sticky footer to offset. |
| `widthClass` | `string` | `'max-w-screen-2xl'` | Max width container class. |
| `containerClass` | `string` | `'mx-auto p-4'` | Additional container classes. |

### Slots
- `default` – page body.

### Events
- None.
