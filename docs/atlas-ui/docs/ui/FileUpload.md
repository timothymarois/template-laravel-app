# FileUpload

FileUpload is a styled file input for selecting single or multiple files.

```ts
import { FileUpload } from '@atlas/ui';
```

## Usage

```vue
<script setup lang="ts">
import { ref } from 'vue';

const file = ref<File | null>(null);
</script>

<template>
  <FileUpload v-model="file" clearable />
</template>
```

Use `v-model` to bind the selected file or files. When `multiple` is true, `modelValue` becomes an array and each file should be appended to `FormData` separately.

```vue
<script setup lang="ts">
import { ref } from 'vue';

const files = ref<File[]>([]);
</script>

<template>
  <FileUpload v-model="files" multiple clearable />
</template>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `File \| File[] \| null` | `null` | Selected file or files. |
| `multiple` | `boolean` | `false` | Allow selecting multiple files. |
| `clearable` | `boolean` | `false` | Show a clear button to reset the selection. |
| `chooseLabel` | `string` | `'Choose'` | Text for the choose button. |
| `size` | `'small' \| 'large'` | `undefined` | Match InputText heights (34 px or 42 px). |
| `invalid` | `boolean` | `false` | Show the error style with a red border. |

### Slots

None.

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `File \| File[] \| null` | Emitted with the selected file or files. |

No PrimeVue equivalent.

