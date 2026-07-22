<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_0_rgba(0,0,0,0.01)] p-5 flex flex-col h-[360px]"
  >
    <div class="flex-shrink-0 space-y-1">
      <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Appointment Trends</h3>
      <div class="flex items-center space-x-1.5 text-[10px] font-bold">
        <span class="w-2.5 h-2.5 rounded-sm bg-[#0252D7] inline-block"></span>
        <span class="text-slate-600">Appointments</span>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex-1 flex items-center justify-center mt-4">
      <div class="w-full h-full bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state: no labels -->
    <div v-else-if="!displayLabels.length" class="flex-1 flex items-center justify-center mt-4">
      <span class="text-xs text-slate-400">No data</span>
    </div>

    <!-- Bar chart -->
    <div v-else class="flex-1 flex flex-col min-h-0 mt-4">
      <!-- Chart drawing area -->
      <div class="relative flex-1 min-h-0 border-b border-slate-100">

        <!-- Y-axis gridlines -->
        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none px-2">
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="w-full"></div>
        </div>

        <!-- Bars -->
        <div class="absolute inset-x-2 bottom-0 top-0 flex items-end gap-1.5">
          <div
            v-for="(val, i) in displayData"
            :key="i"
            class="flex-1 rounded-t transition-all duration-700 ease-out"
            :style="{
              minHeight: '4px',
              height: hasData ? barH(val) + '%' : '4px',
              background: i === displayData.length - 1
                ? '#0252D7'
                : `rgba(2,82,215,${0.22 + (i / Math.max(displayData.length - 1, 1)) * 0.60})`,
            }"
          />
        </div>
      </div>

      <!-- X-axis labels -->
      <div class="flex items-center justify-between pt-2 text-[10px] font-bold text-slate-400 flex-shrink-0">
        <span v-for="label in displayLabels" :key="label">{{ label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  labels:  { type: Array,   default: () => [] },
  data:    { type: Array,   default: () => [] },
  loading: { type: Boolean, default: false },
});

const displayLabels = computed(() => props.labels.slice(-6));
const displayData   = computed(() => props.data.slice(-6));
const hasData = computed(() => displayData.value.some(v => v > 0));
const maxVal  = computed(() => Math.max(1, ...displayData.value));

function barH(val) {
  if (!hasData.value) return 4;
  return Math.max(4, Math.round((val / maxVal.value) * 88));
}
</script>
