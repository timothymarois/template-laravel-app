<template>
    <div
        ref="dropzoneRef"
        :class="[
            'relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 transition-all',
            disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
            invalid
                ? 'border-destructive bg-destructive/5 hover:border-destructive'
                : isDragOver
                    ? 'border-primary bg-primary/5'
                    : 'border-muted-foreground/25 hover:border-muted-foreground/50 hover:bg-muted/30',
        ]"
        @click="triggerInput"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
    >
        <input
            ref="inputRef"
            type="file"
            :accept="accept"
            :multiple="multiple"
            :disabled="disabled"
            class="hidden"
            @change="handleChange"
        />

        <!-- Default slot for custom content -->
        <slot :files="files" :isDragOver="isDragOver" :removeFile="removeFile" :clearFiles="clearFiles">
            <div class="flex flex-col items-center gap-2 text-center">
                <div :class="['rounded-full p-3', isDragOver ? 'bg-primary/10' : 'bg-muted']">
                    <Upload :class="['size-6', isDragOver ? 'text-primary' : 'text-muted-foreground']" />
                </div>
                <div>
                    <p class="text-sm font-medium">
                        <span v-if="isDragOver" class="text-primary">Drop files here</span>
                        <span v-else>Drag & drop files here, or <span class="text-primary">browse</span></span>
                    </p>
                    <p v-if="hint" class="mt-1 text-xs text-muted-foreground">{{ hint }}</p>
                </div>
            </div>
        </slot>

        <!-- File list -->
        <div v-if="files.length > 0 && showFileList" class="mt-4 w-full space-y-2">
            <div
                v-for="(file, index) in files"
                :key="index"
                class="flex items-center justify-between rounded-md border bg-background px-3 py-2"
            >
                <div class="flex items-center gap-2 min-w-0">
                    <FileIcon class="size-4 text-muted-foreground shrink-0" />
                    <span class="text-sm truncate">{{ file.name }}</span>
                    <span class="text-xs text-muted-foreground shrink-0">({{ formatBytes(file.size, 1) }})</span>
                </div>
                <button
                    type="button"
                    class="text-muted-foreground hover:text-destructive shrink-0 cursor-pointer"
                    @click.stop="removeFile(index)"
                >
                    <X class="size-4" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Upload, X, File as FileIcon } from 'lucide-vue-next';
import { formatBytes } from '@/utils/format';
import { normalizeFiles } from '@/utils/file';
import { processDropzoneFiles } from './processDropzoneFiles';

interface Props {
    modelValue?: File | File[] | null;
    accept?: string;
    multiple?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    maxSize?: number; // in bytes
    maxFiles?: number;
    hint?: string;
    showFileList?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    multiple: false,
    showFileList: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: File | File[] | null];
    'change': [files: File[]];
    'error': [error: { type: string; message: string; file?: File }];
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const dropzoneRef = ref<HTMLElement | null>(null);
const isDragOver = ref(false);

const files = computed<File[]>(() => normalizeFiles(props.modelValue));

const processFiles = (newFiles: File[]) => {
    if (props.disabled) return;
    const { errors, nextValue } = processDropzoneFiles({
        newFiles,
        existingFiles: files.value,
        accept: props.accept,
        maxSize: props.maxSize,
        maxFiles: props.maxFiles,
        multiple: props.multiple,
    });

    if (errors.length > 0) {
        errors.forEach((error) => emit('error', error));
    }

    if (nextValue === undefined) return;

    emit('update:modelValue', nextValue);
    const emittedFiles = Array.isArray(nextValue) ? nextValue : nextValue ? [nextValue] : [];
    emit('change', emittedFiles);
};

const triggerInput = () => {
    if (props.disabled) return;
    inputRef.value?.click();
};

const handleChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const newFiles = Array.from(target.files || []);
    processFiles(newFiles);
    // Reset input so same file can be selected again
    target.value = '';
};

const handleDragOver = () => {
    if (props.disabled) return;
    isDragOver.value = true;
};

const handleDragLeave = () => {
    isDragOver.value = false;
};

const handleDrop = (event: DragEvent) => {
    if (props.disabled) return;
    isDragOver.value = false;

    const newFiles = Array.from(event.dataTransfer?.files || []);
    processFiles(newFiles);
};

const removeFile = (index: number) => {
    if (props.disabled) return;

    const newFiles = [...files.value];
    newFiles.splice(index, 1);

    const result = props.multiple ? newFiles : (newFiles[0] || null);
    emit('update:modelValue', result);
    emit('change', newFiles);
};

const clearFiles = () => {
    if (props.disabled) return;
    emit('update:modelValue', null);
    emit('change', []);
};

defineExpose({
    clear: clearFiles,
    trigger: triggerInput,
    removeFile,
});
</script>
