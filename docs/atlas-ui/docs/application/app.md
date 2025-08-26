# App

Composes navigation, page chrome, and page content into a full application wrapper.

```ts
import { App as LayoutApp } from '@atlas/ui';
```

## Usage

```vue
<LayoutApp
  :pageUrl="route.fullPath"
  pageTitle="User"
  :sideBarItems="sideBarItems"
  :topBarItems="topBarItems"
  :pageNavItems="pageNavItems"
  :linkComponent="Link"
  widthClass="w-full"
  :isSideNav="true"
>
  <template #navLogo>
    <img src="/logo.svg" alt="Logo" />
  </template>
  <template #navActions>
    <Button label="Logout" />
  </template>
  <template #appTopBar>
    <span>Announcements</span>
  </template>
  <template #headerTitle>
    <h1>User</h1>
  </template>
  <template #headerAction>
    <Button label="Edit" />
  </template>
  <template #pageSideContent>
    <div>Tools</div>
  </template>
  <template #footer>
    <span>&copy; 2024</span>
  </template>
  <template #footerAction>
    <Button label="Save" />
  </template>
  <template #modals>
    <MyModal v-if="showModal" />
  </template>
  <template #default>
    <p>Page body</p>
  </template>
</LayoutApp>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `pageUrl` | `string` | `''` | Current page identifier. |
| `isSideNav` | `boolean` | `true` | Render side navigation when true; top bar when false. |
| `hasToast` | `boolean` | `true` | Include global `<Toast>` instance. |
| `pageTitle` | `string` | `'Home'` | Text title for the page. |
| `pageTabs` | `any[]` | `[]` | Tab definitions for the header. |
| `pageNavItems` | `any[]` | `[]` | Items for the optional `PageSideNav`. |
| `sideBarItems` | `any[]` | `[]` | Navigation sections for the side bar. |
| `topBarItems` | `any[]` | `[]` | Navigation items for the top bar. |
| `linkComponent` | `string \| object` | `'a'` | Component used for navigation links. |
| `breadcrumbs` | `any[]` | `[]` | Breadcrumb trail for the header. |
| `widthClass` | `string` | `'max-w-screen-2xl'` | Max width container class. |
| `containerClass` | `string` | `'mx-auto p-4'` | Additional classes for the main container. |
| `noScroll` | `boolean` | `false` | Disable vertical scrolling for the main content. |
| `sideBarBackgroundClass` | `string` | `''` | Optional classes for the side navigation background and border. |
| `sideBarActiveClass` | `string` | `''` | Class applied to active side navigation links. |
| `topBarBackgroundClass` | `string` | `''` | Optional classes for the top navigation background and border. |
| `topBarActiveClass` | `string` | `''` | Class applied to active top navigation links. |

### Slots
- `nav` – replace the navigation area entirely.
- `navLogo` – logo inside default navigation components.
- `navActions` – action area in navigation.
- `appTopBar` – optional bar beneath navigation.
- `headerTitle` – custom page title template.
- `headerAction` – action area in the page header.
- `pageSideContent` – content to the side of the page body.
- `footer` – footer content.
- `footerAction` – action slot in the footer.
- `modals` – render modal components outside the layout.
- `default` – main page content.

### Events
- None.
