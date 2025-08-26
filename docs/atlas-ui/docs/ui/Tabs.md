# Tabs

Container for managing multiple tabbed panels.

```ts
import { Tabs, Tab, TabList, TabPanel, TabPanels } from '@atlas/ui';
```

## Usage

```vue
<Tabs value="0">
  <TabList>
    <Tab value="0">One</Tab>
    <Tab value="1">Two</Tab>
  </TabList>
  <TabPanels>
    <TabPanel value="0">Content 1</TabPanel>
    <TabPanel value="1">Content 2</TabPanel>
  </TabPanels>
</Tabs>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `string \| number` | `undefined` | Value of the active tab. |
| `lazy` | `boolean` | `false` | Whether hidden tabs are not rendered. |
| `scrollable` | `boolean` | `false` | Enable scrollable tab headers. |
| `showNavigators` | `boolean` | `true` | Show navigation buttons when scrollable. |
| `tabindex` | `number` | `0` | Tab order of the container. |
| `selectOnFocus` | `boolean` | `false` | Activate tab on focus. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `TabsPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Contains `TabList` and `TabPanels` components.

### Events
None.

Refer to the [PrimeVue Tabs API](https://primevue.org/tabs/#api).
