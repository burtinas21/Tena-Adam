<template>
  <div
    class="flex items-center justify-between p-6 bg-white border border-gray-200 rounded-xl shadow-sm"
  >
    <div class="space-y-2">
      <div
        class="w-10 h-10 flex items-center justify-center rounded-lg"
        :class="iconBgClass"
      >
        <!-- Render the Lucide icon dynamically -->
        <component :is="icon" class="w-5 h-5" :class="iconColorClass" />
      </div>
      <div class="text-3xl font-bold text-gray-900 pt-2">{{ value }}</div>
      <div class="text-xs font-medium text-gray-500">{{ title }}</div>
    </div>

    <div
      class="self-start flex items-center space-x-1 px-2.5 py-1 bg-gray-50 border border-gray-100 rounded-full text-[10px] font-semibold text-gray-400 uppercase tracking-wider"
    >
      <span>{{ period }}</span>
      <span
        v-if="status === 'success'"
        class="w-1.5 h-1.5 rounded-full bg-emerald-500"
      ></span>
      <span
        v-else-if="status === 'danger'"
        class="w-1.5 h-1.5 rounded-full bg-rose-500"
      ></span>
      <span v-else class="w-1.5 h-1.5 rounded-full bg-[#0252D7]"></span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  title: String,
  value: [String, Number],
  period: String,
  status: String, // 'primary', 'success', 'danger'
  icon: [Object, Function], // Accepts imported Lucide components
});

const iconBgClass = computed(() => {
  if (props.status === "success") return "bg-emerald-50";
  if (props.status === "danger") return "bg-rose-50";
  return "bg-blue-50";
});

const iconColorClass = computed(() => {
  if (props.status === "success") return "text-emerald-500";
  if (props.status === "danger") return "text-rose-500";
  return "text-[#0252D7]";
});
</script>
