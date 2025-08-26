# TabPanel

Content container for a tab.

```ts
import { TabPanel } from '@atlas/ui';
```

## Usage

```vue
<Tabs value="0">
  <TabPanels>
    <TabPanel value="0">Content</TabPanel>
  </TabPanels>
</Tabs>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `string \| number` | — | Unique identifier of the panel. |
| `as` | `string \| Component` | `div` | Tag name of the root element. |
| `asChild` | `boolean` | `false` | Use the child element as the root. |
| `dt` | `DesignToken` | — | Design token for scoped CSS variables. |
| `pt` | `TabPanelPassThroughOptions` | — | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | — | Configuration for pass-through options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Content of the tab panel. |

### Events

None.

Refer to the [PrimeVue Tabs API](https://primevue.org/tabs/#api).
