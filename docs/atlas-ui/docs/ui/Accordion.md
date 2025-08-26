# Accordion

Collapsible panels for organizing content.

```ts
import { Accordion, AccordionPanel, AccordionHeader, AccordionContent } from '@atlas/ui';
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
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `string \| string[] \| number \| number[] \| null` | `null` | Value of the active panel(s). |
| `multiple` | `boolean` | `false` | Allow multiple panels to be active. |
| `activeIndex` | `number \| number[] \| null` | `null` | **Deprecated.** Previous index of active panel. |
| `lazy` | `boolean` | `false` | Whether hidden tabs are not rendered. |
| `expandIcon` | `string` | `undefined` | Icon of a collapsed tab. |
| `collapseIcon` | `string` | `undefined` | Icon of an expanded tab. |
| `tabindex` | `number` | `0` | Tab order of the element. |
| `selectOnFocus` | `boolean` | `false` | Activate tab on focus. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `AccordionPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Accordion panels.

### Events
None.

Refer to the [PrimeVue Accordion API](https://primevue.org/accordion/#api).
