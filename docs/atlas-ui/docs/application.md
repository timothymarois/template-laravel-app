# Application Layout

Composes navigation, page chrome, and page content into a full application wrapper.

## Table of Contents

- App Navigation
  - [NavTopbar](application/nav-topbar.md)
  - [NavSidebar](application/nav-sidebar.md)
  - [ProfileMenu](application/profile-menu.md)
- App Layout
  - [App](#app)
  - [AppTopbar](application/app-topbar.md)
- Page Navigation
  - [PageSideNav](application/page-side-nav.md)
- Page Layout
  - [PageHeader](application/page-header.md)
  - [PageSideContent](application/page-side-content.md)
  - [PageContent](application/page-content.md)
  - [PageFooter](application/page-footer.md)

## App

### Example
```ts
import { App as LayoutApp } from '@atlas/ui';
```

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

### Props
- `pageUrl: string` – current page identifier.
- `isSideNav: boolean` – render side navigation when true; top bar when false. Default `true`.
- `hasToast: boolean` – include global `<Toast>` instance. Default `true`.
- `pageTitle: string` – text title for the page. Default `'Home'`.
- `pageTabs: any[]` – tab definitions for the header. Default `[]`.
- `pageNavItems: any[]` – items for the optional `PageSideNav`. Default `[]`.
- `sideBarItems: any[]` – navigation sections for the side bar. Default `[]`.
- `topBarItems: any[]` – navigation items for the top bar. Default `[]`.
- `linkComponent: string | object` – component used for navigation links. Default `'a'`.
- `breadcrumbs: any[]` – breadcrumb trail for the header. Default `[]`.
- `widthClass: string` – max width container class. Default `'max-w-screen-2xl'`.
- `containerClass: string` – additional classes for the main container. Default `'mx-auto p-4'`.
- `noScroll: boolean` – disable vertical scrolling for the main content. Default `false`.
- `sideBarBackgroundClass: string` – optional classes for the side navigation background and border. Default `''`.
- `sideBarActiveClass: string` – class applied to active side navigation links. Default `''`.
- `topBarBackgroundClass: string` – optional classes for the top navigation background and border. Default `''`.
- `topBarActiveClass: string` – class applied to active top navigation links. Default `''`.

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
- None
