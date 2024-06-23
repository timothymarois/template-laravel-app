<template>
    <div>
        <Head title="Store Example" />
        <h1 class="mb-8 text-3xl font-bold">Store</h1>
        <div>
            <Link href="/example">Home</Link>
        </div>
        <Button label="Add to cart" @click="addToCart" />

        <div class="mt-6">
            products: {{ cartStore.products }}
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    'layout': 'Example',
});

const products = [
    {
        id: 1,
        name: 'Product A',
        sale_price: 49.99
    },
    {
        id: 2,
        name: 'Product B',
        sale_price: 29.99
    },
    {
        id: 3,
        name: 'Product C',
        sale_price: 39.99
    }
];

const cartStore = shoppingCart();

const addToCart = () => {
    cartStore.add(products[0])
};
</script>

<!-- <script>
import Layout from '@/Layouts/Example.vue';
export default defineComponent({
	layout: Layout,
});
</script> -->
