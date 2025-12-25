import { describe, it, expect } from 'vitest';
import {
    getPaginationLinkComponent,
    getPageNumberLabel,
    isEllipsisLabel,
    isNextLink,
    isPreviousLink,
    type PaginationLink,
} from '@/components/ui/pagination/paginatorUtils';

const buildLink = (label: string, url: string | null = '#', active = false): PaginationLink => ({
    label,
    url,
    active,
});

describe('paginatorUtils', () => {
    it('resolves link component based on url', () => {
        expect(getPaginationLinkComponent(buildLink('1', null), 'a')).toBe('span');
        expect(getPaginationLinkComponent(buildLink('1', '#'), 'a')).toBe('a');
        const component = {};
        expect(getPaginationLinkComponent(buildLink('1', '#'), component)).toBe(component);
    });

    it('detects previous link only at first index', () => {
        const previous = buildLink('&laquo; Previous');
        expect(isPreviousLink(previous, 0)).toBe(true);
        expect(isPreviousLink(previous, 1)).toBe(false);
    });

    it('detects next link only at last index', () => {
        const next = buildLink('Next &raquo;');
        expect(isNextLink(next, 0, 2)).toBe(false);
        expect(isNextLink(next, 1, 2)).toBe(true);
    });

    it('detects ellipsis labels', () => {
        expect(isEllipsisLabel('...')).toBe(true);
        expect(isEllipsisLabel('&hellip;')).toBe(true);
        expect(isEllipsisLabel('1')).toBe(false);
    });

    it('strips HTML entities for page labels', () => {
        expect(getPageNumberLabel('1')).toBe('1');
        expect(getPageNumberLabel('&laquo; 1')).toBe('1');
        expect(getPageNumberLabel('2 &raquo;')).toBe('2');
    });
});
