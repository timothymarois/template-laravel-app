<template>
    <LayoutApp title="Users" :pageTitle="`Users (${userTotal})`">
        <template #headerAction>
            <Button size="small" label="Add user" @click="open('ADD_EDIT_USER')" />
        </template>
        <template #default>
            <Card pt:content:class="p-1">
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
            This is a page footer
        </template>
        <template #footerAction>
            [actions]
        </template>
    </LayoutApp>
</template>

<script setup>
import Column from 'primevue/column';

const users = usePageProp('users', {});

// const props = defineProps({
//     users: {
//         type: Object,
//         default: () => {}
//     }
// });

const { open } = useModal();

const userTotal = computed(() => {
    return users.value?.total || 0;
});

</script>
