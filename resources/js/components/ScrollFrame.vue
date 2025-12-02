<template>
    <div
        ref="frame"
        class="frame-scroll"
        :class="rootClass"
        :style="scrollable ? { height: `calc(100vh - ${dynamicHeight} - ${addOffset}px)` } : undefined"
    >
        <slot />
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, onMounted, onBeforeUnmount, onUpdated } from 'vue';
import { useScroll } from '../composables/useScroll';

interface Props {
    scrollKey?: string | null;
    page?: boolean;
    allowBodyScroll?: boolean;
    offset?: number | null;
    addOffset?: number;
    rootClass?: string;
    scrollable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    scrollKey: null,
    page: false,
    allowBodyScroll: false,
    offset: null,
    addOffset: 0,
    rootClass: 'overflow-y-auto',
    scrollable: true,
});

const frame = ref<HTMLElement | null>(null);
const dynamicHeight = ref('0px');
let resizeObserver: ResizeObserver | null = null;
let intersectionObserver: IntersectionObserver | null = null;
let mutationObserver: MutationObserver | null = null;

const { bindScrollHandler, lockScroll, unlockScroll } = useScroll(
    props.scrollKey !== null ? props.scrollKey : props.page ? 'page' : Symbol()
);

const { add, remove } = bindScrollHandler(frame);

const updateHeight = () => {
    const offsetValue = props.offset !== null ? props.offset : calculateOffsets();
    dynamicHeight.value = `${offsetValue}px`;
};

const calculateOffsets = () => {
    const frameElement = frame.value;
    if (!frameElement) return 0;

    const { top } = frameElement.getBoundingClientRect();
    return Math.round(top);
};

onMounted(() => {
    add();
    if (props.page && !props.allowBodyScroll) lockScroll();
    // Only compute and observe dynamic height when scrollable
    if (props.scrollable) {
        nextTick(() => {
            updateHeight();
        });
        if (typeof window !== 'undefined') {
            window.addEventListener('resize', updateHeight);
            if (typeof ResizeObserver !== 'undefined') {
                resizeObserver = new ResizeObserver(() => updateHeight());
                const element = frame.value?.parentElement ?? document.body;
                resizeObserver.observe(element);
            }
            if (typeof IntersectionObserver !== 'undefined') {
                intersectionObserver = new IntersectionObserver((entries) => {
                    if (entries.some((entry) => entry.isIntersecting)) {
                        updateHeight();
                    }
                });
                if (frame.value) intersectionObserver.observe(frame.value);
            }
            if (typeof MutationObserver !== 'undefined') {
                mutationObserver = new MutationObserver((mutations) => {
                    const frameEl = frame.value;
                    if (!frameEl) return;
                    const shouldUpdate = mutations.some(
                        (m) => !frameEl.contains(m.target as Node)
                    );
                    if (shouldUpdate) updateHeight();
                });
                mutationObserver.observe(document.body, {
                    attributes: true,
                    childList: true,
                    subtree: true,
                });
            }
        }
    }
});

onUpdated(() => {
    if (props.scrollable) {
        nextTick(() => {
            updateHeight();
        });
    }
});

onBeforeUnmount(() => {
    remove();
    if (props.page && !props.allowBodyScroll) unlockScroll();
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', updateHeight);
        resizeObserver?.disconnect();
        intersectionObserver?.disconnect();
        mutationObserver?.disconnect();
    }
});

watch(() => props.offset, () => props.scrollable && updateHeight());
watch(() => props.addOffset, () => props.scrollable && updateHeight());

watch(
    () => props.allowBodyScroll,
    () => {
        if (!props.page) return;
        props.allowBodyScroll ? unlockScroll() : lockScroll();
    }
);
</script>
