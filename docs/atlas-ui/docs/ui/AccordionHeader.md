# AccordionHeader

Toggle control for an accordion panel.

```ts
import { AccordionHeader } from '@atlas/ui';
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

None.

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Label of the accordion section. |
| `toggleicon` | Custom toggle icon when expanding or collapsing. |

### Events

None.

Refer to the [PrimeVue Accordion API](https://primevue.org/accordion/#api).
