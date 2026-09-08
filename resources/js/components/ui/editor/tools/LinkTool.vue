<template>
    <PopoverBase v-model:open="isPopoverOpen">
        <PopoverTrigger as-child>
            <span>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Button
                            icon
                            text
                            size="small"
                            class="!px-2"
                            :class="{
                                'hover:!bg-accent/50': true,
                                '!bg-accent text-foreground': editor?.isActive('link')
                            }"
                            @click="handleButtonClick"
                        >
                            <div class="flex items-center text-foreground">
                                <IconLink class="size-5" />
                            </div>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>{{ editor?.isActive('link') ? 'Remove link' : 'Add link' }}</TooltipContent>
                </Tooltip>
            </span>
        </PopoverTrigger>
        <PopoverContent align="start" :side-offset="8" class="w-[320px] p-3">
            <div class="flex items-center gap-2">
                <InputText
                    ref="inputRef"
                    v-model.trim="linkText"
                    fluid
                    type="url"
                    placeholder="https://example.com"
                    @keyup.enter="applyLink"
                    @keyup.escape="isPopoverOpen = false"
                />
                <Button
                    size="small"
                    :disabled="!isValidUrl"
                    @click="applyLink"
                >
                    <Check class="size-4" />
                </Button>
            </div>
            <div v-if="linkText && !isValidUrl" class="text-xs text-destructive mt-2">
                Enter a valid URL (http:// or https://)
            </div>
        </PopoverContent>
    </PopoverBase>
</template>

<script setup lang="ts">
import type { ComponentPublicInstance, PropType } from 'vue';
import type { Editor } from '@tiptap/vue-3';
import { ref, computed, watch, nextTick } from 'vue';
import { isValidURL } from '@/utils/validate';
import { IconLink } from '@tabler/icons-vue';
import { Check } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { PopoverBase, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Input as InputText } from '@/components/ui/input';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
    editor: {
        type: Object as PropType<Editor>,
        required: true,
    },
});

const isPopoverOpen = ref(false);
const linkText = ref('');
const inputRef = ref<ComponentPublicInstance | null>(null);

const isValidUrl = computed(() => isValidURL(linkText.value));

const applyLink = () => {
    if (!props.editor || !isValidUrl.value) return;

    props.editor
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: linkText.value })
        .run();

    isPopoverOpen.value = false;
    linkText.value = '';
};

const handleButtonClick = () => {
    const isLinkActive = props.editor?.isActive('link');
    if (isLinkActive) {
        // Remove link - prevent popover from opening
        props.editor.chain().focus().extendMarkRange('link').unsetLink().run();
        isPopoverOpen.value = false;
    }
    // If not active, let the PopoverTrigger handle opening
};

// Focus input when popover opens
watch(isPopoverOpen, (open) => {
    if (open) {
        linkText.value = '';
        nextTick(() => {
            inputRef.value?.$el?.focus();
        });
    }
});
</script>
