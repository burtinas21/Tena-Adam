<template>
  <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-[0_1px_2px_rgba(0,0,0,0.02)] flex flex-col justify-between h-[125px]">
    <!-- Loading skeleton -->
    <template v-if="loading">
      <div class="h-3 w-24 bg-slate-100 rounded animate-pulse" />
      <div class="mt-auto h-8 w-20 bg-slate-100 rounded animate-pulse" />
    </template>

    <template v-else>
      <!-- Top Row: Label and Trend pill badge -->
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 tracking-tight">{{ title }}</span>

        <div
          class="flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wide"
          :class="trendClass"
        >
          <span v-if="trendType === 'up'">↗</span>
          <span v-else-if="trendType === 'down'">↘</span>
          <span v-else>•</span>
          <span>{{ trendValue }}</span>
        </div>
      </div>

      <!-- Bottom Row: Primary Display Metric -->
      <div class="mt-auto">
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight font-sans">
          {{ value }}
        </h3>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: String,
  value: String,
  trendValue: String,
  trendType: String, // 'up', 'down', 'neutral'
  loading: {
    type: Boolean,
    default: false,
  },
});

const trendClass = computed(() => {
  if (props.trendType === 'up') return 'bg-emerald-50 text-emerald-600 border border-emerald-100/50';
  if (props.trendType === 'down') return 'bg-rose-50 text-rose-600 border border-rose-100/50';
  return 'bg-blue-50 text-blue-600 border border-blue-100/50';
});
</script>
