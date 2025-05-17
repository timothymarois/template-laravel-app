<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`">
        <template #headerAction>
            <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
        </template>
        <template #default>
            <Card pt:content:class="p-0.5">
                <template #header>
                    <div class="flex items-center">
                        <div class="grow flex items-center space-x-2">
                            <InputText
                                v-model="search"
                                placeholder="Search user name or email"
                                size="small"
                                class="w-[400px]"
                            />
                        </div>
                    </div>
                </template>
                <template #content>
                    <div>
                        <DataTable
                            row-hover
                            :value="users.data"
                            size="small"
                            table-style="min-width: 50rem"
                        >
                            <Column
                                field="name"
                                header="Name"
                            >
                                <template #body="slotProps">
                                    <Link class="hover:underline" :href="`/projects/${slotProps.data.id}/overview`">
                                        {{ slotProps.data.name }}
                                    </Link>
                                </template>
                            </Column>
                            <Column
                                field="email"
                                header="Email"
                            />
                        </DataTable>
                    </div>
                </template>
            </Card>
        </template>
        <template #footer>
            <Select
                v-model="perPage"
                :options="perPageOptions"
                option-label="label"
                option-value="value"
            />
        </template>
        <template #footerAction>
            <div class="flex items-center justify-center flex-wrap gap-1">
                <template
                    v-for="link in users.links"
                    :key="link.label"
                >
                    <Link
                        preserve-scroll
                        :href="link.url ?? ''"
                        class="flex items-center justify-center px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="{
                            'bg-gray-200 dark:bg-gray-600 dark:text-white text-gray-900 font-semibold': link.active,
                            '!text-gray-300': !link.url
                        }"
                        v-html="link.label"
                    />
                </template>
            </div>
        </template>
    </LayoutApp>
</template>

<script setup>
import debounce from 'lodash/debounce';
import Column from 'primevue/column';

const props = defineProps({
    filters: {
        type: Object,
        default: () => {}
    },
});

const { open } = useModal();
const users = usePageProp('users', {});
const perPageOptions = [{ label: '15', value: 15 }, { label: '25', value: 25 }, { label: '50', value: 50 }];

const perPage = ref(props.filters.perPage ?? 15);
const search = ref(props.filters.search);

watch(props, (p) => {
    search.value = p.filters.search;
},{deep: true});

watch(search, debounce(function(value) {
    router.get(route('users.index'), {
        search: search.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace:true
    });
}, 250));

watch(perPage, (value) => {
    router.get(route('users.index'), {
        search: search.value,
        perPage: value
    }, {
        preserveState: true,
        replace: true
    });
});

const userTotal = computed(() => {
    return users.value?.total || 0;
});

</script>
