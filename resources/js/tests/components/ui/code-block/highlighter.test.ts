import { describe, expect, it } from 'vitest';
import { LANGUAGES, highlightCode } from '@/components/ui/code-block/highlighter';

describe('LANGUAGES', () => {
    it('lists only languages that declare a label', () => {
        // php-template is registered for auto-detection of inline PHP but is
        // deliberately not offered in the dropdown.
        expect(LANGUAGES.every((entry) => Boolean(entry.label))).toBe(true);
        expect(LANGUAGES.map((entry) => entry.value)).not.toContain('php-template');
    });

    it('is sorted by label', () => {
        const labels = LANGUAGES.map((entry) => entry.label);

        expect(labels).toEqual([...labels].sort((a, b) => a.localeCompare(b)));
    });

    it('exposes the languages the kit claims to support', () => {
        const values = LANGUAGES.map((entry) => entry.value);

        expect(values).toEqual(expect.arrayContaining(['php', 'javascript', 'json', 'bash', 'sql']));
    });
});

describe('highlightCode', () => {
    it('auto-detects when no language is given', () => {
        const { html, detected } = highlightCode('SELECT * FROM users;');

        expect(html).toContain('<span');
        expect(detected).not.toBe('');
    });

    it('auto-detects when the language is the literal "auto"', () => {
        const { detected } = highlightCode('{"a": 1}', 'auto');

        expect(detected).not.toBe('');
    });

    it('reports plaintext when auto-detection finds nothing', () => {
        const { detected } = highlightCode('', 'auto');

        expect(detected).toBe('plaintext');
    });

    it.each(['html', 'vue'])('maps the %s alias onto the xml grammar', (alias) => {
        const { detected, html } = highlightCode('<div class="a">hi</div>', alias);

        expect(detected).toBe('xml');
        expect(html).toContain('<span');
    });

    it('uses the requested language when it is registered', () => {
        const { detected, html } = highlightCode('<?php echo 1;', 'php');

        expect(detected).toBe('php');
        expect(html).toContain('<span');
    });

    it('falls back to plaintext for an unregistered language rather than throwing', () => {
        const { detected } = highlightCode('some code', 'not-a-language');

        expect(detected).toBe('plaintext');
    });

    it('escapes markup so highlighted output cannot inject HTML', () => {
        const { html } = highlightCode('<script>alert(1)</script>', 'plaintext');

        expect(html).not.toContain('<script>');
        expect(html).toContain('&lt;');
    });
});
