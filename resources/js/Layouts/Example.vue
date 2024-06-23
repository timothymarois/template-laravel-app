<template>
    <ExampleContainer>
        <div>
            <Link href="/example">Home</Link> |
            <Link href="/example/about">About</Link> |
            <Link href="/example/store">Store</Link> |
            <Link href="/example/signup">Signup</Link>
        </div>
        <slot />
    </ExampleContainer>
</template>

<script setup>
const props = defineProps({
    'example': String
});
</script>
