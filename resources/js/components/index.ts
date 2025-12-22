// Re-export all component layers
export * from './ui';
export * from './composed';
export * from './admin';
export * from './web';

// Legacy aliases for backwards compatibility during migration
// These map old names to new locations - remove once all imports are updated

// From composed/button
export { Button } from './composed/button';
export { ButtonMenu } from './composed/button';

// From composed/dialog
export { Dialog } from './composed/dialog';
export { DialogConfirmation } from './composed/dialog';

// From composed/drawer
export { Drawer } from './composed/drawer';
export { DrawerForm } from './composed/drawer';

// From composed/card
export { Card } from './composed/card';

// From composed/form
export { Input as InputText } from './composed/form';
export { Select } from './composed/form';
export { Checkbox } from './composed/form';
export { LabelField } from './composed/form';
export { Errors } from './composed/form';

// From composed/display
export { Avatar } from './composed/display';
export { Badge } from './composed/display';
export { TooltipIcon } from './composed/display';

// From composed/overlay
export { Menu } from './composed/overlay';
export { Popover } from './composed/overlay';

// From composed/data
export { DataTable } from './composed/data';
export { DataTable as Table } from './composed/data';
export { TableActions } from './composed/data';
export { CustomizeColumns as TableCustomizeColumns } from './composed/data';
export { Paginator } from './composed/data';

// From composed/layout
export { ScrollFrame } from './composed/layout';

// Editor requires optional dependencies (@tiptap/vue-3, etc.)
// Import directly from '@/components/composed/editor' when needed
// export { Editor } from './composed/editor';

// From admin/layout
export { AppShell as App } from './admin/layout';

// From admin/navigation
export { Sidebar as NavSidebar } from './admin/navigation';
export { Topbar as NavTopbar } from './admin/navigation';
export { ProfileMenu } from './admin/navigation';

// From admin/page
export { Header as PageHeader } from './admin/page';
export { Footer as PageFooter } from './admin/page';
export { Content as PageContent } from './admin/page';
export { SideNav as PageSideNav } from './admin/page';
export { SideContent as PageSideContent } from './admin/page';

// From admin/layout (Topbar moved to admin)
export { AppTopbar as Topbar } from './admin/layout';

// Toast from ui
export { Toaster as Toast } from './ui/sonner';

// RadioButton from ui
export { RadioGroupItem as RadioButton } from './ui/radio-group';
