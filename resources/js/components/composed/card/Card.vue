<template>
    <Card :class="className">
        <CardHeader v-if="$slots.header || $slots.title || $slots.subtitle">
            <slot name="header">
                <CardTitle v-if="$slots.title">
                    <slot name="title" />
                </CardTitle>
                <CardDescription v-if="$slots.subtitle">
                    <slot name="subtitle" />
                </CardDescription>
            </slot>
        </CardHeader>
        <CardContent :class="{ 'p-0': noPadding }">
            <slot name="content">
                <slot />
            </slot>
        </CardContent>
        <CardFooter v-if="$slots.footer">
            <slot name="footer" />
        </CardFooter>
    </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface Props {
    noPadding?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    noPadding: false,
});

const className = computed(() => props.class || '');
</script>
