# TabList

Container for tab headers.

```ts
import { TabList } from '@atlas/ui';
```

## Usage

```vue
<Tabs value="0">
  <TabList>
    <Tab value="0">One</Tab>
    <Tab value="1">Two</Tab>
  </TabList>
</Tabs>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `dt` | `DesignToken` | — | Design token for scoped CSS variables. |
| `pt` | `TabListPassThroughOptions` | — | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | — | Configuration for pass-through options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Tab headers to display. |

### Events

None.

Refer to the [PrimeVue Tabs API](https://primevue.org/tabs/#api).
