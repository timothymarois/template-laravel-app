<template>
    <LayoutApp title="User">
        <template #headerTitle>
            <div class="pr-2">
                <Button
                    as="Link"
                    text
                    icon="pi pi-arrow-left"
                    size="small"
                    :href="$route('users.index')"
                />
            </div>
            <div class="flex flex-col space-y-0 py-3">
                <div class="text-md font-medium">{{ item.name }}</div>
                <div class="text-sm text-slate-500">{{ item.email }}</div>
            </div>
        </template>
        <template #pageSideContent>
            <div class="h-[1000px] w-[350px] p-4">
                <div>This is my side content</div>
            </div>
        </template>
        <template #default>
            {{ item }}
        </template>
    </LayoutApp>
</template>

<script setup>
const props = defineProps({
    item: {
        type: Object,
        default: () => ({}),
    },
});
</script>
