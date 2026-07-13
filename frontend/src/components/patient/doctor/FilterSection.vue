<template>
  <div class="border-b border-gray-100 py-4 last:border-0">
    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
      {{ title }}
    </h3>
    <div class="space-y-2.5">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>
