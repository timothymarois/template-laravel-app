<template>
    <OtherContainer>
        <div>Example Layout Nav with page-level prop: "{{  props.example }}"</div>
        <slot />
    </OtherContainer>
</template>

<script setup>
const props = defineProps({
    'example': String
});
</script>
