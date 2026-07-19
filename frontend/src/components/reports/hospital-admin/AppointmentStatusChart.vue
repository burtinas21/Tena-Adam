<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between"
  >
    <!-- Header Row -->
    <div class="flex items-center justify-between pb-2">
      <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">
        Appointment Status
      </h3>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="relative h-[210px] w-full flex items-center justify-center my-2">
      <div class="w-44 h-44 rounded-full border-4 border-slate-100 animate-pulse" />
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!data || data.total === 0"
      class="relative h-[210px] w-full flex items-center justify-center my-2"
    >
      <span class="text-xs text-slate-400 font-medium">No appointment data</span>
    </div>

    <!-- Donut chart -->
    <div
      v-else
      class="relative h-[210px] w-full flex items-center justify-center my-2"
    >
      <svg class="w-44 h-44 transform -rotate-90" viewBox="0 0 36 36">
        <!-- Gray base track -->
        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#F1F5F9" stroke-width="3" />

        <!-- Completed segment (blue) -->
        <circle
          cx="18" cy="18" r="15.915"
          fill="none"
          stroke="#0252D7"
          stroke-width="3.2"
          :stroke-dasharray="`${completedPct} ${100 - completedPct}`"
          stroke-dashoffset="0"
          stroke-linecap="round"
        />

        <!-- Pending segment (amber) -->
        <circle
          cx="18" cy="18" r="15.915"
          fill="none"
          stroke="#F59E0B"
          stroke-width="3"
          :stroke-dasharray="`${pendingPct} ${100 - pendingPct}`"
          :stroke-dashoffset="`-${completedPct}`"
          stroke-linecap="round"
        />

        <!-- Cancelled segment (red) -->
        <circle
          cx="18" cy="18" r="15.915"
          fill="none"
          stroke="#DC2626"
          stroke-width="3.2"
          :stroke-dasharray="`${cancelledPct} ${100 - cancelledPct}`"
          :stroke-dashoffset="`-${completedPct + pendingPct}`"
          stroke-linecap="round"
        />
      </svg>

      <!-- Centre label -->
      <div class="absolute flex flex-col items-center pointer-events-none">
        <span class="text-xl font-extrabold text-slate-800">{{ data.total }}</span>
        <span class="text-[10px] text-slate-400 font-semibold">Total</span>
      </div>
    </div>

    <!-- Legend -->
    <div
      class="grid grid-cols-2 gap-x-4 gap-y-2.5 pt-3.5 border-t border-slate-50 text-xs font-bold text-slate-500"
    >
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#0252D7] shrink-0"></span>
        <span class="text-slate-700 font-semibold">Completed</span>
        <span class="ml-auto text-slate-400 text-[10px]">{{ data?.completed ?? 0 }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
        <span class="text-slate-700 font-semibold">Pending</span>
        <span class="ml-auto text-slate-400 text-[10px]">{{ data?.pending ?? 0 }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] shrink-0"></span>
        <span class="text-slate-700 font-semibold">Cancelled</span>
        <span class="ml-auto text-slate-400 text-[10px]">{{ data?.cancelled ?? 0 }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 shrink-0"></span>
        <span class="text-slate-700 font-semibold">Today</span>
        <span class="ml-auto text-slate-400 text-[10px]">{{ data?.today ?? 0 }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  data: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const completedPct = computed(() => {
  if (!props.data || !props.data.total) return 0;
  return Math.round((props.data.completed / props.data.total) * 100);
});

const pendingPct = computed(() => {
  if (!props.data || !props.data.total) return 0;
  return Math.round((props.data.pending / props.data.total) * 100);
});

const cancelledPct = computed(() => {
  if (!props.data || !props.data.total) return 0;
  return Math.round((props.data.cancelled / props.data.total) * 100);
});
</script>
