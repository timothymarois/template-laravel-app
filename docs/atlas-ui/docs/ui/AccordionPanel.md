# AccordionPanel

Wrapper for a single accordion section.

```ts
import { AccordionPanel } from '@atlas/ui';
```

## Usage

```vue
<Accordion>
  <AccordionPanel value="0">
    <AccordionHeader>Header</AccordionHeader>
    <AccordionContent>Content</AccordionContent>
  </AccordionPanel>
</Accordion>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `string \| number` | — | Unique identifier for the panel. |
| `disabled` | `boolean` | `false` | Disables user interaction. |
| `as` | `string \| Component` | `div` | Tag name of the root element. |
| `asChild` | `boolean` | `false` | Use the child element as the root. |
| `dt` | `DesignToken` | — | Design token for scoped CSS variables. |
| `pt` | `AccordionPanelPassThroughOptions` | — | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | — | Configuration for pass-through options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Content of the accordion panel. |

### Events

None.

Refer to the [PrimeVue Accordion API](https://primevue.org/accordion/#api).
