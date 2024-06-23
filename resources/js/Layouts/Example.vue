<template>
    <AppContainer>
        <div>Example Layout Nav with page-level prop: "{{  props.example }}")</div>
        <slot />

        {{  page }}
    </AppContainer>
</template>

<script setup>
import { defineComponent } from 'vue';
const props = defineProps({
    'example': String
});

const page = usePage();
</script>
