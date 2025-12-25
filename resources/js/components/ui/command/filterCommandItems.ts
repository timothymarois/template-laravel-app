/**
 * Filters command items and groups based on search and filter settings.
 *
 * Extracted as a pure helper so filtering logic can be unit-tested without
 * mounting UI components.
 */
export interface CommandFilterState {
    count: number;
    items: Map<string, number>;
    groups: Set<string>;
}

export interface CommandFilterParams {
    allItems: Map<string, string>;
    allGroups: Map<string, Set<string>>;
    search: string;
    disableFilter: boolean;
    contains: (value: string, search: string) => boolean;
    previous?: {
        items: Map<string, number>;
        groups: Set<string>;
    };
}

export const filterCommandItems = ({
    allItems,
    allGroups,
    search,
    disableFilter,
    contains,
    previous,
}: CommandFilterParams): CommandFilterState => {
    const previousItems = previous?.items ?? new Map<string, number>();
    const previousGroups = previous?.groups ?? new Set<string>();

    if (disableFilter) {
        const items = new Map(previousItems);
        const groups = new Set(previousGroups);

        for (const [id] of allItems) {
            items.set(id, 1);
        }

        for (const [groupId] of allGroups) {
            groups.add(groupId);
        }

        return {
            count: allItems.size,
            items,
            groups,
        };
    }

    if (!search) {
        return {
            count: allItems.size,
            items: previousItems,
            groups: previousGroups,
        };
    }

    const items = new Map(previousItems);
    const groups = new Set<string>();
    let itemCount = 0;

    for (const [id, value] of allItems) {
        const score = contains(value, search);
        items.set(id, score ? 1 : 0);
        if (score) itemCount++;
    }

    for (const [groupId, group] of allGroups) {
        for (const itemId of group) {
            if ((items.get(itemId) ?? 0) > 0) {
                groups.add(groupId);
                break;
            }
        }
    }

    return {
        count: itemCount,
        items,
        groups,
    };
};
