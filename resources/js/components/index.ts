// Re-export all component layers
export * from './ui';
export * from './app';
export * from './site';

// Legacy aliases for backwards compatibility
// These map old names to new locations - remove once all imports are updated

// From ui/button
export { Button } from './ui/button';
export { ButtonMenu } from './ui/button';

// From ui/dialog
export { Dialog } from './ui/dialog';
export { DialogConfirmation } from './ui/dialog';

// From ui/sheet
export { SheetForm } from './ui/sheet';

// From ui/card
export { Card } from './ui/card';

// From ui/form
export { Input as InputText } from './ui/form';
export { Select } from './ui/form';
export { Checkbox } from './ui/form';
export { LabelField } from './ui/form';
export { Errors } from './ui/form';

// From ui/avatar
export { Avatar } from './ui/avatar';

// From ui/badge
export { Badge } from './ui/badge';

// From ui/tooltip
export { TooltipIcon } from './ui/tooltip';

// From ui/dropdown-menu
export { Menu } from './ui/dropdown-menu';

// From ui/popover
export { Popover } from './ui/popover';

// From ui/data-table
export { DataTable } from './ui/data-table';
export { DataTable as Table } from './ui/data-table';
export { TableActions } from './ui/data-table';
export { CustomizeColumns as TableCustomizeColumns } from './ui/data-table';
export { Paginator } from './ui/data-table';

// From ui/scroll-frame
export { ScrollFrame } from './ui/scroll-frame';

// Editor requires optional dependencies (@tiptap/vue-3, etc.)
// Import directly from '@/components/ui/editor' when needed
// export { Editor } from './ui/editor';

// From app/layout
export { AppShell as App } from './app/layout';

// From app/navigation
export { Sidebar as NavSidebar } from './app/navigation';
export { Topbar as NavTopbar } from './app/navigation';
export { ProfileMenu } from './app/navigation';

// From app/page
export { Header as PageHeader } from './app/page';
export { Footer as PageFooter } from './app/page';
export { Content as PageContent } from './app/page';
export { SideNav as PageSideNav } from './app/page';
export { SideContent as PageSideContent } from './app/page';

// From app/layout (Topbar moved to app)
export { AppTopbar as Topbar } from './app/layout';

// Toast from ui
export { Toaster as Toast } from './ui/sonner';

// RadioButton from ui
export { RadioGroupItem as RadioButton } from './ui/radio-group';
