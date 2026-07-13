<template>
  <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center justify-between shadow-sm">
    <div>
      <p class="text-xs font-semibold text-gray-500 mb-1">{{ label }}</p>
      <p class="text-3xl font-bold text-slate-800" :class="textColorClass">{{ count }}</p>
    </div>
    <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', bgClass]">
      <!-- Dynamic SVG Injection Slot -->
      <slot />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: String,
  count: [Number, String],
  type: {
    type: String,
    default: 'default'
  }
});

const textColorClass = computed(() => {
  if (props.type === 'completed') return 'text-emerald-600';
  if (props.type === 'missed') return 'text-red-500';
  return 'text-slate-800';
});

const bgClass = computed(() => {
  if (props.type === 'upcoming') return 'bg-blue-50 text-blue-600';
  if (props.type === 'completed') return 'bg-emerald-50 text-emerald-600';
  if (props.type === 'missed') return 'bg-red-50 text-red-500';
  return 'bg-blue-50/50 text-blue-500';
});
</script>
