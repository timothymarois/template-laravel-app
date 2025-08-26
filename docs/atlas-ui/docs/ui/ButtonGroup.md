# ButtonGroup

Group multiple buttons together.

```ts
import { ButtonGroup, Button } from '@atlas/ui';
```

## Usage

```vue
<ButtonGroup>
  <Button label="Save" />
  <Button label="Cancel" />
</ButtonGroup>
```

## API

### Props
| Prop | Type | Description |
| ---- | ---- | ----------- |
| `dt` | `DesignToken` | Design token for scoped CSS variables. |
| `pt` | `ButtonGroupPassThroughOptions` | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | Configuration for pass-through. |
| `unstyled` | `boolean` | Removes component styles. |

### Slots
- `default` – Buttons to display in the group.

### Events
- None.

Refer to the [PrimeVue ButtonGroup API](https://primevue.org/buttongroup/#api) for full details.
