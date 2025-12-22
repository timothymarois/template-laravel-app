import { describe, it, expect } from 'vitest';
import { isClient } from '@/utils/browser/isClient';

describe('isClient', () => {
    // In happy-dom test environment, window and document should be defined
    it('returns true in browser-like environment', () => {
        // happy-dom provides window and document
        expect(isClient).toBe(true);
    });

    it('is a boolean constant', () => {
        expect(typeof isClient).toBe('boolean');
    });

    // Note: Testing the false case would require mocking globals
    // which changes the module's constant. In a real SSR environment,
    // this would correctly return false.
});
