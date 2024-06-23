<template>
    <div>
        <Head title="About" />
        <h1 class="mb-8 text-3xl font-bold">About</h1>
        <div>
            <Link href="/">Home</Link>
        </div>
        <div class="mt-6">
            <div>Page Prop:</div>
            <div>{{ props.example }}</div>
        </div>
        <div>
            <div>Shared Prop:</div>
            <div>{{ props.shared }}</div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    'example': String,
    'shared': String
});
</script>
