# TabPanels

Wrapper for multiple tab panels.

```ts
import { TabPanels } from '@atlas/ui';
```

## Usage

```vue
<Tabs value="0">
  <TabPanels>
    <TabPanel value="0">Content 1</TabPanel>
    <TabPanel value="1">Content 2</TabPanel>
  </TabPanels>
</Tabs>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `dt` | `DesignToken` | — | Design token for scoped CSS variables. |
| `pt` | `TabPanelsPassThroughOptions` | — | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | — | Configuration for pass-through options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Tab panels to display. |

### Events

None.

Refer to the [PrimeVue Tabs API](https://primevue.org/tabs/#api).
