<template>
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Posts</h1>
            <button @click="$inertia.visit('/posts/create')" class="bg-blue-500 text-white px-4 py-2 rounded">Create Post</button>
        </div>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <ul>
                <li v-for="post in posts" :key="post.id" class="border-b last:border-none p-4">
                    <h2 class="text-xl font-semibold">{{ post.title }}</h2>
                    <p class="text-gray-700">{{ post.body }}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    posts: Array
})
</script>
