<template>
    <div>
        <div>Example Layout Nav "{{  props.example }}"</div>
        <slot />
    </div>
</template>

<script setup>
const props = defineProps({
    'example': String
});
</script>
