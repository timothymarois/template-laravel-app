<template>
    <Container>
        <div>Example Layout Nav with page-level prop: "{{  props.example }}"</div>
        <slot />
    </Container>
</template>

<script setup>
const props = defineProps({
    'example': String
});
</script>
