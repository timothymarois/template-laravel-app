<template>
    <Head title="Posts - Example" />
    <header class="bg-white dark:bg-surface-700 shadow-sm">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-8 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">Posts</h1>
            <Button @click="router.visit($route('posts.create'))" size="small">Create Post</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <ul>
                <li
                    v-for="post in posts.data"
                    :key="post.id"
                    @click="router.visit($route('posts.show', [post.id]))"
                    class="border-b last:border-none border-gray-100 dark:border-gray-700 p-4 cursor-pointer hover:bg-surface-200/50 dark:hover:bg-surface-700/50 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold">{{ post.title }}</h2>
                            <p class="text-xs text-gray-700 dark:text-gray-200">{{ post.body }}</p>
                        </div>
                        <div>
                            <!-- <div class="flex space-x-2 items-center"> -->
                                <InputGroup>
                                    <Button @click.prevent.stop="openEditModal(post)" outlined label="Edit" icon="pi pi-pencil" size="small" />
                                    <Button @click.prevent.stop="deletePost(post)" outlined  icon="pi pi-trash" size="small" :loading="deleteLoading" />
                                </InputGroup>
                            <!-- </div> -->
                        </div>
                </li>
            </ul>
            <!-- <div>{{ pageLoaded }}</div> -->
        </div>
    </main>

    <Dialog v-model:visible="showModal" modal header="Edit Post">
        <form  class="p-6">
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
        </form>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="showModal = false;form.reset()"></Button>
            <Button type="button" label="Save" @click="savePost" :disabled="form.processing" :loading="form.processing"></Button>
        </div>
    </Dialog>
</template>

<script setup>
const props = defineProps({
    layout: 'Example',
    posts: Object
});

const showModal = ref(false)
const deleteLoading = ref(false)

const form = useForm({
    id: null,
    title: null,
    body: null
})

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

const openEditModal = (post) => {
    showModal.value = true
    form.id = post.id
    form.title = post.title
    form.body = post.body
};

const savePost = () => {
    if (form.id) {
        form.put(route('posts.update', [form.id]), {
            onSuccess: post => {
                showModal.value = false
            },
        })
    }
};
</script>
