# DialogConfirmation

Preconfigured dialog for confirming destructive actions.

```ts
import { DialogConfirmation } from '@atlas/ui';
```

## Usage

```vue
<DialogConfirmation v-model="open" title="Delete item" @confirm="remove" />
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `boolean` | `false` | Controls visibility. |
| `title` | `string` | `'Delete Item'` | Header text. |
| `icon` | `string` | `'pi pi-trash'` | Icon class for the dialog. |
| `message` | `string` | `'Are you sure you want to delete this item?'` | Confirmation message. |
| `loading` | `boolean` | `false` | Disables buttons and shows loading state. |
| `pt` | `DialogConfirmationPassThroughOptions` | `undefined` | Passthrough options for internal elements. |

Also supports all PrimeVue Dialog props except `visible`, `header`, and `pt`.

### Slots
- `message` – Custom confirmation message and icon.
- `actions` – Replace the default action buttons.

### Events
- `update:modelValue` – Emitted when visibility changes.
- `confirm` – Emitted when the confirm action is triggered.

Refer to the [PrimeVue Dialog API](https://primevue.org/dialog/#api).
