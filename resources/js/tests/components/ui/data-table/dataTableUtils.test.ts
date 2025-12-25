import { describe, it, expect } from 'vitest';
import {
    buildColumnSelectionState,
    filterUnselectedColumnGroups,
    getSelectionState,
    isDefaultColumnSelection,
    isItemSelected,
    resolveTableColumns,
} from '@/components/ui/data-table/dataTableUtils';

describe('dataTableUtils', () => {
    it('resolves table columns by active list order', () => {
        const columns = [
            { key: 'name', header: 'Name' },
            { key: 'email', header: 'Email' },
        ];

        const result = resolveTableColumns(columns, ['email', 'missing', 'name']);

        expect(result).toEqual([columns[1], columns[0]]);
    });

    it('builds column selection state from a list', () => {
        const columns = [
            { key: 'name', header: 'Name' },
            { key: 'email', header: 'Email' },
        ];

        const result = buildColumnSelectionState(columns, ['email']);

        expect(result.activeColumns).toEqual({
            name: false,
            email: true,
        });
        expect(result.selectedColumns).toEqual([columns[1]]);
    });

    it('filters unselected columns into groups', () => {
        const columns = [
            { key: 'name', header: 'Name', group: 'Identity' },
            { key: 'email', header: 'Email', group: 'Identity' },
            { key: 'status', header: 'Status', group: 'Meta' },
            { key: 'notes', header: 'Notes' },
        ];
        const activeColumns = {
            name: true,
            email: false,
            status: false,
            notes: false,
        };

        const result = filterUnselectedColumnGroups(columns, activeColumns, 'a');

        expect(result.Identity).toEqual([columns[1]]);
        expect(result.Meta).toEqual([columns[2]]);
        expect(result['']).toBeUndefined();
    });

    it('detects default column selection order', () => {
        const columns = [
            { key: 'name', header: 'Name' },
            { key: 'email', header: 'Email' },
        ];

        expect(isDefaultColumnSelection(['name', 'email'], columns, columns)).toBe(true);
        expect(isDefaultColumnSelection(['email', 'name'], columns, columns)).toBe(false);
        expect(isDefaultColumnSelection(['name', 'missing'], [columns[0]], columns)).toBe(true);
    });

    it('computes selection state based on selected items', () => {
        const items = [1, 2, 3];

        expect(getSelectionState(items, [], false)).toEqual({
            isAllSelected: false,
            isSomeSelected: false,
        });
        expect(getSelectionState(items, [1], false)).toEqual({
            isAllSelected: false,
            isSomeSelected: true,
        });
        expect(getSelectionState(items, [1, 2, 3], false)).toEqual({
            isAllSelected: true,
            isSomeSelected: false,
        });
        expect(getSelectionState(items, [1], true)).toEqual({
            isAllSelected: true,
            isSomeSelected: false,
        });
    });

    it('checks if an item is selected', () => {
        const selected = [{ id: 1 }, { id: 2 }];

        expect(isItemSelected({ id: 2 }, selected, 'id', false)).toBe(true);
        expect(isItemSelected({ id: 3 }, selected, 'id', false)).toBe(false);
        expect(isItemSelected({ id: 3 }, selected, 'id', true)).toBe(true);
    });
});
