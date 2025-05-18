<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`">
        <template #default>
            <Card pt:content:class="p-0.5 pt-0">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="grow flex items-center space-x-2">
                            <InputText
                                v-model="search"
                                placeholder="Search user name or email"
                                size="small"
                                class="w-[400px]"
                                clearable
                            />
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
                        </div>
                    </div>
                </template>
                <template #content>
                    <Table
                        :items="users.data"
                        :columns="columns"
                        :sortField="sortField"
                        :sortOrder="sortOrder"
                        :selection="selected"
                        @update:selection="selected = $event"
                        @sort="onSort"
                    >
                        <template #name="{ data }">
                            <Link class="hover:underline" :href="`/projects/${data.id}/overview`">
                                {{ data.name }}
                            </Link>
                        </template>
                    </Table>
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
                <template v-for="link in users.links" :key="link.label">
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
import Table from '@atlas/components/Table/Table.vue';

const props = defineProps({
    users: {
        type: Object,
        default: () => ({}),
    },
    options: {
        type: Object,
        default: () => ({}),
    },
});

const { open } = useModal();

const columns = [
    { field: 'id', header: 'Id', sortable: false },
    { field: 'name', header: 'Name', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
];

const perPageOptions = [
    { label: '15', value: 15 },
    { label: '25', value: 25 },
    { label: '50', value: 50 },
];

const { search, filters, perPage, sortField, sortOrder } = useDataTableOptions('users.index', props.options, {
    only: ['users'],
});

const onSort = ({ field, order }) => {
    sortField.value = field;
    sortOrder.value = order;
};

const selected = ref([]);

const userTotal = computed(() => props.users?.total || 0);
</script>
