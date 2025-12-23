<script lang="ts" setup>
import type { ToasterProps } from "vue-sonner";
import { reactiveOmit } from "@vueuse/core";
import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon, XIcon } from "lucide-vue-next";
import { Toaster as Sonner } from "vue-sonner";

const props = defineProps<ToasterProps>();
const delegatedProps = reactiveOmit(props, "toastOptions");
</script>

<template>
    <Sonner
        class="toaster group sonner-custom"
        :toast-options="{
            classes: {
                toast: 'group toast group-[.toaster]:bg-background group-[.toaster]:text-foreground group-[.toaster]:border-border group-[.toaster]:shadow-lg',
                description: 'group-[.toast]:text-muted-foreground',
                actionButton:
                    'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
                cancelButton:
                    'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
            },
        }"
        v-bind="delegatedProps"
    >
        <template #success-icon>
            <CircleCheckIcon class="size-4" />
        </template>
        <template #info-icon>
            <InfoIcon class="size-4" />
        </template>
        <template #warning-icon>
            <TriangleAlertIcon class="size-4" />
        </template>
        <template #error-icon>
            <OctagonXIcon class="size-4" />
        </template>
        <template #loading-icon>
            <div>
                <Loader2Icon class="size-4 animate-spin" />
            </div>
        </template>
        <template #close-icon>
            <XIcon class="size-4" />
        </template>
    </Sonner>
</template>

<style>
[data-sonner-toast] [data-close-button] {
    left: unset !important;
    right: 6px !important;
    top: 6px !important;
    transform: none !important;
    border: none !important;
    background: transparent !important;
    color: hsl(var(--muted-foreground)) !important;
}

[data-sonner-toast] [data-close-button]:hover {
    background: hsl(var(--muted)) !important;
    color: hsl(var(--foreground)) !important;
}

/* Hide close button when toast has action buttons */
[data-sonner-toast]:has([data-button]) [data-close-button] {
    display: none !important;
}
</style>
