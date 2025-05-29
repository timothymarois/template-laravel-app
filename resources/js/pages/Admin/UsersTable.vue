<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`" containerClass="p-0" :noScroll="true">
        <template #default>
            <div class="bg-white dark:bg-surface-800">
                <ScrollFrame rootClass="overflow-hidden" :addOffset="53">
                    <Table
                        :items="users.data"
                        :columns="columns"
                        :sortField="sortField"
                        :sortOrder="sortOrder"
                        :selection="null"
                        :scrollable="true"
                        :scrollHeight="'flex'"
                        @update:selection="selected = $event"
                        @sort="onSort"
                    >
                        <template #edit="{ data }">
                            <div class="w-full flex items-center justify-center">
                                <ButtonMenu
                                    :items="[
                                        { label: 'Edit', icon: 'pi pi-pencil', click: () => open('ADD_EDIT_USER', data) },
                                        { separator: true },
                                        { label: 'Archive', icon: 'pi pi-trash', click: () => open('DELETE_USER', data) },
                                    ]"
                                />
                            </div>
                        </template>
                        <template #name="{ data }">
                            <Link class="hover:underline text-black font-medium" :href="`/admin/users/${data.id}`">
                                {{ data.name }}
                            </Link>
                        </template>
                    </Table>
                </ScrollFrame>
            </div>
        </template>
        <template #footer>
            <Select
                v-model="perPage"
                :options="perPageOptions"
                size="small"
                option-label="label"
                option-value="value"
            />
        </template>
        <template #footerAction>
            <LinkPaginator :links="users.links" :linkComponent="'Link'" />
        </template>
    </LayoutApp>
</template>

<script setup>
import Table from '@atlas/components/Table/Table.vue';
import ButtonMenu from '@atlas/components/ButtonMenu.vue';

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
    { field: 'edit', header: '', class: 'w-[20px]', sortable: false },
    { field: 'id', header: 'Id', sortable: false },
    { field: 'name', header: 'Name', sortable: true },
    { field: 'email', header: 'Email', sortable: true },
];

const perPageOptions = [
    { label: '15', value: 15 },
    { label: '25', value: 25 },
    { label: '50', value: 50 },
    { label: '100', value: 100 },
];

const { search, filters, perPage, sortField, sortOrder } = useDataTableOptions('users.table', props.options, {
    only: ['users'],
});

const onSort = ({ field, order }) => {
    sortField.value = field;
    sortOrder.value = order;
};

const selected = ref([]);

const userTotal = computed(() => props.users?.total || 0);
</script>
