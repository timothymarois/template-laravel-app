<template>
    <div>
        <Head title="About" />
        <h1 class="mb-8 text-3xl font-bold">Home</h1>
        <div>
            <Link href="/about">About</Link>
        </div>
        <div class="mt-6">
            <div>Page Prop:</div>
            <div>{{ props.example }}</div>
        </div>
        <div>
            <div>Shared Prop:</div>
            <div>{{ props.shared }}</div>
        </div>

        <Button label="Example Button" disabled />

    </div>
</template>

<script setup>
import Button from 'primevue/button';
const props = defineProps({
    'example': String,
    'shared': String
});
</script>
