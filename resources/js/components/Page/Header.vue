<template>
    <div class="w-full page-header bg-white dark:bg-surface-700 border-b border-surface-300 dark:border-surface-600 z-20 sticky top-0 shadow-sm" :class="{
        '!shadow-md dark:!shadow-lg': !isTop
    }">
        <div class="flex justify-center w-full">
            <div class="w-full max-w-screen-2xl">
                <div class="flex justify-center items-center w-full px-4">
                    <div class="grow">
                        <div v-if="!props.hideTitle" class="flex items-center">
                            <div class="mr-auto text-xl m-0 p-0 flex items-center">
                                <ul class="flex items-center py-4">
                                    <li v-for="nav in props.breadcrumbs" :key="nav.href">
                                        <div class="flex items-center text-slate-700">
                                            <Link class="hover:underline" :href="nav.href">{{ nav.title }}</Link>
                                            <svg aria-hidden="true" class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </li>
                                    <li class="font-semibold">{{ props.title }}</li>
                                </ul>
                                <div v-if="slots.badge" class="ml-4"><slot name="badge" /></div>
                            </div>
                        </div>
                        <div v-if="props.tabs && props.tabs.length>0" class="flex flex-wrap -mb-px" :class="{'pt-2':props.hideTitle}">
                            <div class="grow">
                                <ul class="flex flex-wrap -mb-px">
                                    <li v-for="tab in props.tabs" :key="tab.href" class="mr-2">
                                        <Link :href="tab.href" style="position: relative;bottom: 1px;" class="text-base text-gray-500 inline-block py-2 px-4 border-b-4 border-transparent rounded-t-lg hover:text-gray-600 hover:border-surface-300 dark:hover:border-surface-400 dark:hover:text-gray-300 dark:text-gray-300" :class="{'text-gray-800 !border-surface-500 dark:!border-surface-300 dark:!text-gray-100' : $page.url == tab.href }">{{  tab.title }}</Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-if="slots.actions" class="py-4">
                        <slot name="actions" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const slots = useSlots()
const props = defineProps({
    breadcrumbs: Object,
    tabs: Array,
    title: String,
    hideTitle: Boolean
});

const { isTop } = usePageTop();
</script>




