<template>
    <div class="max-w-8xl lg:max-w-full mx-auto px-6 lg:px-8">
        <div>Example Layout Nav with page-level prop: "{{  props.example }}")</div>
        <slot />

        <!-- {{  page }} -->
    </div>
</template>

<script setup>
import { defineComponent } from 'vue';
const props = defineProps({
    'example': String
});

// const page = usePage();
</script>
