# LabelField

LabelField pairs a label and optional tooltip with a form field and displays validation errors.

```ts
import { LabelField, InputText } from '@atlas/ui';
```

## Usage

```vue
<LabelField label="Name" name="name" required tooltip="Enter full name">
  <InputText v-model="name" />
</LabelField>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `label` | `string` | `''` | Text for the label. |
| `tooltip` | `string` | `''` | Help text shown in a tooltip. |
| `name` | `string` | `''` | Associates the label with an input. |
| `required` | `boolean` | `false` | Display an asterisk when true. |
| `error` | `string` | `''` | Validation message displayed below the field. |
| `pt` | `LabelFieldPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Input element to label. |

### Events

None.

No PrimeVue equivalent.

