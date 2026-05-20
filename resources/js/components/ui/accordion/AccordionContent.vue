<script setup lang="ts">
import { inject, computed, ref, watch, onMounted } from 'vue';
import { cn } from '@/utils';

interface AccordionContext {
    toggle: (value: string) => void;
    isOpen: (value: string) => boolean;
}

interface AccordionItemContext {
    value: string;
    disabled?: boolean;
}

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const accordionContext = inject<AccordionContext>('accordionContext')!;
const itemContext = inject<AccordionItemContext>('accordionItemContext')!;

const isOpen = computed(() => accordionContext.isOpen(itemContext.value));

const contentRef = ref<HTMLElement | null>(null);
const height = ref<string>('0px');
const hasMounted = ref(false);

onMounted(() => {
    // If this item opens on first render, skip the 0 → N animation to avoid
    // a flash of the content expanding on page load.
    if (isOpen.value) {
        height.value = 'auto';
    }
    hasMounted.value = true;
});

function onTransitionEnd(event: TransitionEvent) {
    if (event.propertyName !== 'height') return;
    if (isOpen.value) {
        // Drop to `auto` once expanded so dynamic content (images, nested
        // components) can grow without re-measurement.
        height.value = 'auto';
    }
}

watch(isOpen, (open) => {
    if (!hasMounted.value) return;

    const el = contentRef.value;
    if (!el) return;

    if (open) {
        // Opening: go from 0 → measured height. `onTransitionEnd` will flip
        // to `auto` after the transition completes.
        height.value = `${el.scrollHeight}px`;
    } else {
        // Closing: CSS can't animate from `auto`. Pin the current height in
        // px in one frame, let the browser commit it, then drop to 0 in the
        // next frame so the height property actually transitions. Vue's
        // `nextTick` flushes the vdom queue but isn't a guarantee the browser
        // painted the style — the double-rAF pattern is.
        height.value = `${el.scrollHeight}px`;
        requestAnimationFrame(() => {
            void el.offsetHeight;
            requestAnimationFrame(() => {
                height.value = '0px';
            });
        });
    }
});
</script>

<template>
    <div
        ref="contentRef"
        :data-state="isOpen ? 'open' : 'closed'"
        class="overflow-hidden transition-[height] duration-300 ease-out motion-reduce:transition-none"
        :style="{ height }"
        @transitionend="onTransitionEnd"
    >
        <div
            :class="
                cn(
                    'px-4 pb-4 pt-0 transition-opacity duration-200 ease-out motion-reduce:transition-none',
                    isOpen ? 'opacity-100' : 'opacity-0',
                    props.class,
                )
            "
        >
            <slot />
        </div>
    </div>
</template>
