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
                            <div class="w-[250px]">
                                <Select
                                    v-model="filters.user_id"
                                    :options="[{ id: null, name: 'All users' }, ...users.data]"
                                    option-label="name"
                                    option-value="id"
                                    placeholder="Filter user (testing only)"
                                    fluid
                                    filter
                                    showClear
                                />
                            </div>
                            <InputText
                                v-model="search"
                                placeholder="Search user name or email"
                                class="w-[400px]"
                                clearable
                            />
                            <Button outlined label="Filters" @click="open('ADD_EDIT_USER')" />
                        </div>
                    </div>
                </template>
                <template #content>
                    <Table
                        :items="users.data"
                        :itemTotal="userTotal"
                        :columns="columns"
                        :activeColumnList="['edit', 'name', 'email']"
                        :sortField="sortField"
                        :sortOrder="sortOrder"
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
                            <Link class="hover:underline" :href="`/users/${data.id}`">
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
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, Button, Select, Input as InputText, Paginator as LinkPaginator, DataTable as Table, ButtonMenu } from '@/components/ui';
import { useModal } from '@/composables';
import { useDataTableOptions } from '@/composables/inertia';

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
    { key: 'edit', header: '', class: 'w-[20px]', sortable: false },
    { key: 'id', header: 'Id', sortable: false },
    { key: 'name', header: 'Name', sortable: true },
    { key: 'email', header: 'Email', sortable: true },
];

const perPageOptions = [
    { label: '15', value: 15 },
    { label: '25', value: 25 },
    { label: '50', value: 50 },
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
