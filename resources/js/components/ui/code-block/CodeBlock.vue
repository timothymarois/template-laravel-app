<template>
    <div class="group relative overflow-hidden rounded border border-border/60 bg-muted/40">
        <!-- Header strip: language indicator (always visible) + copy button (hover) -->
        <div class="absolute top-1 right-1 z-10 flex items-center gap-1">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-background/80 hover:text-foreground cursor-pointer transition-colors"
                        :title="`Language: ${activeLanguageLabel}. Click to change.`"
                        :aria-label="`Language: ${activeLanguageLabel}. Click to change.`"
                    >
                        <span>{{ activeLanguageLabel }}</span>
                        <ChevronDown class="h-3 w-3" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="max-h-72 overflow-y-auto">
                    <DropdownMenuItem
                        class="cursor-pointer"
                        @select="onLanguageChange('auto')"
                    >
                        <span>Auto-detect</span>
                        <Check v-if="activeLanguage === 'auto'" class="ml-auto h-3.5 w-3.5" />
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        v-for="lang in LANGUAGES"
                        :key="lang.value"
                        class="cursor-pointer"
                        @select="onLanguageChange(lang.value)"
                    >
                        <span>{{ lang.label }}</span>
                        <Check v-if="lang.value === activeLanguage" class="ml-auto h-3.5 w-3.5" />
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <button
                type="button"
                class="h-7 w-7 inline-flex items-center justify-center rounded text-muted-foreground hover:text-foreground hover:bg-background/80 opacity-0 group-hover:opacity-100 focus-visible:opacity-100 transition-opacity cursor-pointer"
                :title="copied ? 'Copied' : 'Copy to clipboard'"
                :aria-label="copied ? 'Copied' : 'Copy to clipboard'"
                @click="onCopy"
            >
                <component :is="copied ? Check : Copy" class="h-3.5 w-3.5" />
            </button>
        </div>

        <pre
            class="overflow-x-auto m-0"
        ><code
            class="hljs block bg-transparent text-foreground text-sm font-mono leading-relaxed px-3 py-2 pr-24"
            :class="multiline ? 'whitespace-pre' : 'whitespace-pre-wrap break-all'"
            v-html="highlightedHtml"
        /></pre>
    </div>
</template>

<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';
import { Check, ChevronDown, Copy } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { highlightCode, LANGUAGES, type LanguageId } from './highlighter';

const props = defineProps<{
    /** The code text to render and copy. */
    code: string;
    /**
     * Language id to use for highlighting. Omit (or pass `'auto'`) to let
     * highlight.js detect from the snippet. Supports two-way binding via
     * `v-model:language` so callers can react to user picks from the
     * dropdown.
     */
    language?: LanguageId;
}>();

const emit = defineEmits<{
    'update:language': [value: LanguageId];
}>();

// Canonicalize caller-friendly aliases (`'html'`, `'vue'`) to the hljs id
// (`'xml'`) so the dropdown label and check-mark logic find a match in the
// LANGUAGES list — otherwise the indicator falls back to the raw alias
// string and no row gets ticked.
function normalize(id: LanguageId | undefined): LanguageId {
    if (id === 'html' || id === 'vue') return 'xml' as LanguageId;
    return id ?? 'auto';
}

const activeLanguage = ref<LanguageId>(normalize(props.language));

watch(() => props.language, (next) => {
    const normalized = normalize(next);
    if (normalized !== activeLanguage.value) {
        activeLanguage.value = normalized;
    }
});

const highlightResult = computed(() => highlightCode(props.code, activeLanguage.value));
const highlightedHtml = computed(() => highlightResult.value.html);

const activeLanguageLabel = computed(() => {
    // Auto-detect: show the detected language directly (no "auto" prefix).
    // Falls back to "Plain text" if hljs can't classify the snippet.
    if (activeLanguage.value === 'auto') {
        const detected = LANGUAGES.find((l) => l.value === highlightResult.value.detected);
        return detected?.label ?? 'Plain text';
    }
    const lang = LANGUAGES.find((l) => l.value === activeLanguage.value);
    return lang?.label ?? activeLanguage.value;
});

function onLanguageChange(value: string): void {
    const normalized = normalize(value as LanguageId);
    activeLanguage.value = normalized;
    emit('update:language', normalized);
}

// Multi-line snippets get `whitespace-pre` so layout is preserved as
// written; single-line snippets get `whitespace-pre-wrap` so a long URL
// or auth header wraps inside the card instead of forcing horizontal scroll.
const multiline = computed(() => props.code.includes('\n'));

const copied = ref(false);
let resetTimer: ReturnType<typeof setTimeout> | null = null;

async function onCopy(): Promise<void> {
    try {
        await navigator.clipboard.writeText(props.code);
        copied.value = true;
        if (resetTimer) clearTimeout(resetTimer);
        resetTimer = setTimeout(() => {
            copied.value = false;
        }, 1800);
    } catch {
        // Clipboard API can reject in non-secure contexts or when permission
        // is denied. Fail silently — the user can still copy by selection.
    }
}

onUnmounted(() => {
    if (resetTimer) clearTimeout(resetTimer);
});
</script>
