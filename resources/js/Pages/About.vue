<template>
    <div>
        <Head title="About" />

        <h1 class="mb-8 text-3xl font-bold">About</h1>
        <p class="mb-8 leading-normal text-red-500">This is about page!!!</p>

        {{ props.example }}

        <div>
            <Link href="/">Home</Link>
        </div>

    </div>
</template>

<script setup>
const props = defineProps({
    'example': String
});
</script>
