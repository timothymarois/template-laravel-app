# Tab

Individual tab header within a tab set.

```ts
import { Tab } from '@atlas/ui';
```

## Usage

```vue
<Tabs value="0">
  <TabList>
    <Tab value="0">One</Tab>
  </TabList>
</Tabs>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `string \| number` | — | Unique identifier of the tab. |
| `disabled` | `boolean` | `false` | Disables user interaction. |
| `as` | `string \| Component` | `button` | Tag name of the root element. |
| `asChild` | `boolean` | `false` | Use the child element as the root. |
| `dt` | `DesignToken` | — | Design token for scoped CSS variables. |
| `pt` | `TabPassThroughOptions` | — | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | — | Configuration for pass-through options. |

### Slots

| Name | Description |
| ---- | ----------- |
| `default` | Content of the tab header. |

### Events

None.

Refer to the [PrimeVue Tabs API](https://primevue.org/tabs/#api).
