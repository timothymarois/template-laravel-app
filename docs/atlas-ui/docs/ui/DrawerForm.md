# DrawerForm

Form container presented inside a sliding drawer.

```ts
import { DrawerForm } from '@atlas/ui';
```

## Usage

```vue
<DrawerForm v-model="open" title="Title" @submit="save">
  <!-- form fields -->
</DrawerForm>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `boolean` | `false` | Controls visibility. |
| `title` | `string` | `''` | Drawer title text. |
| `loading` | `boolean` | `false` | Shows loading state on save. |
| `width` | `string` | `undefined` | Optional width of the drawer. |
| `errors` | `Record<string, any>` | `undefined` | Form error object. |
| `tabs` | `Tab[]` | `[]` | Array of tab definitions `{ title, disabled?, lockTooltipText? }`. |
| `modelActiveTab` | `number` | `0` | Index of the active tab. |

### Slots
- `header` – Custom header content.
- `default` – Form content or first tab panel.
- `tab-{index}` – Content for the tab at the specified index.
- `footer` – Custom footer content.
- `message` – Message above footer actions.
- `actions` – Replace default footer actions.

### Events
- `update:modelValue` – Emitted when visibility changes.
- `submit` – Emitted when the save action is triggered.
- `update:modelActiveTab` – Emitted when active tab changes.

Refer to the [PrimeVue Drawer API](https://primevue.org/drawer/#api).
