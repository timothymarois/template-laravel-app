<template>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Post: {{ props.post.id }}</h1>
        <form  class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input v-model="form.title" id="title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Title">
            </div>
            <div class="mb-4">
                <label for="body" class="block text-gray-700 text-sm font-bold mb-2">Body:</label>
                <textarea v-model="form.body" id="body" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Body"></textarea>
            </div>
            <div class="flex items-center justify-between">
                <Button @click="submitForm" :disabled="form.processing" :loading="form.processing" label="Update" />
            </div>
        </form>
    </div>
</template>

<script setup>
const props = defineProps({
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
