<template>
  <component
    :is="to ? 'router-link' : 'button'"
    :to="to"
    class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center gap-y-2.5 group hover:border-blue-200 transition-all"
  >
    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-105 transition-transform">
      <component :is="icon" class="w-4 h-4 text-blue-600" />
    </div>
    <span class="text-xs font-bold text-gray-700">{{ label }}</span>
  </component>
</template>

<script setup>
defineProps({
  label: String,
  icon: Object,
  to: { type: String, default: null },
});
</script>
