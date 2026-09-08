<template>
    <Sheet v-model:open="isOpen">
        <SheetContent :side="position" class="flex flex-col !max-w-none p-0 overflow-hidden" :style="widthStyle">
            <!-- Header -->
            <div class="px-6 pt-4" :class="tabs?.length ? 'pb-2' : 'pb-4 border-b border-border'">
                <SheetTitle>{{ title }}</SheetTitle>
                <!-- reka-ui's dialog contract requires a description; without one it
                     warns and ships no aria-describedby. The sr-only fallback keeps
                     every existing caller compliant without changing what it renders. -->
                <SheetDescription v-if="$slots.description" class="mt-1 text-sm text-muted-foreground">
                    <slot name="description" />
                </SheetDescription>
                <SheetDescription v-else-if="description" class="mt-1 text-sm text-muted-foreground">
                    {{ description }}
                </SheetDescription>
                <SheetDescription v-else class="sr-only">{{ title }}</SheetDescription>
            </div>

            <!-- With Tabs -->
            <template v-if="tabs?.length">
                <Tabs v-model="activeTabValue" variant="underline" class="flex flex-col flex-1 min-h-0">
                    <div class="border-b border-border">
                        <TabsList class="px-6 border-b-0">
                            <TabsTrigger
                                v-for="(tab, index) in tabs"
                                :key="index"
                                :value="String(index)"
                                :disabled="tab.disabled"
                            >
                                {{ tab.title }}
                            </TabsTrigger>
                        </TabsList>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4">
                        <TabsContent value="0" class="mt-0">
                            <slot />
                        </TabsContent>
                        <TabsContent
                            v-for="(tab, index) in tabs.slice(1)"
                            :key="index"
                            :value="String(index + 1)"
                            class="mt-0"
                        >
                            <slot :name="`tab-${index + 1}`" />
                        </TabsContent>
                    </div>
                </Tabs>
            </template>

            <!-- Without Tabs -->
            <template v-else>
                <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4">
                    <slot />
                </div>
            </template>

            <!-- Footer -->
            <div class="bg-muted/30">
                <!-- Errors -->
                <FormErrors
                    v-if="hasErrors"
                    :errors="errors"
                    :expandDefault="true"
                    class="rounded-none border-0"
                />
                <div class="px-6 py-4 flex items-center gap-2 border-t border-border">
                    <Button :disabled="loading || submitDisabled" @click="$emit('submit')">
                        <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitLabel }}
                    </Button>
                    <Button variant="ghost" @click="isOpen = false">{{ cancelLabel }}</Button>
                    <div v-if="$slots['footer-actions']" class="ml-auto flex items-center gap-2">
                        <slot name="footer-actions" />
                    </div>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import Sheet from './Sheet.vue';
import SheetContent from './SheetContent.vue';
import SheetDescription from './SheetDescription.vue';
import SheetTitle from './SheetTitle.vue';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Loader2 } from 'lucide-vue-next';
import { FormErrors } from '@/components/ui/form-errors';

interface Tab {
    title: string;
    disabled?: boolean;
}

interface Props {
    modelValue?: boolean;
    title?: string;
    /** Announced to screen readers, and shown under the title when set. */
    description?: string;
    tabs?: Tab[];
    position?: 'left' | 'right' | 'top' | 'bottom';
    width?: string;
    loading?: boolean;
    /** Name the outcome, e.g. 'Create account' — keep it identical to whatever opened the sheet. */
    submitLabel?: string;
    cancelLabel?: string;
    submitDisabled?: boolean;
    errors?: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: '',
    description: '',
    tabs: () => [],
    position: 'right',
    width: '400px',
    loading: false,
    submitLabel: 'Save',
    cancelLabel: 'Cancel',
    submitDisabled: false,
    errors: () => ({}),
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
    'submit': [];
}>();

const activeTabValue = ref('0');

const isOpen = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const widthStyle = computed(() => {
    if (props.position === 'left' || props.position === 'right') {
        // min() clamps the width itself. Relying on maxWidth does not work here:
        // SheetContent carries `!max-w-none`, and Tailwind's `!` is !important,
        // which beats an inline max-width. A sheet asked for 720px therefore
        // stayed 720px in a 390px viewport and was laid out off-screen, with its
        // Save and Cancel controls unreachable.
        return { width: `min(${props.width}, 100vw)`, maxWidth: '100%' };
    }
    return {};
});

const hasErrors = computed(() => Object.keys(props.errors).length > 0);
</script>
