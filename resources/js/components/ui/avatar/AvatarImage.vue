<script setup lang="ts">
import type { AvatarImageEmits, AvatarImageProps } from "reka-ui";
import { AvatarImage, useForwardPropsEmits } from "reka-ui";

const props = defineProps<AvatarImageProps>();
const emits = defineEmits<AvatarImageEmits>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
  <AvatarImage v-bind="forwarded" class="h-full w-full object-cover" />
</template>
