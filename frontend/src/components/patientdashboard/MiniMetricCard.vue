<template>
  <div
    class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between min-h-[75px] hover:border-gray-200 transition-colors"
  >
    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-gray-400">
      <span>{{ title }}</span>
      <component :is="icon" :class="iconColor" class="w-3.5 h-3.5" />
    </div>
    <h3 class="text-2xl font-bold text-gray-800 leading-none mt-2">
      {{ value }}
    </h3>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: Object,
  color: { type: String, default: "gray" },
});

const iconColor = computed(() => ({
  blue:    "text-blue-500",
  emerald: "text-emerald-500",
  amber:   "text-amber-500",
  red:     "text-red-500",
  gray:    "text-gray-400",
}[props.color] ?? "text-gray-400"));
</script>
