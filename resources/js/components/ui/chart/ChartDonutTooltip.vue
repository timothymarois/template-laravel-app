<script setup lang="ts">
import { VisTooltip } from "@unovis/vue";
import { Donut } from "@unovis/ts";

const props = defineProps<{
  valueKey: string
  valueFormatter?: (value: number) => string
}>();

const selector = Donut.selectors.segment;

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
  <VisTooltip
    :horizontal-shift="15"
    :vertical-shift="15"
    :triggers="{ [selector]: template }"
  />
</template>

<style>
.unovis-tooltip {
  --vis-tooltip-background-color: hsl(var(--popover)) !important;
  --vis-tooltip-border-color: hsl(var(--border)) !important;
  --vis-tooltip-text-color: hsl(var(--popover-foreground)) !important;
  --vis-tooltip-shadow-color: rgba(0, 0, 0, 0.1) !important;
  --vis-tooltip-padding: 8px 12px !important;
  --vis-tooltip-border-radius: 6px !important;
}
</style>
