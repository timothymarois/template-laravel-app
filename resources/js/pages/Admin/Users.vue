<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`">
        <template #default>
            <Card pt:content:class="p-0">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="grow flex items-center space-x-2">
                            <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
                        </div>
                        <div class="flex items-center space-x-2">
                            <InputText
                                v-model="search"
                                placeholder="Search user name or email"
                                size="small"
                                class="w-[400px]"
                                clearable
                            />
                            <Button outlined size="small" label="Filters" @click="open('ADD_EDIT_USER')" />
                        </div>
                    </div>
                </template>
                <template #content>
                    <Table
                        :items="users.data"
                        :columns="columns"
                        :sortField="sortField"
                        :sortOrder="sortOrder"
                        :selection="null"
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
