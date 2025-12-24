<script setup lang="ts">
import type { DialogContentEmits, DialogContentProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { ref, provide, computed, onMounted, onUnmounted } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { X } from "lucide-vue-next";
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    useForwardPropsEmits,
} from "reka-ui";
import { cn } from "@/utils";

interface Props extends DialogContentProps {
    class?: HTMLAttributes["class"];
    draggable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    draggable: false,
});
const emits = defineEmits<DialogContentEmits>();

const delegatedProps = reactiveOmit(props, "class", "draggable");
const forwarded = useForwardPropsEmits(delegatedProps, emits);

// Draggable functionality
const dialogEl = ref<HTMLElement | null>(null);
const offset = ref({ x: 0, y: 0 });
const isDragging = ref(false);
const startMouse = ref({ x: 0, y: 0 });
const startOffset = ref({ x: 0, y: 0 });

const onMouseDown = (e: MouseEvent) => {
    if (!props.draggable) return;

    const target = e.target as HTMLElement;
    const header = target.closest('[data-dialog-header]');
    if (!header) return;

    e.preventDefault();
    isDragging.value = true;
    startMouse.value = { x: e.clientX, y: e.clientY };
    startOffset.value = { ...offset.value };

    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
};

const onMouseMove = (e: MouseEvent) => {
    if (!isDragging.value) return;

    offset.value = {
        x: startOffset.value.x + (e.clientX - startMouse.value.x),
        y: startOffset.value.y + (e.clientY - startMouse.value.y),
    };
};

const onMouseUp = () => {
    isDragging.value = false;
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', onMouseUp);
};

const dialogStyle = computed(() => {
    if (!props.draggable) return {};

    return {
        left: `calc(50% + ${offset.value.x}px)`,
        top: `calc(50% + ${offset.value.y}px)`,
    };
});

// Reset position when dialog opens
onMounted(() => {
    offset.value = { x: 0, y: 0 };
});

// Clean up event listeners if unmounted mid-drag
onUnmounted(() => {
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', onMouseUp);
});

// Provide draggable state for DialogHeader
provide('dialogDraggable', computed(() => props.draggable));
</script>

<template>
    <DialogPortal>
        <DialogOverlay
            v-if="!props.draggable"
            class="dialog-overlay fixed inset-0 z-50 bg-black/50"
        />
        <DialogContent
            ref="dialogEl"
            v-bind="forwarded"
            :aria-describedby="undefined"
            :trap-focus="!props.draggable"
            :class="
                cn(
                    'dialog-content fixed z-50 grid w-full max-w-lg -translate-x-1/2 -translate-y-1/2 gap-4 border bg-background p-6 shadow-lg sm:rounded-lg',
                    !props.draggable && 'left-1/2 top-1/2',
                    props.draggable && 'shadow-2xl',
                    props.class,
                )"
            :style="dialogStyle"
            @mousedown="onMouseDown"
            @pointer-down-outside="(e) => { if (props.draggable) e.preventDefault(); }"
            @interact-outside="(e) => { if (props.draggable) e.preventDefault(); }"
            @focus-outside="(e) => { if (props.draggable) e.preventDefault(); }"
        >
            <slot />

            <DialogClose
                class="absolute right-4 top-4 size-8 rounded-md flex items-center justify-center bg-muted/50 hover:bg-muted transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none"
            >
                <X class="size-4" />
                <span class="sr-only">Close</span>
            </DialogClose>
        </DialogContent>
    </DialogPortal>
</template>
