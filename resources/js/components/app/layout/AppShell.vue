<template>
    <TooltipProvider :delay-duration="0">
        <div
            class="min-h-screen w-full bg-muted"
            :class="{
                'relative h-screen flex w-full overflow-hidden': isSideNav === true
            }"
        >
            <div>
                <slot name="nav">
                    <NavSidebar
                        v-if="isSideNav"
                        ref="sideNavRef"
                        :items="sideBarItems"
                        :linkComponent="linkComponent"
                        :backgroundClass="sideBarBackgroundClass"
                        :activeClass="sideBarActiveClass"
                    >
                        <template #logo>
                            <slot name="navLogo" />
                        </template>
                        <template #actions>
                            <slot name="navActions" />
                        </template>
                    </NavSidebar>
                    <NavTopbar v-else
                               :items="topBarItems"
                               :linkComponent="linkComponent"
                               :widthClass="widthClass"
                               :backgroundClass="topBarBackgroundClass"
                               :activeClass="topBarActiveClass"
                    >
                        <template #logo>
                            <slot name="navLogo" />
                        </template>
                        <template #actions>
                            <slot name="navActions" />
                        </template>
                    </NavTopbar>
                </slot>
            </div>
            <div class="flex-1 min-w-0 flex flex-col">
                <AppTopbar v-if="hasAppTopBar">
                    <template #default>
                        <slot name="appTopBar" />
                    </template>
                </AppTopbar>
                <div class="flex flex-1 overflow-hidden">
                    <PageSidebar
                        v-if="pageSidebarItems?.length"
                        ref="pageSideNavRef"
                        :items="pageSidebarItems"
                        :title="pageSidebarTitle"
                        :linkComponent="linkComponent"
                    />
                    <PageSideNav
                        v-else-if="pageNavItems?.length"
                        ref="pageSideNavRef"
                        :items="pageNavItems"
                        :linkComponent="linkComponent"
                    />
                    <div class="flex-1 min-w-0">
                        <PageHeader
                            v-if="hasPageHeader"
                            :title="resolvedPageTitle"
                            :tabs="pageTabs"
                            :linkComponent="linkComponent"
                            :breadcrumbs="breadcrumbs"
                            :widthClass="widthClass"
                        >
                            <template #title>
                                <slot name="headerTitle" />
                            </template>
                            <template #action>
                                <slot name="headerAction" />
                            </template>
                        </PageHeader>
                        <div class="w-full flex h-screen overflow-hidden">
                            <div
                                v-if="hasPageSideContent"
                                ref="sideContentRef"
                                class="flex-none border-r h-full bg-card min-w-64 shadow-sm"
                            >
                                <PageSideContent>
                                    <template #default>
                                        <slot name="pageSideContent" />
                                    </template>
                                </PageSideContent>
                            </div>
                            <div class="flex-grow min-w-0">
                                <PageContent
                                    :footerHeight="footerHeight"
                                    :containerClass="containerClass"
                                    :widthClass="widthClass"
                                    :rootClass="noScroll ? 'overflow-hidden' : 'overflow-y-auto'"
                                    :scrollable="!noScroll"
                                >
                                    <template #side>
                                        <slot name="pageSideContent" />
                                    </template>
                                    <template #default>
                                        <slot />
                                    </template>
                                </PageContent>
                                <PageFooter
                                    v-if="hasPageFooter"
                                    ref="footerRef"
                                    :leftOffset="footerLeftOffset"
                                    :widthClass="widthClass"
                                >
                                    <template #default>
                                        <slot name="footer" />
                                    </template>
                                    <template #action>
                                        <slot name="footerAction" />
                                    </template>
                                </PageFooter>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Toaster v-if="hasToast" :position="toastPosition" :close-button="toastCloseButton" />
        </div>
        <slot name="modals" />
    </TooltipProvider>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick, useSlots, watch, provide, defineAsyncComponent } from 'vue';
import { hasSlotContent } from '@/utils';
import PageHeader from '../page/Header.vue';
import PageFooter from '../page/Footer.vue';
import PageContent from '../page/Content.vue';
import PageSideNav from '../page/SideNav.vue';
import PageSidebar from '../page/PageSidebar.vue';
import PageSideContent from '../page/SideContent.vue';
import NavSidebar from '../navigation/Sidebar.vue';
import AppTopbar from './AppTopbar.vue';
import { TooltipProvider } from '@/components/ui/tooltip';

// Lazy load components that aren't needed on initial render
const Toaster = defineAsyncComponent(() => import('@/components/ui/sonner/Sonner.vue'));
const NavTopbar = defineAsyncComponent(() => import('../navigation/Topbar.vue'));

