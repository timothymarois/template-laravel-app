<template>
    <div>
        <Head title="Home Example" />
        <h1 class="mb-8 text-3xl font-bold">Home</h1>
        <div>
            <Link href="/example/about">About</Link> | <Link href="/example/store">Store</Link>
        </div>
        <div class="mt-6">
            <div>Page Prop:</div>
            <div>{{ props.example }}</div>
        </div>
        <div>
            <div>Shared Prop:</div>
            <div>{{ props.shared }}</div>
        </div>
        {{ cartStore.products }}
    </div>
</template>

<script setup>
const cartStore = shoppingCart();
const props = defineProps({
    'example': String,
    'shared': String
});
</script>

<script>
import Layout from '@/Layouts/Example.vue';
export default defineComponent({
	layout: Layout,
});
</script>
