import { describe, expect, it } from 'vitest';
import {
    SIDEBAR_COOKIE_MAX_AGE,
    SIDEBAR_COOKIE_NAME,
    SIDEBAR_KEYBOARD_SHORTCUT,
    SIDEBAR_WIDTH,
    SIDEBAR_WIDTH_ICON,
    SIDEBAR_WIDTH_MOBILE,
    provideSidebarContext,
    useSidebar,
} from '@/components/ui/sidebar/utils';

describe('sidebar constants', () => {
    it('names the cookie the collapsed state is persisted under', () => {
        // The value is read by the server-rendered layout too, so it is a contract,
        // not an implementation detail — renaming it silently loses every user's state.
        expect(SIDEBAR_COOKIE_NAME).toBe('sidebar_state');
    });

    it('keeps the cookie for seven days', () => {
        expect(SIDEBAR_COOKIE_MAX_AGE).toBe(60 * 60 * 24 * 7);
    });

    it.each([
        ['SIDEBAR_WIDTH', SIDEBAR_WIDTH],
        ['SIDEBAR_WIDTH_MOBILE', SIDEBAR_WIDTH_MOBILE],
        ['SIDEBAR_WIDTH_ICON', SIDEBAR_WIDTH_ICON],
    ])('%s is a CSS length', (_name, value) => {
        expect(value).toMatch(/^\d+(\.\d+)?rem$/);
    });

    it('collapses to a narrower width than it expands to', () => {
        expect(parseFloat(SIDEBAR_WIDTH_ICON)).toBeLessThan(parseFloat(SIDEBAR_WIDTH));
    });

    it('binds a single-character keyboard shortcut', () => {
        expect(SIDEBAR_KEYBOARD_SHORTCUT).toBe('b');
    });
});

describe('sidebar context', () => {
    it('exposes a provide/inject pair', () => {
        expect(typeof provideSidebarContext).toBe('function');
        expect(typeof useSidebar).toBe('function');
    });
});