const props = defineProps({
    pageUrl: {
        type: String,
        default: undefined,
    },
    isSideNav: {
        type: Boolean,
        default: true,
    },
    hasToast: {
        type: Boolean,
        default: true,
    },
    toastPosition: {
        type: String,
        default: 'bottom-left',
    },
    toastCloseButton: {
        type: Boolean,
        default: true,
    },
    title: {
        type: String,
        default: undefined,
    },
    pageTitle: {
        type: String,
        default: 'Home',
    },
    pageTabs: {
        type: Array,
        default: () => [],
    },
    pageNavItems: {
        type: Array,
        default: () => [],
    },
    pageSidebarItems: {
        type: Array,
        default: () => [],
    },
    pageSidebarTitle: {
        type: String,
        default: '',
    },
    sideBarItems: {
        type: Array,
        default: () => [],
    },
    topBarItems: {
        type: Array,
        default: () => [],
    },
    linkComponent: {
        type: [String, Object],
        default: 'a',
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    widthClass: {
        type: String,
        default: 'max-w-screen-2xl',
    },
    containerClass: {
        type: String,
        default: 'mx-auto p-4',
    },
    noScroll: {
        type: Boolean,
        default: false,
    },
    sideBarBackgroundClass: {
        type: String,
        default: '',
    },
    sideBarActiveClass: {
        type: String,
        default: '',
    },
    topBarBackgroundClass: {
        type: String,
        default: '',
    },
    topBarActiveClass: {
        type: String,
        default: '',
    },
});

const slots = useSlots();

const sideNavRef = ref(null);
const pageSideNavRef = ref(null);
const footerRef = ref(null);
const sideContentRef = ref(null);

let resizeObserver = null;

const footerHeight = ref(0);
const footerLeftOffset = ref(0);
const isScrolledToBottom = ref(false);

// Provide footer height for child components (e.g., tables that need to account for fixed footer)
provide('layoutFooterHeight', footerHeight);
// Provide scroll state and setter for child components to update
provide('isScrolledToBottom', isScrolledToBottom);
provide('setScrolledToBottom', (value) => {
    isScrolledToBottom.value = value;
});

const calculateFooterMetrics = () => {
    const sideNavWidth = sideNavRef.value?.$el?.offsetWidth || 0;
    const pageSideNavWidth = pageSideNavRef.value?.$el?.offsetWidth || 0;
    const sideContentWidth = sideContentRef.value?.offsetWidth || 0;
    const footerEl = footerRef.value?.$el;

    footerLeftOffset.value = sideNavWidth + pageSideNavWidth + sideContentWidth;
    footerHeight.value = footerEl?.offsetHeight || 0;
};

const hasAppTopBar = computed(() => hasSlotContent(slots.appTopBar));
const hasPageSideContent = computed(() => hasSlotContent(slots.pageSideContent));

const resolvedPageTitle = computed(() => props.title ?? props.pageTitle);

const hasPageHeader = computed(() =>
    !!resolvedPageTitle.value || (Array.isArray(props.pageTabs) && props.pageTabs.length > 0) || hasSlotContent(slots.headerAction)
);

const hasPageFooter = computed(() => hasSlotContent(slots.footer) || hasSlotContent(slots.footerAction));
const observeElements = () => {
    if (typeof window === 'undefined' || typeof ResizeObserver === 'undefined') return;

    resizeObserver?.disconnect();
    resizeObserver = new ResizeObserver(() => {
        calculateFooterMetrics();
    });

    const sideNavEl = sideNavRef.value?.$el;
    const pageSideNavEl = pageSideNavRef.value?.$el;
    const sideContentEl = sideContentRef.value;

    [sideNavEl, pageSideNavEl, sideContentEl].forEach((el) => {
        if (el instanceof Element) {
            resizeObserver.observe(el);
        }
    });
};

onMounted(() => {
    if (typeof window === 'undefined') return;

    nextTick(() => {
        calculateFooterMetrics();
        observeElements();
    });
});

watch(
    () => props.isSideNav,
    () => {
        if (typeof window === 'undefined') return;
        nextTick(() => {
            calculateFooterMetrics();
            observeElements();
        });
    }
);

watch(
    hasPageSideContent,
    () => {
        if (typeof window === 'undefined') return;
        nextTick(() => {
            calculateFooterMetrics();
            observeElements();
        });
    }
);

onBeforeUnmount(() => {
    resizeObserver?.disconnect();
});
</script>
