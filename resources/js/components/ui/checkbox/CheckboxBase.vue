<script setup lang="ts">
/**
 * CheckboxBase - Low-level checkbox primitive wrapping Reka UI's CheckboxRoot
 *
 * Usage:
 *   <CheckboxBase v-model="isChecked" />
 *   <CheckboxBase :modelValue="true" @update:modelValue="handleChange" />
 *
 * Reka UI's CheckboxRoot natively supports `modelValue` and `update:modelValue`.
 */
import type { CheckboxRootProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { Check, Minus } from "lucide-vue-next";
import { CheckboxIndicator, CheckboxRoot, useForwardPropsEmits } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<CheckboxRootProps & { class?: HTMLAttributes["class"] }>();
const emits = defineEmits<{
    'update:modelValue': [value: boolean | 'indeterminate'];
}>();

const delegatedProps = reactiveOmit(props, "class");
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <CheckboxRoot
        v-bind="forwarded"
        :class="
            cn('group peer relative h-4 w-4 shrink-0 rounded-sm border border-input bg-background transition-all cursor-pointer hover:border-foreground/50 focus-visible:outline-none focus-visible:border-foreground/50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input data-[state=checked]:border-primary data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:hover:bg-primary/80 data-[state=checked]:disabled:hover:bg-primary data-[state=indeterminate]:border-primary data-[state=indeterminate]:bg-primary data-[state=indeterminate]:text-primary-foreground data-[state=indeterminate]:hover:bg-primary/80',
               props.class)"
    >
        <span class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-30 group-data-[state=checked]:opacity-0 group-disabled:!opacity-0">
            <Check class="h-3 w-3" :stroke-width="3" />
        </span>
        <CheckboxIndicator class="absolute inset-0 flex items-center justify-center text-current">
            <slot>
                <Minus v-if="props.modelValue === 'indeterminate'" class="h-3 w-3" :stroke-width="3" />
                <Check v-else class="h-3 w-3" :stroke-width="3" />
            </slot>
        </CheckboxIndicator>
    </CheckboxRoot>
</template>
