<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`" containerClass="p-0" :noScroll="true">
        <template #headerAction>
            <div class="flex items-center justify-between">
                <!-- <div class="grow flex items-center space-x-2">
                    <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
                </div> -->
                <div class="flex items-center space-x-2">
                    <!-- <div class="w-[250px]">
                        <Select
                            v-model="filters.user_id"
                            :options="[{ id: null, name: 'All users' }, ...users.data]"
                            option-label="name"
                            option-value="id"
                            size="small"
                            placeholder="Filter user (testing only)"
                            fluid
                            filter
                            showClear
                        />
                    </div> -->
                    <InputText
                        v-model="search"
                        placeholder="Search user name or email"
                        class="w-[400px]"
                        size="small"
                        clearable
                    />
                    <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
                    <!-- <Button outlined label="Filters" @click="open('ADD_EDIT_USER')" /> -->
                </div>
            </div>
        </template>
        <template #default>
            <div class="bg-white dark:bg-surface-800">
                <ScrollFrame rootClass="overflow-hidden" :addOffset="53">
                    <Table
                        :items="users.data"
                        :columns="columns"
                        :sortField="sortField"
                        :sortOrder="sortOrder"
                        :selection="selected"
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
                            <Link class="hover:underline text-black font-medium" :href="$route('users.show', data.id)">
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
    { field: 'edit', header: '', class: 'w-[20px]', sortable: false, frozen: true, style: 'min-width: 40px' },
    // { field: 'id', header: 'Id', sortable: false, frozen: true, style: 'min-width: 60px' },
    { field: 'name', header: 'Name', sortable: true, frozen: true, style: 'min-width: 200px' },
    { field: 'email', header: 'Email', sortable: true, style: 'min-width: 200px' },
    { field: 'email', header: 'Email 2', sortable: true, style: 'min-width: 200px' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 400px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { field: 'email', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
];

const perPageOptions = [
    { label: '15', value: 15 },
    { label: '25', value: 25 },
    { label: '50', value: 50 },
    { label: '100', value: 100 },
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
