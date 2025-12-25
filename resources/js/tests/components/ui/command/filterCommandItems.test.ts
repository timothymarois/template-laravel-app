import { describe, it, expect } from 'vitest';
import { filterCommandItems } from '@/components/ui/command/filterCommandItems';

const contains = (value: string, search: string) => {
    return value.toLowerCase().includes(search.toLowerCase());
};

describe('filterCommandItems', () => {
    it('marks all items and groups when filtering is disabled', () => {
        const allItems = new Map([
            ['a', 'Apple'],
            ['b', 'Banana'],
        ]);
        const allGroups = new Map([
            ['group-1', new Set(['a'])],
            ['group-2', new Set(['b'])],
        ]);
        const previousItems = new Map([['stale', 0]]);
        const previousGroups = new Set(['stale-group']);

        const result = filterCommandItems({
            allItems,
            allGroups,
            search: 'ap',
            disableFilter: true,
            contains,
            previous: {
                items: previousItems,
                groups: previousGroups,
            },
        });

        expect(result.count).toBe(2);
        expect(result.items.get('a')).toBe(1);
        expect(result.items.get('b')).toBe(1);
        expect(result.groups.has('group-1')).toBe(true);
        expect(result.groups.has('group-2')).toBe(true);
        expect(result.items.has('stale')).toBe(true);
        expect(result.groups.has('stale-group')).toBe(true);
    });

    it('returns previous state when search is empty', () => {
        const allItems = new Map([['a', 'Apple']]);
        const allGroups = new Map([['group-1', new Set(['a'])]]);
        const previousItems = new Map([['a', 1]]);
        const previousGroups = new Set(['group-1']);

        const result = filterCommandItems({
            allItems,
            allGroups,
            search: '',
            disableFilter: false,
            contains,
            previous: {
                items: previousItems,
                groups: previousGroups,
            },
        });

        expect(result.count).toBe(1);
        expect(result.items).toBe(previousItems);
        expect(result.groups).toBe(previousGroups);
    });

    it('filters items and groups based on search', () => {
        const allItems = new Map([
            ['a', 'Apple'],
            ['b', 'Banana'],
            ['c', 'Apricot'],
        ]);
        const allGroups = new Map([
            ['group-1', new Set(['a', 'b'])],
            ['group-2', new Set(['c'])],
        ]);

        const result = filterCommandItems({
            allItems,
            allGroups,
            search: 'ap',
            disableFilter: false,
            contains,
        });

        expect(result.count).toBe(2);
        expect(result.items.get('a')).toBe(1);
        expect(result.items.get('b')).toBe(0);
        expect(result.items.get('c')).toBe(1);
        expect(result.groups.has('group-1')).toBe(true);
        expect(result.groups.has('group-2')).toBe(true);
    });

    it('returns empty results when no items match', () => {
        const allItems = new Map([
            ['a', 'Apple'],
            ['b', 'Banana'],
        ]);
        const allGroups = new Map([
            ['group-1', new Set(['a'])],
            ['group-2', new Set(['b'])],
        ]);

        const result = filterCommandItems({
            allItems,
            allGroups,
            search: 'zzz',
            disableFilter: false,
            contains,
        });

        expect(result.count).toBe(0);
        expect(result.items.get('a')).toBe(0);
        expect(result.items.get('b')).toBe(0);
        expect(result.groups.size).toBe(0);
    });
});
