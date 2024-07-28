<template>
    <Head title="Store" />
    <PageHeader title="Store" :tabs="tabs" />
    <PageMain>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-4">This is an example store page. This uses pinia persisted storage.</div>

            <Card class="mb-4">
                <template #title>Products in cart</template>
                <template #content>
                    <div>{{ cartStore.products }}</div>
                </template>
            </Card>

            <Card>
                <template #content>
                    <div class="flex space-x-4 items-center">
                        <Button size="small" label="Add product A" @click="cartStore.add(products[0])" />
                        <Button size="small" label="Add product B" @click="cartStore.add(products[1])" />
                        <Button size="small" label="Clear cart" @click="cartStore.clear()" outlined />
                        <div>Cart total: <span class="font-semibold">{{ formatToCurrency(cartStore.total || 0) }}</span></div>
                    </div>
                </template>
            </Card>

        </div>
    </PageMain>
</template>
<script setup>
// const props = defineProps({
//     layout: 'Example'
// });
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
const cartStore = useCartStore();

const tabs = [
    {
        href: '/example/store',
        title: 'Store'
    },
    {
        href: '/example/store/active',
        title: 'Active'
    },
];
</script>
