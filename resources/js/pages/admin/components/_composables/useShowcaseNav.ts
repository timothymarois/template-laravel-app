import {
    Home,
    FormInput,
    MousePointerClick,
    Eye,
    Database,
    BarChart3,
} from 'lucide-vue-next';

export const useShowcaseNav = () => {
    // Hierarchical sidebar navigation with collapsible sections
    const sidebarItems = [
        {
            label: 'Overview',
            href: '/admin/components',
            icon: Home,
        },
        {
            label: 'Forms',
            icon: FormInput,
            children: [
                { label: 'Overview', href: '/admin/components/forms' },
                { label: 'Input', href: '/admin/components/forms/input' },
                { label: 'Textarea', href: '/admin/components/forms/textarea' },
                { label: 'Select', href: '/admin/components/forms/select' },
                { label: 'Checkbox', href: '/admin/components/forms/checkbox' },
                { label: 'Switch', href: '/admin/components/forms/switch' },
                { label: 'Slider', href: '/admin/components/forms/slider' },
                { label: 'Calendar', href: '/admin/components/forms/calendar' },
                { label: 'Fields', href: '/admin/components/forms/fields' },
                { label: 'Editor', href: '/admin/components/forms/editor' },
                { label: 'Upload', href: '/admin/components/forms/upload' },
            ],
        },
        {
            label: 'Actions',
            icon: MousePointerClick,
            children: [
                { label: 'Overview', href: '/admin/components/actions' },
                { label: 'Button', href: '/admin/components/actions/button' },
                { label: 'Menu', href: '/admin/components/actions/menu' },
                { label: 'Dialog', href: '/admin/components/actions/dialog' },
                { label: 'Sheet', href: '/admin/components/actions/sheet' },
            ],
        },
        {
            label: 'Display',
            icon: Eye,
            children: [
                { label: 'Overview', href: '/admin/components/display' },
                { label: 'Alert', href: '/admin/components/display/alert' },
                { label: 'Card', href: '/admin/components/display/card' },
                { label: 'Badge', href: '/admin/components/display/badge' },
                { label: 'Avatar', href: '/admin/components/display/avatar' },
                { label: 'Tooltip', href: '/admin/components/display/tooltip' },
                { label: 'Popover', href: '/admin/components/display/popover' },
                { label: 'Loading', href: '/admin/components/display/loading' },
                { label: 'Tabs', href: '/admin/components/display/tabs' },
                { label: 'Accordion', href: '/admin/components/display/accordion' },
                { label: 'Toast', href: '/admin/components/display/toast' },
            ],
        },
        {
            label: 'Data',
            icon: Database,
            children: [
                { label: 'Overview', href: '/admin/components/data' },
                { label: 'Table', href: '/admin/components/data/table' },
                { label: 'Actions', href: '/admin/components/data/actions' },
                { label: 'Pagination', href: '/admin/components/data/pagination' },
            ],
        },
        {
            label: 'Charts',
            icon: BarChart3,
            children: [
                { label: 'Overview', href: '/admin/components/charts' },
                { label: 'Bar', href: '/admin/components/charts/bar' },
                { label: 'Line', href: '/admin/components/charts/line' },
                { label: 'Area', href: '/admin/components/charts/area' },
                { label: 'Pie & Donut', href: '/admin/components/charts/pie' },
            ],
        },
    ];

    // Legacy flat navigation (kept for backwards compatibility)
    const sideNavItems = [
        { label: 'Overview', href: '/admin/components' },
        { label: 'Forms', href: '/admin/components/forms', parent: '/admin/components/forms' },
        { label: 'Actions', href: '/admin/components/actions', parent: '/admin/components/actions' },
        { label: 'Display', href: '/admin/components/display', parent: '/admin/components/display' },
        { label: 'Data', href: '/admin/components/data', parent: '/admin/components/data' },
        { label: 'Charts', href: '/admin/components/charts', parent: '/admin/components/charts' },
    ];

    // Tabs for each category - specific page URLs
    const formsTabs = [
        { title: 'Overview', href: '/admin/components/forms' },
        { title: 'Input', href: '/admin/components/forms/input' },
        { title: 'Textarea', href: '/admin/components/forms/textarea' },
        { title: 'Select', href: '/admin/components/forms/select' },
        { title: 'Checkbox', href: '/admin/components/forms/checkbox' },
        { title: 'Calendar', href: '/admin/components/forms/calendar' },
        { title: 'Fields', href: '/admin/components/forms/fields' },
        { title: 'Editor', href: '/admin/components/forms/editor' },
        { title: 'Upload', href: '/admin/components/forms/upload' },
    ];

    const actionsTabs = [
        { title: 'Overview', href: '/admin/components/actions' },
        { title: 'Button', href: '/admin/components/actions/button' },
        { title: 'Menu', href: '/admin/components/actions/menu' },
        { title: 'Dialog', href: '/admin/components/actions/dialog' },
        { title: 'Sheet', href: '/admin/components/actions/sheet' },
    ];

    const displayTabs = [
        { title: 'Overview', href: '/admin/components/display' },
        { title: 'Alert', href: '/admin/components/display/alert' },
        { title: 'Card', href: '/admin/components/display/card' },
        { title: 'Badge', href: '/admin/components/display/badge' },
        { title: 'Avatar', href: '/admin/components/display/avatar' },
        { title: 'Tooltip', href: '/admin/components/display/tooltip' },
        { title: 'Popover', href: '/admin/components/display/popover' },
        { title: 'Loading', href: '/admin/components/display/loading' },
        { title: 'Tabs', href: '/admin/components/display/tabs' },
        { title: 'Accordion', href: '/admin/components/display/accordion' },
        { title: 'Toast', href: '/admin/components/display/toast' },
    ];

    const dataTabs = [
        { title: 'Overview', href: '/admin/components/data' },
        { title: 'Table', href: '/admin/components/data/table' },
        { title: 'Actions', href: '/admin/components/data/actions' },
        { title: 'Pagination', href: '/admin/components/data/pagination' },
    ];

    const chartsTabs = [
        { title: 'Overview', href: '/admin/components/charts' },
        { title: 'Bar', href: '/admin/components/charts/bar' },
        { title: 'Line', href: '/admin/components/charts/line' },
        { title: 'Area', href: '/admin/components/charts/area' },
        { title: 'Pie & Donut', href: '/admin/components/charts/pie' },
    ];

    return {
        sidebarItems,
        sideNavItems,
        formsTabs,
        actionsTabs,
        displayTabs,
        dataTabs,
        chartsTabs,
    };
};
