<template>
    <Head title="Posts - Example" />
    <header class="bg-white shadow-sm">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <h1 class="text-lg font-semibold leading-6 text-gray-900">Posts</h1>
            <Button @click="router.visit(route('posts.create'))" size="small">Create Post</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <ul>
                <li
                    v-for="post in posts.data"
                    :key="post.id"
                    @click="router.visit(route('posts.show', [post.id]))"
                    class="border-b last:border-none p-4 cursor-pointer hover:bg-gray-200/50 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold">{{ post.title }}</h2>
                            <p class="text-xs text-gray-700">{{ post.body }}</p>
                        </div>
                        <div>
                            <div class="flex space-x-2 items-center">
                                <Button @click.prevent.stop="" text label="Edit" icon="pi pi-pencil" size="small" />
                                <Button @click.prevent.stop="deletePost(post)" text  icon="pi pi-trash" size="small" :loading="deleteLoading" />
                            </div>
                        </div>
                </li>
            </ul>
        </div>
    </main>
</template>

<script setup>
const props = defineProps({
    layout: 'Example',
    posts: Object
});

const deleteLoading = ref(false)

const deletePost = (post) => {
    // { preserveState: true  }
    router.delete(route('posts.destroy', [post.id]), {
        onStart: visit => {
            // deleteLoading.value = true
        },
        onSuccess: page => {
            // deleteLoading.value = false
        },
    })
}
</script>
