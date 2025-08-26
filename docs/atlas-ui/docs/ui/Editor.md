# Editor

Editor is a rich text editor built with Tiptap. See the [Editor guide](../editor.md) for dependencies and usage.

```ts
import { Editor } from '@atlas/ui';
```

## Usage

```vue
<Editor v-model="content" placeholder="Write something..." />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string \| null` | `undefined` | Bound editor content. |
| `placeholder` | `string` | `''` | Placeholder text. |
| `editorClass` | `string` | `'p-4 text-black text-sm dark:text-white'` | Additional classes for the editor area. |
| `autofocus` | `boolean` | `false` | Focus editor on mount. |
| `toolbar` | `boolean` | `true` | Show the formatting toolbar. |
| `toolbarOptions` | `string[]` | `['bold', 'italic', 'strike', 'bullet', 'ordered', 'link', 'clear']` | Array of toolbar buttons to display. |
| `textOnly` | `boolean` | `false` | Restrict to plain text input. |
| `toolbarClass` | `string` | `'border-b border-surface-300 dark:border-surface-700'` | Additional classes for the toolbar. |

### Slots

| Name | Description |
| ---- | ----------- |
| `toolbar` | Customize the toolbar content. |

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted with the current content. |

No PrimeVue equivalent.

