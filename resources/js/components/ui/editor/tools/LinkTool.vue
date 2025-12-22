<template>
    <div class="relative">
        <Tooltip>
            <TooltipTrigger as-child>
                <Button
                    icon
                    text
                    size="small"
                    class="!px-2 hover:!bg-accent/50"
                    @click="toggleLinkPopover"
                >
                    <div class="flex items-center text-foreground">
                        <IconLink class="size-5" />
                    </div>
                </Button>
            </TooltipTrigger>
            <TooltipContent>Add link</TooltipContent>
        </Tooltip>
        <Popover ref="linkPopover">
            <div class="flex items-center space-x-2 w-[320px]">
                <div class="flex-1">
                    <InputText
                        v-model.trim="linkText"
                        fluid
                        type="text"
                        placeholder="https://example.com"
                        @keyup.enter="toggleLink"
                    />
                </div>
                <div>
                    <Button
                        size="small"
                        :disabled="!isValidUrl"
                        @click="toggleLink"
                    >
                        <Check class="size-4" />
                    </Button>
                </div>
            </div>
        </Popover>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { IconLink } from '@tabler/icons-vue';
import { Check } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Popover } from '@/components/ui/popover';
import { Input as InputText } from '@/components/ui/form';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
    editor: Object
});

const linkPopover = ref();
const linkText = ref('');

const isValidUrl = computed(() => /^https?:\/\//i.test(linkText.value));

const toggleLink = () => {
    if (!props.editor || !isValidUrl.value) return;

    props.editor
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: linkText.value })
        .run();

    linkPopover.value.hide();
};

const toggleLinkPopover = (event) => {
    linkText.value = '';
    const isLinkActive = props.editor?.isActive('link');
    if (isLinkActive) {
        props.editor.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        linkPopover.value.toggle(event);
    }
};
</script>
