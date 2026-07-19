<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_0_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between h-[360px]"
  >
    <div class="space-y-2">
      <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Appointment Trends</h3>
      <div class="flex items-center space-x-3 text-[10px] font-bold text-slate-400">
        <div class="flex items-center space-x-1.5">
          <span class="w-2.5 h-2.5 rounded-sm bg-[#0252D7]"></span>
          <span class="text-slate-600">Appointments</span>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex-1 flex items-center justify-center mt-4">
      <div class="w-full h-40 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty -->
    <div v-else-if="!data.length" class="flex-1 flex items-center justify-center mt-4">
      <span class="text-xs text-slate-400">No data</span>
    </div>

    <!-- Bar chart (last 6 months) -->
    <div v-else class="relative h-[220px] w-full flex flex-col justify-end mt-4">
      <div class="absolute inset-x-2 top-2 bottom-8 flex items-end justify-between gap-1">
        <div
          v-for="(val, i) in displayData"
          :key="i"
          class="flex-1 rounded-t-sm transition-all duration-500"
          :style="{
            height: barH(val) + '%',
            background: i === displayData.length - 1 ? '#0252D7' : `rgba(2,82,215,${0.20 + (i / displayData.length) * 0.60})`,
          }"
        />
      </div>

      <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[10px] font-bold text-slate-400">
        <span v-for="label in displayLabels" :key="label">{{ label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  data: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const displayLabels = computed(() => props.labels.slice(-6));
const displayData = computed(() => props.data.slice(-6));
const maxVal = computed(() => Math.max(1, ...displayData.value));

function barH(val) {
  return Math.round((val / maxVal.value) * 88);
}
</script>
