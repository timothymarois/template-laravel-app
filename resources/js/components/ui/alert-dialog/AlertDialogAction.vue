<script setup lang="ts">
import type { AlertDialogActionProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import type { ButtonVariants } from "@/components/ui/button";
import { reactiveOmit } from "@vueuse/core";
import { AlertDialogAction } from "reka-ui";
import { cn } from "@/utils";
import { buttonVariants } from '@/components/ui/button';

interface Props extends AlertDialogActionProps {
    class?: HTMLAttributes["class"]
    variant?: ButtonVariants["variant"]
}

const props = defineProps<Props>();

const delegatedProps = reactiveOmit(props, "class", "variant");
</script>

<template>
    <AlertDialogAction v-bind="delegatedProps" :class="cn(buttonVariants({ variant: props.variant }), props.class)">
        <slot />
    </AlertDialogAction>
</template>
