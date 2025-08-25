# Vue Composables

This documentation covers the usage of three Vue composables: `useDataTableOptions`, `useModal`, and `useScroll`.

## Table of Contents

- [useDataTableOptions](#usedatatableoptions) - Datatable state management with Inertia
- [useModal](#usemodal) - Modal management system
- [useScroll](#usescroll) - Scroll utilities and detection

## useDataTableOptions

The `useDataTableOptions` composable keeps datatable query options in sync with the backend via Inertia. It accepts a route configuration and initial options—typically the `options` array returned by the [Atlas Laravel Inertia DataTable Options trait](https://github.com/tmarois/atlas-laravel/blob/master/docs/inertia-data-table-options.md)—and returns reactive refs for those options along with helpers to fetch new data.

### Basic Usage

```vue
<script setup>
import { useDataTableOptions } from '@atlas/ui';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const table = useDataTableOptions('users.index', props.options);
</script>
```

### API Reference

- `search`, `perPage`, `sortField`, `sortOrder`, `filters`, `viewFields` – reactive query option refs.
- `selectAll`, `selected` – selection helpers for rows.
- `fetchData()` – trigger an Inertia visit with the current options.
- `resetSelection()` – clear the current selection.

## useModal

The `useModal` composable provides a flexible modal management system for Vue applications.

### Basic Usage

```typescript
import { useModal } from '@atlas/ui';

const modal = useModal();

// Open a modal
modal.open('confirmDialog', { message: 'Are you sure?' });

// Close a modal
modal.close('confirmDialog');

// Check if modal is open
const isActive = modal.activeState('confirmDialog');

// Get modal data
const modalData = modal.data('confirmDialog');
```

### API Reference

#### Methods

- `open(name: string, data?: unknown)`: Opens a modal with the specified name and optional data
- `close(name: string)`: Closes a specific modal
- `closeAll()`: Closes all open modals
- `activeState(name: string)`: Returns a reactive ref for the modal's open state
- `data(name: string)`: Returns a computed ref of the modal's data
- `isOpen`: Computed property that returns true if any modal is open
- `onOpen(name: string, callback: (data: unknown) => void)`: Register a callback when a modal opens
- `onClose(name: string, callback: (data: unknown) => void)`: Register a callback when a modal closes

### Examples

#### Using with v-model

```js
<template>
    <Modal v-model="isActive">
        <!-- Modal content -->
    </Modal>
</template>

<script setup>
import { useModal } from '@atlas/ui';

const modal = useModal();
const isActive = modal.activeState('confirmDialog');
</script>
```

#### Using with Callbacks

```js
<script setup>
import { useModal } from '@atlas/ui';

const modal = useModal();

// Register open callback
modal.onOpen('userForm', (data) => {
    console.log('User form opened with data:', data);
});

// Register close callback
modal.onClose('userForm', (data) => {
    console.log('User form closed with data:', data);
});
</script>
```

## useScroll

The `useScroll` composable provides utilities for scroll management and detection.

### Basic Usage

```js
<script setup>
import { useScroll } from '@atlas/ui';
import { ref } from 'vue';

const elementRef = ref(null);
const scroll = useScroll('myScrollContainer');

// Bind scroll handler to element
const scrollHandler = scroll.bindScrollHandler(elementRef);

// Add scroll listener
onMounted(() => {
    scrollHandler.add();
});

// Remove scroll listener
onUnmounted(() => {
    scrollHandler.remove();
});
</script>
```

### API Reference

#### Methods

- `isTop`: Computed ref indicating if the scroll position is at the top
- `setTop(value: boolean)`: Manually set the top state
- `bindScrollHandler(elementRef: Ref<HTMLElement | null>)`: Binds scroll events to an element
- `lockScroll()`: Prevents scrolling on the document
- `unlockScroll()`: Restores scrolling on the document

### Examples

#### Scroll Detection

```js
<template>
    <div ref="containerRef" class="scroll-container">
        <div v-if="!isAtTop" class="scroll-indicator">
            Scrolled down
        </div>
        <!-- Content -->
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useScroll } from '@atlas/ui';

const containerRef = ref(null);
const scroll = useScroll('container');
const isAtTop = scroll.isTop;

const scrollHandler = scroll.bindScrollHandler(containerRef);

onMounted(() => {
    scrollHandler.add();
});

onUnmounted(() => {
    scrollHandler.remove();
});
</script>
```

#### Modal with Scroll Lock

```js
<script setup>
import { useModal, useScroll } from '@atlas/ui';

const modal = useModal();
const scroll = useScroll('modal');

// Lock scroll when modal opens
modal.onOpen('fullscreenModal', () => {
    scroll.lockScroll();
});

// Unlock scroll when modal closes
modal.onClose('fullscreenModal', () => {
    scroll.unlockScroll();
});
</script>
```

This documentation provides examples and usage patterns for both composables. For more specific use cases or advanced configurations, please refer to the source code or raise an issue in the repository.
