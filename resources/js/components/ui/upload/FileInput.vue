<template>
    <div class="relative" :class="{ 'w-full': fluid }">
        <input
            ref="inputRef"
            type="file"
            :accept="accept"
            :multiple="multiple"
            :disabled="disabled"
            class="hidden"
            @change="handleChange"
        />
        <button
            type="button"
            :disabled="disabled"
            :class="[
                'flex items-center gap-2 h-9 rounded-md border bg-background px-3 text-sm transition-all cursor-pointer',
                'disabled:cursor-not-allowed disabled:opacity-50',
                invalid
                    ? 'border-destructive hover:border-destructive focus-visible:border-destructive'
                    : 'border-input hover:border-foreground/50 focus-visible:border-foreground/50 disabled:hover:border-input',
                fluid ? 'w-full' : '',
            ]"
            @click="triggerInput"
        >
            <Upload class="size-4 text-muted-foreground" />
            <span v-if="!hasFiles" class="text-muted-foreground">{{ placeholder }}</span>
            <span v-else class="truncate">{{ fileNames }}</span>
        </button>
        <button
            v-if="hasFiles && clearable"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground cursor-pointer"
            @click.stop="clearFiles"
        >
            <X class="size-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Upload, X } from 'lucide-vue-next';
import { hasFiles as checkHasFiles, getFileNames } from '@/utils/file';

interface Props {
    modelValue?: File | File[] | null;
    accept?: string;
    multiple?: boolean;
    disabled?: boolean;
    fluid?: boolean;
    invalid?: boolean;
    clearable?: boolean;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Choose file...',
    clearable: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: File | File[] | null];
    'change': [files: File[]];
}>();

const inputRef = ref<HTMLInputElement | null>(null);

const hasFiles = computed(() => checkHasFiles(props.modelValue));

const fileNames = computed(() => getFileNames(props.modelValue));

const triggerInput = () => {
    inputRef.value?.click();
};

const handleChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = Array.from(target.files || []);

    if (files.length === 0) return;

    const value = props.multiple ? files : files[0];
    emit('update:modelValue', value);
    emit('change', files);
};

const clearFiles = () => {
    if (inputRef.value) {
        inputRef.value.value = '';
    }
    emit('update:modelValue', null);
    emit('change', []);
};

defineExpose({
    clear: clearFiles,
    trigger: triggerInput,
});
</script>
