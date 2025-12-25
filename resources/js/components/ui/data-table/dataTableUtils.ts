/**
 * Utilities for data-table selection and column state management.
 *
 * Extracted as pure helpers so logic can be unit-tested without mounting UI components.
 */
export interface ColumnDefinition {
    key: string;
    header?: string;
    group?: string;
}

export const resolveTableColumns = <T extends { key: string }>(
    columns: T[],
    activeColumnList: string[]
): T[] => {
    return activeColumnList
        .map(key => columns.find(col => col.key === key))
        .filter(Boolean) as T[];
};

export const buildColumnSelectionState = <T extends { key: string }>(
    columns: T[],
    columnsList: string[]
): {
    activeColumns: Record<string, boolean>;
    selectedColumns: T[];
} => {
    const activeColumns: Record<string, boolean> = {};
    const selectedColumns: T[] = [];

    for (const column of columns) {
        activeColumns[column.key] = columnsList.includes(column.key);
    }

    for (const key of columnsList) {
        const column = columns.find(col => col.key === key);
        if (column) selectedColumns.push(column);
    }

    return { activeColumns, selectedColumns };
};

export const filterUnselectedColumnGroups = <T extends { key: string; header: string; group?: string }>(
    columns: T[],
    activeColumns: Record<string, boolean>,
    searchQuery: string
): Record<string, T[]> => {
    const query = searchQuery.toLowerCase();
    const list = columns.filter(col =>
        !activeColumns[col.key] &&
        col.header.toLowerCase().includes(query)
    );

    return list.reduce((groups: Record<string, T[]>, column) => {
        const group = column.group || '';
        (groups[group] ||= []).push(column);
        return groups;
    }, {});
};

export const isDefaultColumnSelection = <T extends { key: string }>(
    defaultColumns: string[],
    selectedColumns: T[],
    columns: T[]
): boolean => {
    const defaultKeys = defaultColumns.filter(key =>
        columns.some(col => col.key === key)
    );

    return defaultKeys.length === selectedColumns.length &&
        defaultKeys.every((key, index) => selectedColumns[index]?.key === key);
};

export interface SelectionState {
    isAllSelected: boolean;
    isSomeSelected: boolean;
}

export const getSelectionState = <T>(
    items: T[],
    selected: T[] | undefined,
    selectAll: boolean
): SelectionState => {
    if (selectAll) {
        return { isAllSelected: true, isSomeSelected: false };
    }

    if (!Array.isArray(selected) || selected.length === 0) {
        return { isAllSelected: false, isSomeSelected: false };
    }

    return {
        isAllSelected: selected.length === items.length,
        isSomeSelected: selected.length > 0 && selected.length < items.length,
    };
};

export const isItemSelected = <T extends Record<string, any>>(
    item: T,
    selected: T[] | undefined,
    dataKey: string,
    selectAll: boolean
): boolean => {
    if (selectAll) return true;
    if (!Array.isArray(selected) || selected.length === 0) return false;
    return selected.some(selectedItem => selectedItem[dataKey] === item[dataKey]);
};
