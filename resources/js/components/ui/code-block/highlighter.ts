// Configured highlight.js instance for the CodeBlock component.
// Languages are registered explicitly (not via the full bundle) so Vite can
// tree-shake — adding a language means one entry in LANGUAGE_DEFS below.

import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import css from 'highlight.js/lib/languages/css';
import diff from 'highlight.js/lib/languages/diff';
import dockerfile from 'highlight.js/lib/languages/dockerfile';
import go from 'highlight.js/lib/languages/go';
import ini from 'highlight.js/lib/languages/ini';
import javascript from 'highlight.js/lib/languages/javascript';
import json from 'highlight.js/lib/languages/json';
import markdown from 'highlight.js/lib/languages/markdown';
import nginx from 'highlight.js/lib/languages/nginx';
import php from 'highlight.js/lib/languages/php';
import phpTemplate from 'highlight.js/lib/languages/php-template';
import plaintext from 'highlight.js/lib/languages/plaintext';
import python from 'highlight.js/lib/languages/python';
import ruby from 'highlight.js/lib/languages/ruby';
import shell from 'highlight.js/lib/languages/shell';
import sql from 'highlight.js/lib/languages/sql';
import typescript from 'highlight.js/lib/languages/typescript';
import xml from 'highlight.js/lib/languages/xml';
import yaml from 'highlight.js/lib/languages/yaml';

import type { LanguageFn } from 'highlight.js';

// Single source of truth — drives registration, the dropdown list, and the
// LanguageId union. Add a language by importing it above and appending one
// row here. Omit `label` to register the grammar without surfacing it in the
// dropdown (useful for auto-detect helpers like `php-template`).
type LanguageDef = {
    id: string;
    lang: LanguageFn;
    label?: string;
};

const LANGUAGE_DEFS: LanguageDef[] = [
    { id: 'bash', lang: bash, label: 'Bash' },
    { id: 'css', lang: css, label: 'CSS' },
    { id: 'diff', lang: diff, label: 'Diff' },
    { id: 'dockerfile', lang: dockerfile, label: 'Dockerfile' },
    { id: 'go', lang: go, label: 'Go' },
    { id: 'ini', lang: ini, label: 'INI' },
    { id: 'javascript', lang: javascript, label: 'JavaScript' },
    { id: 'json', lang: json, label: 'JSON' },
    { id: 'markdown', lang: markdown, label: 'Markdown' },
    { id: 'nginx', lang: nginx, label: 'Nginx' },
    { id: 'php', lang: php, label: 'PHP' },
    // php-template is registered for hljs's auto-detection of inline PHP in
    // HTML/Blade snippets but not surfaced in the dropdown.
    { id: 'php-template', lang: phpTemplate },
    { id: 'plaintext', lang: plaintext, label: 'Plain text' },
    { id: 'python', lang: python, label: 'Python' },
    { id: 'ruby', lang: ruby, label: 'Ruby' },
    { id: 'shell', lang: shell, label: 'Shell' },
    { id: 'sql', lang: sql, label: 'SQL' },
    { id: 'typescript', lang: typescript, label: 'TypeScript' },
    { id: 'xml', lang: xml, label: 'HTML / XML' },
    { id: 'yaml', lang: yaml, label: 'YAML' },
];

LANGUAGE_DEFS.forEach(({ id, lang }) => hljs.registerLanguage(id, lang));

export { hljs };

export type LanguageId = 'auto' | 'html' | 'vue' | (typeof LANGUAGE_DEFS)[number]['id'];

// Dropdown-visible languages (sorted by label for the menu). Items without
// a label are registered but hidden.
export const LANGUAGES: { value: string; label: string }[] = LANGUAGE_DEFS
    .filter((def): def is LanguageDef & { label: string } => def.label !== undefined)
    .map(({ id, label }) => ({ value: id, label }))
    .sort((a, b) => a.label.localeCompare(b.label));

/**
 * Highlight `code` to HTML. When `language` is omitted or set to `auto`,
 * uses hljs's auto-detection and returns the detected language alongside
 * the HTML so callers can surface it in the UI.
 */
export function highlightCode(code: string, language?: string): { html: string; detected: string } {
    if (!language || language === 'auto') {
        const result = hljs.highlightAuto(code);
        return { html: result.value, detected: result.language ?? 'plaintext' };
    }

    // Map common aliases that aren't hljs's canonical names.
    const lang = language === 'html' || language === 'vue' ? 'xml' : language;

    try {
        const result = hljs.highlight(code, { language: lang, ignoreIllegals: true });
        return { html: result.value, detected: lang };
    } catch {
        // Unknown language id — fall back to plain text.
        const result = hljs.highlight(code, { language: 'plaintext', ignoreIllegals: true });
        return { html: result.value, detected: 'plaintext' };
    }
}
