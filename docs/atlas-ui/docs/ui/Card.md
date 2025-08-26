# Card

Container for grouping related content.

```ts
import { Card } from '@atlas/ui';
```

## Usage

```vue
<Card title="Title">Content</Card>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `noPadding` | `boolean` | `false` | Removes padding from the card's content section. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `CardPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Card content.
- `header` – Custom header section.
- `title` – Replaces the card title.
- `subtitle` – Adds a subtitle below the title.
- `footer` – Footer content.

### Events
None.

Refer to the [PrimeVue Card API](https://primevue.org/card/#api).
