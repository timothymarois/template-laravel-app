<template>
    <AppLayout title="User">
        <template #headerTitle>
            <div class="pr-2">
                <Link :href="$route('admin.users.index')">
                    <Button text size="small">
                        <ArrowLeft class="size-4" />
                    </Button>
                </Link>
            </div>
            <div class="flex flex-col space-y-0 py-3">
                <div class="text-base font-medium">{{ item.name }}</div>
                <div class="text-sm text-slate-500">{{ item.email }}</div>
            </div>
        </template>
        <template #headerAction>
            <Button
                size="small"
                label="Edit"
                @click="open('ADD_EDIT_USER', item)"
            />
        </template>
        <template #pageSideContent>
            <div class="h-[1000px] w-[350px] p-4">
                <div>This is my side content</div>
            </div>
        </template>
        <template #default>
            <div class="h-[1000px]">
                <div>This is my page content</div>
                <div>{{ item }}</div>
            </div>
        </template>
        <!-- <template #footer>
            This is a page footer
        </template>
        <template #footerAction>
            [actions]
        </template> -->
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Button } from '@/components/ui';
import { useModal } from '@/composables';
import { ArrowLeft } from 'lucide-vue-next';
const props = defineProps({
    item: {
        type: Object,
        default: () => ({}),
    },
});

const { open } = useModal();
</script>
