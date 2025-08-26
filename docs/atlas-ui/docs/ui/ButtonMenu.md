# ButtonMenu

Dropdown menu activated from a button.

```ts
import { ButtonMenu } from '@atlas/ui';
```

## Usage

```vue
<ButtonMenu :items="items" @action="onAction" />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `items` | `MenuItem[]` | — | Menu items with `label`, `icon`, `action`, `disabled`, optional `children`, and `tooltip`. |
| `icon` | `string` | `'pi pi-ellipsis-v'` | Icon class for the default trigger. |
| `ptData` | `Record<string, any>` | `{}` | Additional data forwarded with the `action` event. |
| `onHover` | `boolean` | `false` | Show menu on hover instead of click. |
| `pt` | `ButtonMenuPassThroughOptions` | — | Pass-through styling options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `trigger` | Custom trigger element. Slot props: `{ toggleMenu, triggerRef }`. |

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| `action` | `(action: any, ptData: Record<string, any>)` | Emitted when an item is selected. |

Refer to the [PrimeVue Menu API](https://primevue.org/menu/#api) for menu item options.
