<template>
    <div class="relative">
        <Button
            v-tooltip.top="tooltip"
            icon
            text
            size="small"
            class="!px-2 hover:!bg-surface-200/50"
            @click="toggleLinkPopover"
        >
            <div class="flex items-center text-black dark:text-white">
                <IconLink class="size-5" />
            </div>
        </Button>
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
                        icon="pi pi-check"
                        :disabled="!isValidUrl"
                        @click="toggleLink"
                    />
                </div>
            </div>
        </Popover>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { IconLink } from '@tabler/icons-vue';
import Button from '../../Button.vue';
import Popover from '../../Popover.vue';
import InputText from '../../InputText.vue';

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

const tooltip = {
    value: 'Add link',
    pt: {
        root: 'absolute shadow-md py-0 px-0 max-w-[260px]',
        text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
    }
};
</script>
