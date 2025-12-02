<template>
    <div class="atlas-editor-content flex flex-col w-full h-full">
        <EditorToolbar
            v-if="toolbar && !textOnly"
            :editor="editorInstance"
            :rootClass="toolbarClass"
            :options="toolbarOptions"
        >
            <template #default="{ editor }">
                <slot name="toolbar" :editor="editor" />
            </template>
        </EditorToolbar>
        <EditorContent :editor="editorInstance" />
    </div>
</template>

<script setup>
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import HardBreak from '@tiptap/extension-hard-break';
import Document from '@tiptap/extension-document';
import Paragraph from '@tiptap/extension-paragraph';
import Text from '@tiptap/extension-text';
import EditorToolbar from './Toolbar.vue';
import { watch } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: [String, null],
        required: true,
    },
    placeholder: {
        type: String,
        default: '',
    },
    editorClass: {
        type: String,
        default: 'p-4 text-black text-sm dark:text-white',
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    toolbar: {
        type: Boolean,
        default: true,
    },
    toolbarOptions: {
        type: Array,
        default: () => ['bold', 'italic', 'strike', 'bullet', 'ordered', 'link', 'clear'],
    },
    textOnly: {
        type: Boolean,
        default: false,
    },
    toolbarClass: {
        type: String,
        default: 'border-b border-surface-300 dark:border-surface-700',
    }
});

const editorInstance = useEditor({
    parseOptions: {
        preserveWhitespace: 'full',
    },
    editorProps: {
        attributes: {
            class: 'w-full h-full focus:outline-none ' + props.editorClass,
        },
        handleKeyDown(view, event) {
            if (event.key === 'Enter' && !event.shiftKey && props.textOnly) {
                event.preventDefault();
                view.dispatch(
                    view.state.tr.replaceSelectionWith(
                        view.state.schema.nodes.hardBreak.create()
                    )
                );
                return true;
            }
            return false;
        },
    },
    content: props.modelValue,
    autofocus: props.autofocus,
    extensions: props.textOnly
        ? [
              Document,
              Paragraph,
              Text,
              HardBreak,
              Placeholder.configure({
                  placeholder: props.placeholder,
              }),
          ]
        : [
              StarterKit,
              Link.configure({
                  openOnClick: false,
                  defaultProtocol: 'https',
              }),
              Placeholder.configure({
                  placeholder: props.placeholder,
              }),
          ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', props.textOnly ? editor.getText() : editor.getHTML());
    },
});

defineExpose({
    editorInstance
});

watch(() => props.modelValue, (newValue) => {
    if (editorInstance.value && ((!props.textOnly && newValue !== editorInstance.value.getHTML()) || (props.textOnly && newValue !== editorInstance.value.getText()))) {
        editorInstance.value.commands.setContent(newValue, false, { preserveWhitespace: 'full' });
    }
});
</script>


<style>
.ProseMirror p.is-empty::before {
    content: attr(data-placeholder);
    color: #9ca3af; /* Tailwind's text-gray-400 */
    float: left;
    height: 0;
    pointer-events: none;
}
.atlas-editor-content {
    font-size: 0.875rem; /* Tailwind's text-sm */
    line-height: 1.425;  /* Tailwind's leading-relaxed */
}
.atlas-editor-content p:empty {
    height: 1rem;
}
.atlas-editor-content p {
    font-size: 0.875rem; /* Tailwind's text-sm */
}
.atlas-editor-content a {
    color: #4f46e5; /* Tailwind's text-indigo-600 */
    text-decoration: underline;
}
.atlas-editor-content ul,
.atlas-editor-content ol {
    padding-left: 1rem;
    margin-top: 0.125rem;
    margin-bottom: 0.125rem;
}
.atlas-editor-content ul {
    list-style-type: disc;
}
.atlas-editor-content ol {
    list-style-type: decimal;
}
</style>
