# Enum Exporter

Sync Laravel PHP enums with Vue by generating matching TypeScript or JavaScript enums.

## Configuration (optional)

Publish the config to change the output directory or file format:

```bash
php artisan vendor:publish --tag=atlas-config
```

## Export enums

```bash
php artisan atlas:export-enums
```

The command scans your configured enum paths and writes files to `resources/js/enums` by default. An `index.ts` file re-exports
each enum for easy imports. When multiple enums share the same class name, the index aliases duplicates using namespace segments:

```ts
// resources/js/enums/index.ts
export { ActionStatus } from './Action/ActionStatus';
export { ActionStatus as ActionWorkerStatus } from './Action/Worker/ActionStatus';
```

## Example

PHP enum:

```php
namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
```

Generated TypeScript:

```ts
// resources/js/enums/UserStatus.ts
export enum UserStatus {
    ACTIVE = 'active',
    INACTIVE = 'inactive',
}
```

Use in Vue:

```vue
<script setup lang="ts">
import { UserStatus } from '@/enums';

const user = ref({ status: 'active' });
const isActive = computed(() => user.value.status === UserStatus.ACTIVE);
</script>
```

This keeps enum definitions synchronized across Laravel and Vue with full IDE support.

