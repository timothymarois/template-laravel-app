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
                    <span class="text-xs text-muted-foreground shrink-0">({{ formatFileSize(file.size) }})</span>
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
import { ref, computed, watch } from 'vue';
import { Upload, X, File as FileIcon } from 'lucide-vue-next';

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

const files = computed<File[]>(() => {
    if (!props.modelValue) return [];
    if (Array.isArray(props.modelValue)) return props.modelValue;
    return [props.modelValue];
});

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const validateFile = (file: File): boolean => {
    // Check file size
    if (props.maxSize && file.size > props.maxSize) {
        emit('error', {
            type: 'size',
            message: `File "${file.name}" exceeds maximum size of ${formatFileSize(props.maxSize)}`,
            file,
        });
        return false;
    }

    // Check file type
    if (props.accept) {
        const acceptedTypes = props.accept.split(',').map(t => t.trim());
        const fileType = file.type;
        const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase();

        const isAccepted = acceptedTypes.some(type => {
            if (type.startsWith('.')) {
                return fileExtension === type.toLowerCase();
            }
            if (type.endsWith('/*')) {
                return fileType.startsWith(type.replace('/*', '/'));
            }
            return fileType === type;
        });

        if (!isAccepted) {
            emit('error', {
                type: 'type',
                message: `File "${file.name}" is not an accepted file type`,
                file,
            });
            return false;
        }
    }

    return true;
};

const processFiles = (newFiles: File[]) => {
    if (props.disabled) return;

    // Filter valid files
    const validFiles = newFiles.filter(validateFile);

    if (validFiles.length === 0) return;

    // Check max files
    if (props.maxFiles && props.multiple) {
        const currentCount = files.value.length;
        const availableSlots = props.maxFiles - currentCount;

        if (availableSlots <= 0) {
            emit('error', {
                type: 'maxFiles',
                message: `Maximum of ${props.maxFiles} files allowed`,
            });
            return;
        }

        if (validFiles.length > availableSlots) {
            validFiles.splice(availableSlots);
        }
    }

    let result: File | File[] | null;

    if (props.multiple) {
        result = [...files.value, ...validFiles];
    } else {
        result = validFiles[0];
    }

    emit('update:modelValue', result);
    emit('change', Array.isArray(result) ? result : [result]);
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
