# Alert

Displays a contextual inline message with optional icon and actions.

```ts
import { Alert } from '@atlas/ui';
```

## Usage

```vue
<Alert>Default message</Alert>
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `warning` | `boolean` | `false` | Use warning styling. |
| `hideIcon` | `boolean` | `false` | Hide the default icon. |
| `pt` | `AlertPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `default` – Content of the alert.
- `icon` – Custom icon.
- `actions` – Action elements such as buttons.

### Events
- None.

