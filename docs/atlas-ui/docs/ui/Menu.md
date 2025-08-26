# Menu

Displays a list of navigation items.

```ts
import { Menu } from '@atlas/ui';
```

## Usage

```vue
<Menu :model="items" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `model` | `MenuItem[]` | — | Array of menu item objects. |
| `popup` | `boolean` | `false` | Renders the menu as a popup overlay. |
| `appendTo` | `string \\| HTMLElement` | `'body'` | Element or selector to append the overlay. |
| `autoZIndex` | `boolean` | `true` | Whether to automatically manage layering. |
| `baseZIndex` | `number` | `0` | Base z-index for layering. |
| `pt` | `MenuPassThroughOptions` | — | Pass-through styling options. |

### Slots
| Name | Description |
| ---- | ----------- |
| `start` | Custom content before the menu. |
| `end` | Custom content after the menu. |
| `item` | Template for a menu item. |
| `itemicon` | Custom icon template for a menu item. |

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `show` | `void` | Emitted when the menu overlay is shown. |
| `hide` | `void` | Emitted when the menu overlay is hidden. |
| `focus` | `Event` | Emitted when the menu receives focus. |
| `blur` | `Event` | Emitted when the menu loses focus. |

Refer to the [PrimeVue Menu API](https://primevue.org/menu/#api).
