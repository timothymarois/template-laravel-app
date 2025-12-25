/**
 * Utilities for parsing and interpreting pagination links.
 *
 * Extracted as a small module so helpers are pure, reusable, and easy to unit-test.
 */
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export const getPaginationLinkComponent = (
    link: PaginationLink,
    linkComponent: string | object
): string | object => {
    if (!link.url) return 'span';
    return linkComponent;
};

export const isPreviousLink = (link: PaginationLink, index: number): boolean => {
    return index === 0 && (
        link.label.toLowerCase().includes('previous') ||
        link.label.includes('&laquo;')
    );
};

export const isNextLink = (link: PaginationLink, index: number, totalLinks: number): boolean => {
    return index === totalLinks - 1 && (
        link.label.toLowerCase().includes('next') ||
        link.label.includes('&raquo;')
    );
};

export const isEllipsisLabel = (label: string): boolean => {
    return label === '...' || label.includes('&hellip;');
};

export const getPageNumberLabel = (label: string): string => {
    return label.replace(/&[^;]+;/g, '').trim();
};
