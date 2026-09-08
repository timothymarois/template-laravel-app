// Base UI Components (shadcn + enhanced)
// This is the single source of truth for all UI components

// Alert
export * from './alert';

// Alert Dialog
export * from './alert-dialog';

// Avatar
export * from './avatar';

// Badge
export * from './badge';

// Button
export * from './button';

// Calendar
export * from './calendar';

// Card
export * from './card';

// Checkbox
export * from './checkbox';

// Code Block (read-only display + copy)
export * from './code-block';

// Combobox (autocomplete input)
export * from './combobox';

// Data Table
export * from './data-table';

// Date Picker
export * from './date-picker';

// Dialog
export * from './dialog';

// Dropdown Menu
export * from './dropdown-menu';

// Editor (requires optional @tiptap dependencies)
// Import directly from '@/components/ui/editor' when needed
// export * from './editor';

// Form Errors
export * from './form-errors';

// Input
export * from './input';

// Label
export * from './label';

// Pagination
export * from './pagination';

// Pin Input (OTP / passcode)
export * from './pin-input';

// Popover
export * from './popover';

// Radio Group
export * from './radio-group';

// Range Calendar
export * from './range-calendar';

// Scroll Frame
export * from './scroll-frame';

// Select (primitives)
export * from './select';

// Select Popover (enhanced select)
export * from './select-popover';

// Sheet
export * from './sheet';

// Slider
export * from './slider';

// Sonner (Toast)
export * from './sonner';

// Switch
export * from './switch';

// Table
export * from './table';

// Tags Input
export * from './tags-input';

// Textarea
export * from './textarea';

// Tooltip
export * from './tooltip';

// Upload
export * from './upload';

// View Toggle (grid / list switcher)
export * from './view-toggle';

// Primitives that ship in this kit but were never exported. Reaching them by
// path worked; importing them from the barrel did not, with nothing saying why.
export * from './accordion';
export * from './collapsible';
export * from './command';
export * from './context-menu';
export * from './progress';
export * from './separator';
export * from './skeleton';
export * from './spinner';
export * from './tabs';

// Deliberately NOT exported, so the barrel stays cheap to import:
//   carousel, chart  — pull optional dependencies into the module graph
//   resizable        — same
//   sidebar          — its Sidebar clashes with components/app/navigation/Sidebar
//   editor           — optional @tiptap deps (see above)
// Import these by path when you need them.
