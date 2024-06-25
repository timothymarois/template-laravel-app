
<template>
    <Head title="Edit Post - Example" />
    <header class="bg-white shadow-sm">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-8 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900">Edit Post: {{ props.post.id }}</h1>
            <Button outlined @click="router.visit($route('posts.index'))" size="small">View All</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <form  class="bg-white shadow-md rounded-lg p-6">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                    <input v-model="form.title" id="title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Title">
                    <div v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title  }}</div>
                </div>
                <div class="mb-4">
                    <label for="body" class="block text-gray-700 text-sm font-bold mb-2">Body:</label>
                    <textarea v-model="form.body" id="body" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Body"></textarea>
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
