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
    // Items sorted alphabetically with Overview always first
    const sidebarItems = [
        {
            label: 'Overview',
            href: '/admin/components',
            icon: Home,
        },
        {
            label: 'Actions',
            icon: MousePointerClick,
            children: [
                { label: 'Overview', href: '/admin/components/actions' },
                { label: 'Button', href: '/admin/components/actions/button' },
                { label: 'Command', href: '/admin/components/actions/command' },
                { label: 'Dialog', href: '/admin/components/actions/dialog' },
                { label: 'Menu', href: '/admin/components/actions/menu' },
                { label: 'Sheet', href: '/admin/components/actions/sheet' },
            ],
        },
        {
            label: 'Charts',
            icon: BarChart3,
            children: [
                { label: 'Overview', href: '/admin/components/charts' },
                { label: 'Area', href: '/admin/components/charts/area' },
                { label: 'Bar', href: '/admin/components/charts/bar' },
                { label: 'Line', href: '/admin/components/charts/line' },
                { label: 'Pie & Donut', href: '/admin/components/charts/pie' },
            ],
        },
        {
            label: 'Data',
            icon: Database,
            children: [
                { label: 'Overview', href: '/admin/components/data' },
                { label: 'Actions', href: '/admin/components/data/actions' },
                { label: 'Pagination', href: '/admin/components/data/pagination' },
                { label: 'Table', href: '/admin/components/data/table' },
            ],
        },
        {
            label: 'Display',
            icon: Eye,
            children: [
                { label: 'Overview', href: '/admin/components/display' },
                { label: 'Accordion', href: '/admin/components/display/accordion' },
                { label: 'Alert', href: '/admin/components/display/alert' },
                { label: 'Avatar', href: '/admin/components/display/avatar' },
                { label: 'Badge', href: '/admin/components/display/badge' },
                { label: 'Card', href: '/admin/components/display/card' },
                { label: 'Carousel', href: '/admin/components/display/carousel' },
                { label: 'Code Block', href: '/admin/components/display/code-block' },
                { label: 'Loading', href: '/admin/components/display/loading' },
                { label: 'Popover', href: '/admin/components/display/popover' },
                { label: 'Resizable', href: '/admin/components/display/resizable' },
                { label: 'Tabs', href: '/admin/components/display/tabs' },
                { label: 'Toast', href: '/admin/components/display/toast' },
                { label: 'Tooltip', href: '/admin/components/display/tooltip' },
                { label: 'View Toggle', href: '/admin/components/display/view-toggle' },
            ],
        },
        {
            label: 'Forms',
            icon: FormInput,
            children: [
                { label: 'Overview', href: '/admin/components/forms' },
                { label: 'Calendar', href: '/admin/components/forms/calendar/date-input', parent: '/admin/components/forms/calendar' },
                { label: 'Checkbox', href: '/admin/components/forms/checkbox' },
                { label: 'Combobox', href: '/admin/components/forms/combobox' },
                { label: 'Editor', href: '/admin/components/forms/editor' },
                { label: 'Fields', href: '/admin/components/forms/fields' },
                { label: 'Input', href: '/admin/components/forms/input', parent: '/admin/components/forms/input' },
                { label: 'Pin Input', href: '/admin/components/forms/pin-input' },
                { label: 'Select', href: '/admin/components/forms/select' },
                { label: 'Slider', href: '/admin/components/forms/slider' },
                { label: 'Switch', href: '/admin/components/forms/switch' },
                { label: 'Textarea', href: '/admin/components/forms/textarea' },
                { label: 'Upload', href: '/admin/components/forms/upload' },
            ],
        },
    ];

    // Legacy flat navigation (kept for backwards compatibility)
    const sideNavItems = [
        { label: 'Overview', href: '/admin/components' },
        { label: 'Actions', href: '/admin/components/actions', parent: '/admin/components/actions' },
        { label: 'Charts', href: '/admin/components/charts', parent: '/admin/components/charts' },
        { label: 'Data', href: '/admin/components/data', parent: '/admin/components/data' },
        { label: 'Display', href: '/admin/components/display', parent: '/admin/components/display' },
        { label: 'Forms', href: '/admin/components/forms', parent: '/admin/components/forms' },
    ];

    // Tabs for each category - sorted alphabetically with Overview first
    const actionsTabs = [
        { title: 'Overview', href: '/admin/components/actions' },
        { title: 'Button', href: '/admin/components/actions/button' },
        { title: 'Command', href: '/admin/components/actions/command' },
        { title: 'Dialog', href: '/admin/components/actions/dialog' },
        { title: 'Menu', href: '/admin/components/actions/menu' },
        { title: 'Sheet', href: '/admin/components/actions/sheet' },
    ];

    const chartsTabs = [
        { title: 'Overview', href: '/admin/components/charts' },
        { title: 'Area', href: '/admin/components/charts/area' },
        { title: 'Bar', href: '/admin/components/charts/bar' },
        { title: 'Line', href: '/admin/components/charts/line' },
        { title: 'Pie & Donut', href: '/admin/components/charts/pie' },
    ];

    const dataTabs = [
        { title: 'Overview', href: '/admin/components/data' },
        { title: 'Actions', href: '/admin/components/data/actions' },
        { title: 'Pagination', href: '/admin/components/data/pagination' },
        { title: 'Table', href: '/admin/components/data/table' },
    ];

    const displayTabs = [
        { title: 'Overview', href: '/admin/components/display' },
        { title: 'Accordion', href: '/admin/components/display/accordion' },
        { title: 'Alert', href: '/admin/components/display/alert' },
        { title: 'Avatar', href: '/admin/components/display/avatar' },
        { title: 'Badge', href: '/admin/components/display/badge' },
        { title: 'Card', href: '/admin/components/display/card' },
        { title: 'Carousel', href: '/admin/components/display/carousel' },
        { title: 'Code Block', href: '/admin/components/display/code-block' },
        { title: 'Loading', href: '/admin/components/display/loading' },
        { title: 'Popover', href: '/admin/components/display/popover' },
        { title: 'Resizable', href: '/admin/components/display/resizable' },
        { title: 'Tabs', href: '/admin/components/display/tabs' },
        { title: 'Toast', href: '/admin/components/display/toast' },
        { title: 'Tooltip', href: '/admin/components/display/tooltip' },
        { title: 'View Toggle', href: '/admin/components/display/view-toggle' },
    ];

    const formsTabs = [
        { title: 'Overview', href: '/admin/components/forms' },
        { title: 'Calendar', href: '/admin/components/forms/calendar/date-input', parent: '/admin/components/forms/calendar' },
        { title: 'Checkbox', href: '/admin/components/forms/checkbox' },
        { title: 'Combobox', href: '/admin/components/forms/combobox' },
        { title: 'Editor', href: '/admin/components/forms/editor' },
        { title: 'Fields', href: '/admin/components/forms/fields' },
        { title: 'Input', href: '/admin/components/forms/input', parent: '/admin/components/forms/input' },
        { title: 'Pin Input', href: '/admin/components/forms/pin-input' },
        { title: 'Select', href: '/admin/components/forms/select' },
        { title: 'Slider', href: '/admin/components/forms/slider' },
        { title: 'Switch', href: '/admin/components/forms/switch' },
        { title: 'Textarea', href: '/admin/components/forms/textarea' },
        { title: 'Upload', href: '/admin/components/forms/upload' },
    ];

    return {
        sidebarItems,
        sideNavItems,
        actionsTabs,
        chartsTabs,
        dataTabs,
        displayTabs,
        formsTabs,
    };
};
