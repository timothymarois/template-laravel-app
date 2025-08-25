# Editor

The Atlas rich text editor builds on [TipTap](https://tiptap.dev/) to provide an extensible editing experience.

## Dependencies
- `@tiptap/vue-3`
- `@tiptap/starter-kit`

## Features
- Formatting toolbar with bold, italic, strikethrough, bullet and ordered lists, link, and clear formatting options
- Headings and lists
- Code blocks and blockquotes
- Link support
- Keyboard shortcuts for common actions

## Toolbar
The toolbar exposes the following tools:

- **bold** – toggle bold text
- **italic** – toggle italic text
- **strike** – toggle strikethrough
- **bullet** – create a bulleted list
- **ordered** – create a numbered list
- **link** – prompt for a URL and apply it to the selection
- **clear** – remove formatting from the selection

Customize the toolbar by passing a `toolbarOptions` array. The default order is `['bold','italic','strike','bullet','ordered','link','clear']`.

```vue
<Editor v-model="content" :toolbar-options="['bold','italic','bullet']" />
```

Set `toolbar` to `false` to hide it completely.

## Usage
```ts
import { Editor } from '@atlas/ui';
```

```vue
<Editor v-model="content" :toolbar="true" />
```
Bind `v-model` to your data to read and write HTML content.

## Displaying Content
```ts
import { EditorContent } from '@atlas/ui';
```

```vue
<EditorContent :content="html" />
```
Use `EditorContent` to render editor output as HTML.

