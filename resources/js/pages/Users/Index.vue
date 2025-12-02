<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`" containerClass="p-0" :noScroll="true">
        <template v-if="(selectAll ? userTotal : selected?.length) > 0" #headerTitle>
            <TableActions
                :selectedCount="selectAll ? userTotal : selected?.length"
                :menuItems="tableActionMenuItems"
                @action="handleTableAction"
            />
        </template>
        <template #headerAction>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <InputText
                        v-model="search"
                        placeholder="Search user name or email"
                        class="w-[400px]"
                        size="small"
                        clearable
                    />
                    <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
                </div>
            </div>
        </template>
        <template #default>
            <div class="bg-white dark:bg-surface-800">
                <Table
                    :items="users.data"
                    :itemTotal="userTotal"
                    :columns="columns"
                    :sortField="sortField"
                    :sortOrder="sortOrder"
                    :selected="selected"
                    :selectAll="selectAll"
                    :defaultColumnList="defaultColumnList"
                    :activeColumnList="viewFields"
                    hasSelection
                    hasCustomizeColumns
                    :scrollOffset="53"
                    scrollable
                    @update:selected="selected = $event"
                    @update:selectAll="selectAll = $event"
                    @update:activeColumnList="viewFields = $event"
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
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import LayoutApp from '@components/Layout/App.vue';
import InputText from '@components/InputText.vue';
import Button from '@components/Button.vue';
import Select from '@components/Select.vue';
import LinkPaginator from '@components/Paginator.vue';
import Table from '@components/App/Table/Table.vue';
import ButtonMenu from '@components/ButtonMenu.vue';
import TableActions from '@components/App/Table/Actions.vue';
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

const tableActionMenuItems = ref([
    { label: 'Edit', action: 'edit' },
    { label: 'Delete', action: 'delete', disabled: true, tooltip: 'This action is disabled.'},
    { label: 'More',
        children:
        [
            { label: 'Export', action: 'export' },
            { separator: true },
            { label: 'Duplicate', action: 'dup', disabled: true, },
            { label: 'Email', action: 'email', disabled: true },
        ]
    }
]);

const handleTableAction = (action) => {
    if (action === 'clear') {
        resetSelection();
    }
};

const columns = [
    { key: 'edit', header: '', class: 'w-[20px]', sortable: false, frozen: true, style: 'min-width: 40px', locked: true, hidden: true },
    // { field: 'id', header: 'Id', sortable: false, frozen: true, style: 'min-width: 60px' },
    { key: 'name', header: 'Name', sortable: true, frozen: true, style: 'min-width: 200px', locked: true },
    { key: 'email', header: 'Email', sortable: true, style: 'min-width: 200px' },
    { key: 'email2', header: 'Email 2', sortable: true, style: 'min-width: 200px' },
    { key: 'email3', header: 'Email 3', sortable: true, style: 'min-width: 400px;' },
    { key: 'email4', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { key: 'email5', header: 'Email 3', sortable: true, style: 'min-width: 200px;' },
    { key: 'email6', header: 'Email 3', sortable: true, style: 'min-width: 200px;', group: 'Email fields' },
    { key: 'email7', header: 'Email 3', sortable: true, style: 'min-width: 200px;', group: 'Email fields' },
    { key: 'email8', header: 'Email 3', sortable: true, style: 'min-width: 200px;', group: 'Email fields' },
    { key: 'email9', header: 'Email 3', sortable: true, style: 'min-width: 200px;', group: 'Email fields' },
    { key: 'email0', header: 'Email 3', sortable: true, style: 'min-width: 200px;', group: 'Email fields' },
];

const perPageOptions = [
    { label: '15', value: 15 },
    { label: '25', value: 25 },
    { label: '50', value: 50 },
    { label: '100', value: 100 },
];

const { search, filters, perPage, sortField, sortOrder, viewFields, selectAll, selected, resetSelection } = useDataTableOptions('users.index.filters', props.options, {
    method: 'post',
    only: ['users'],
});

const defaultColumnList = ['edit', 'name', 'email'];

const onSort = ({ field, order }) => {
    sortField.value = field;
    sortOrder.value = order;
};

const userTotal = computed(() => props.users?.total || 0);
</script>
