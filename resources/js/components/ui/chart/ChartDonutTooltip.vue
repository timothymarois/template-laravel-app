<script setup lang="ts">
import { ref, onMounted } from "vue";
import { VisTooltip } from "@unovis/vue";
import { Donut } from "@unovis/ts";

const props = defineProps<{
    valueKey: string
    valueFormatter?: (value: number) => string
}>();

const selector = Donut.selectors.segment;
const markerRef = ref<HTMLElement | null>(null);

// Add interactive class only to this tooltip's container
onMounted(() => {
    setTimeout(() => {
        if (!markerRef.value) return;

        // Find the container that holds both the marker and the donut
        const rootSelector = Donut.selectors.root;
        let parent = markerRef.value.parentElement;

        while (parent) {
            const donut = parent.querySelector(`.${rootSelector}`);
            if (donut) {
                parent.classList.add('chart-interactive');
                break;
            }
            parent = parent.parentElement;
        }
    }, 100);
});

function template(d: any, i: number, elements: (HTMLElement | SVGElement)[]) {
    if (!d?.data) return '';

    const data = d.data;
    const name = data.name || 'Unknown';
    const value = data[props.valueKey];
    const formattedValue = props.valueFormatter ? props.valueFormatter(value) : value?.toLocaleString() ?? value;
    const style = getComputedStyle(elements[i]);
    const color = style.fill;

    return `
    <div style="display: flex; align-items: center; gap: 8px;">
      <span style="width: 10px; height: 10px; border-radius: 50%; background: ${color}; flex-shrink: 0;"></span>
      <span style="font-weight: 500;">${name}</span>
      <span style="font-weight: 600; margin-left: 8px;">${formattedValue}</span>
    </div>
  `;
}
</script>

<template>
  <span ref="markerRef" class="hidden" aria-hidden="true" />
  <VisTooltip
    :horizontal-shift="15"
    :vertical-shift="15"
    :triggers="{ [selector]: template }"
  />
</template>

