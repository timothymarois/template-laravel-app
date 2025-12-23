<script setup lang="ts">
import type { BulletLegendItemInterface } from "@unovis/ts";
import type { Component } from "vue";
import { omit } from "@unovis/ts";
import { VisCrosshair, VisTooltip } from "@unovis/vue";
import { createApp, watch } from "vue";
import { ChartTooltip } from ".";

const props = withDefaults(defineProps<{
    colors: string[]
    index: string
    items: BulletLegendItemInterface[]
    customTooltip?: Component
}>(), {
    colors: () => [],
});

// Use weakmap to store reference to each datapoint for Tooltip
let wm = new WeakMap();

// Reset cache when props that affect tooltip content change
watch(() => [props.index, props.items], () => {
    wm = new WeakMap();
});

function template(d: any) {
    if (wm.has(d)) {
        return wm.get(d);
    }
    else {
        const componentDiv = document.createElement("div");
        const omittedData = Object.entries(omit(d, [props.index])).map(([key, value]) => {
            const legendReference = props.items.find(i => i.name === key);
            return { ...legendReference, value };
        });
        const TooltipComponent = props.customTooltip ?? ChartTooltip;
        const app = createApp(TooltipComponent, { title: d[props.index].toString(), data: omittedData });
        app.mount(componentDiv);
        const html = componentDiv.innerHTML;
        app.unmount();
        wm.set(d, html);
        return html;
    }
}

function color(d: unknown, i: number) {
    return props.colors[i] ?? "transparent";
}
</script>

<template>
    <VisTooltip :horizontal-shift="20" :vertical-shift="20" />
    <VisCrosshair :template="template" :color="color" />
</template>
