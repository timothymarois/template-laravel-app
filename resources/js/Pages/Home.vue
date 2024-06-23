<template>
  <div>
    <Head title="Home" />
    <h1 class="mb-8 text-3xl font-bold">Home</h1>
    <p class="mb-8 leading-normal text-red-500">This is a home page!!!</p>

    <div>

      <!-- {{  $page.component preserve-state  }} -->
      <Button label="Save Changes" disabled />

      {{ props.example }}

      <div>
        <Link href="/about">About</Link>
      </div>

    </div>
  </div>
</template>

<script setup>
import Button from 'primevue/button';

const props = defineProps({
    'example': String
});

</script>
