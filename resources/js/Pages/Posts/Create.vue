<template>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Create Post</h1>
        <form @submit.prevent="submitForm" class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input v-model="form.title" id="title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Title">
            </div>
            <div class="mb-4">
                <label for="body" class="block text-gray-700 text-sm font-bold mb-2">Body:</label>
                <textarea v-model="form.body" id="body" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Body"></textarea>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create</button>
            </div>
        </form>
    </div>
</template>

<script setup>
import {useForm} from '@inertiajs/vue3'

const form = useForm({
    title: '',
    body: ''
})

const submitForm = () => {
    form.post('/posts')
}
</script>
