<template>
    <Head title="Users" />
    <LayoutDefault>
        <PageHeader :title="`Users (${userTotal})`">
            <template #actions>
                <Button label="Add" @click="open('ADD_EDIT_USER')" />
            </template>
        </PageHeader>
        <PageMain>
            <div class="shadow border border-surface-200 dark:border-surface-600 bg-white dark:bg-surface-700 rounded">
                <ul>
                    <li
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b last:border-none border-gray-100 dark:border-gray-700 p-4 hover:bg-surface-200/50 dark:hover:bg-surface-700/50 flex items-center justify-between rounded"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="min-w-[300px]">
                                <h2 class="text-sm font-semibold">
                                    {{ user.name }}
                                </h2>
                                <p class="text-xs text-gray-700 dark:text-gray-200">
                                    {{ user.email }}
                                </p>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">
                                    Updated
                                </h2>
                                <p class="text-xs text-gray-700 dark:text-gray-200">
                                    {{ formatToDatetime(user.updated_at) }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <ButtonGroup>
                                <Button
                                    label="Edit"
                                    icon="pi pi-pencil"
                                    size="small"
                                    @click.prevent.stop="open('ADD_EDIT_USER', user)"
                                />
                                <Button
                                    icon="pi pi-pencil"
                                    size="small"
                                    @click.prevent.stop="open('TEST')"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    size="small"
                                    @click.prevent.stop="open('DELETE_USER', user)"
                                />
                            </ButtonGroup>
                        </div>
                    </li>
                </ul>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
import { useModal } from '@atlas/composables';
import { formatToDatetime } from '@atlas/utils/format';

const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    }
});

const { open } = useModal();

const userTotal = computed(() => {
    return props?.users?.total || 0;
});
</script>
