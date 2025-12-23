export const useShowcaseNav = () => {
    // Side navigation - main categories
    const sideNavItems = [
        { label: 'Overview', href: '/admin/components' },
        { label: 'Forms', href: '/admin/components/forms', parent: '/admin/components/forms' },
        { label: 'Actions', href: '/admin/components/actions', parent: '/admin/components/actions' },
        { label: 'Display', href: '/admin/components/display', parent: '/admin/components/display' },
        { label: 'Data', href: '/admin/components/data', parent: '/admin/components/data' },
    ];

    // Tabs for each category - specific page URLs
    const formsTabs = [
        { title: 'Overview', href: '/admin/components/forms' },
        { title: 'Input', href: '/admin/components/forms/input' },
        { title: 'Select', href: '/admin/components/forms/select' },
        { title: 'Checkbox & Radio', href: '/admin/components/forms/checkbox' },
        { title: 'Fields', href: '/admin/components/forms/fields' },
    ];

    const actionsTabs = [
        { title: 'Overview', href: '/admin/components/actions' },
        { title: 'Button', href: '/admin/components/actions/button' },
        { title: 'Menu', href: '/admin/components/actions/menu' },
        { title: 'Dialog', href: '/admin/components/actions/dialog' },
    ];

    const displayTabs = [
        { title: 'Overview', href: '/admin/components/display' },
        { title: 'Alert', href: '/admin/components/display/alert' },
        { title: 'Card', href: '/admin/components/display/card' },
        { title: 'Badge', href: '/admin/components/display/badge' },
        { title: 'Tooltip', href: '/admin/components/display/tooltip' },
    ];

    const dataTabs = [
        { title: 'Overview', href: '/admin/components/data' },
        { title: 'Table', href: '/admin/components/data/table' },
        { title: 'Actions', href: '/admin/components/data/actions' },
        { title: 'Pagination', href: '/admin/components/data/pagination' },
    ];

    return {
        sideNavItems,
        formsTabs,
        actionsTabs,
        displayTabs,
        dataTabs,
    };
};
