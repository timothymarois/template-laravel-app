
<template>
    <Head title="Edit Post - Example" />
    <header class="bg-white dark:bg-surface-700 shadow-sm">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-8 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">Edit Post: {{ props.post.id }}</h1>
            <Button outlined @click="router.visit($route('posts.index'))" size="small">View All</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <form  class="shadow-md rounded-lg p-6">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-bold mb-2">Title:</label>
                    <InputText type="text" v-model="form.title" placeholder="Title" />
                    <div v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title  }}</div>
                </div>
                <div class="mb-4">
                    <label for="body" class="block text-sm font-bold mb-2">Body:</label>
                    <Textarea v-model="form.body" rows="5" placeholder="Body" />
                    <div v-if="form.errors.body" class="text-red-600 text-sm">{{ form.errors.body  }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <Button @click="submitForm" :disabled="form.processing" :loading="form.processing" label="Update" />
                </div>
            </form>
        </div>
    </main>
</template>

<script setup>
const props = defineProps({
    layout: 'Example',
    post: Object
});

const form = useForm({
    title: props.post.title,
    body: props.post.body,
})

const submitForm = () => {
    form.put(route('posts.update', [props.post.id]))
}
</script>
