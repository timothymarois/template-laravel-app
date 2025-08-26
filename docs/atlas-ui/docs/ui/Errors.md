# Errors

Displays a collapsible list of validation or request errors.

```ts
import { Errors } from '@atlas/ui';
```

## Usage

```vue
<Errors :errors="formErrors" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `errors` | `Record<string, any> \| any[]` | `{}` | Record or array of error messages. |
| `failed` | `boolean` | `false` | Show a default error message when true and no errors are provided. |
| `title` | `string` | `''` | Optional heading text. |
| `expandDefault` | `boolean` | `true` | Expand errors by default. |
| `pt` | `ErrorsPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `default` – Custom template for each error. Slot props: `{ error }`.
- `defaultError` – Content shown when `failed` is true and no errors exist.

### Events
- None.

