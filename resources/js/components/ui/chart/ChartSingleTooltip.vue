<script setup lang="ts">
/* eslint-disable vue/one-component-per-file */
import type { BulletLegendItemInterface } from "@unovis/ts";
import type { Component } from "vue";
import { omit } from "@unovis/ts";
import { VisTooltip } from "@unovis/vue";
import { createApp, watch } from "vue";
import { ChartTooltip } from ".";

const props = defineProps<{
    selector: string
    index: string
    items?: BulletLegendItemInterface[]
    valueFormatter?: (tick: number, i?: number, ticks?: number[]) => string
    customTooltip?: Component
}>();

// Use weakmap to store reference to each datapoint for Tooltip
let wm = new WeakMap();

// Reset cache when props that affect tooltip content change
watch(() => [props.index, props.items, props.valueFormatter], () => {
    wm = new WeakMap();
});

function template(d: any, i: number, elements: (HTMLElement | SVGElement)[]) {
    const valueFormatter = props.valueFormatter ?? ((tick: number) => `${tick}`);
    if (props.index in d) {
        if (wm.has(d)) {
            return wm.get(d);
        }
        else {
            const componentDiv = document.createElement("div");
            const omittedData = Object.entries(omit(d, [props.index])).map(([key, value]) => {
                const legendReference = props.items?.find(i => i.name === key);
                return { ...legendReference, value: valueFormatter(value) };
            });
            const TooltipComponent = props.customTooltip ?? ChartTooltip;
            const app = createApp(TooltipComponent, { title: d[props.index], data: omittedData });
            app.mount(componentDiv);
            const html = componentDiv.innerHTML;
            app.unmount();
            wm.set(d, html);
            return html;
        }
    }

    else {
        const data = d.data;

        if (wm.has(data)) {
            return wm.get(data);
        }
        else {
            const style = getComputedStyle(elements[i]);
            const omittedData = [{ name: data.name, value: valueFormatter(data[props.index]), color: style.fill }];
            const componentDiv = document.createElement("div");
            const TooltipComponent = props.customTooltip ?? ChartTooltip;
            const app = createApp(TooltipComponent, { title: d[props.index], data: omittedData });
            app.mount(componentDiv);
            const html = componentDiv.innerHTML;
            app.unmount();
            wm.set(d, html);
            return html;
        }
    }
}
</script>

<template>
    <VisTooltip
        :horizontal-shift="20" :vertical-shift="20" :triggers="{
            [selector]: template,
        }"
    />
</template>
