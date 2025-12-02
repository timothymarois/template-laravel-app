<template>
    <div
        class="min-h-screen w-full bg-surface-100 dark:bg-surface-900"
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
                <PageSideNav
                    v-if="pageNavItems?.length"
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
                            class="flex-none border-r border-gray-300 h-full bg-white dark:bg-surface-800 dark:border-surface-700 min-w-64 shadow-sm z-[99]"
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
        <Toast v-if="hasToast" position="bottom-left" />
    </div>
    <slot name="modals" />
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick, useSlots, watch } from 'vue';
import { hasSlotContent } from '../../utils';
import PageHeader from './Page/Header.vue';
import PageFooter from './Page/Footer.vue';
import PageContent from './Page/Content.vue';
import PageSideNav from './Page/SideNav.vue';
import PageSideContent from './Page/SideContent.vue';
import NavSidebar from './Nav/Sidebar.vue';
import NavTopbar from './Nav/Topbar.vue';
import AppTopbar from './Topbar.vue';
import Toast from '../Toast.vue';

interface Props {
    pageUrl?: string;
    isSideNav?: boolean;
    hasToast?: boolean;
    title?: string;
    pageTitle?: string;
    pageTabs?: any[];
    pageNavItems?: any[];
    sideBarItems?: any[];
    topBarItems?: any[];
    linkComponent?: string | object;
    breadcrumbs?: any[];
    widthClass?: string;
    containerClass?: string;
    noScroll?: boolean;
    sideBarBackgroundClass?: string;
    sideBarActiveClass?: string;
    topBarBackgroundClass?: string;
    topBarActiveClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    isSideNav: true,
    hasToast: true,
    pageTitle: 'Home',
    pageTabs: () => [],
    pageNavItems: () => [],
    sideBarItems: () => [],
    topBarItems: () => [],
    linkComponent: 'a',
    breadcrumbs: () => [],
    widthClass: 'max-w-screen-2xl',
    containerClass: 'mx-auto p-4',
    noScroll: false,
    sideBarBackgroundClass: '',
    sideBarActiveClass: '',
    topBarBackgroundClass: '',
    topBarActiveClass: '',
});

const slots = useSlots();

const sideNavRef = ref(null);
const pageSideNavRef = ref(null);
const footerRef = ref(null);
const sideContentRef = ref<HTMLElement | null>(null);

let resizeObserver: ResizeObserver | null = null;

const footerHeight = ref(0);
const footerLeftOffset = ref(0);

const calculateFooterMetrics = () => {
    const sideNavWidth = (sideNavRef.value as any)?.$el?.offsetWidth || 0;
    const pageSideNavWidth = (pageSideNavRef.value as any)?.$el?.offsetWidth || 0;
    const sideContentWidth = sideContentRef.value?.offsetWidth || 0;
    const footerEl = (footerRef.value as any)?.$el;

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

    const sideNavEl = (sideNavRef.value as any)?.$el;
    const pageSideNavEl = (pageSideNavRef.value as any)?.$el;
    const sideContentEl = sideContentRef.value;

    [sideNavEl, pageSideNavEl, sideContentEl].forEach((el) => {
        if (el) {
            resizeObserver!.observe(el);
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
