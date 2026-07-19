<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_0_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between h-[360px]">
    <div class="flex items-center justify-between pb-2">
      <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Patient Growth</h3>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="w-full h-48 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state -->
    <div v-else-if="!labels.length" class="flex-1 flex items-center justify-center">
      <span class="text-xs text-slate-400 font-medium">No trend data available</span>
    </div>

    <!-- Chart -->
    <div v-else class="relative h-[250px] w-full mt-4 flex flex-col justify-end">
      <!-- Bar columns -->
      <div class="absolute inset-x-4 top-4 bottom-8 flex items-end justify-between z-10 gap-1">
        <div
          v-for="(val, i) in displayData"
          :key="i"
          class="flex-1 rounded-t-sm transition-all duration-500"
          :style="{
            height: barHeight(val) + '%',
            background: i === displayData.length - 1 ? '#0252D7' : `rgba(2,82,215,${0.10 + (i / displayData.length) * 0.7})`,
          }"
        />

        <!-- Trend line overlay -->
        <svg
          class="absolute inset-0 w-full h-full overflow-visible pointer-events-none"
          viewBox="0 0 100 100"
          preserveAspectRatio="none"
        >
          <polyline
            :points="svgLinePoints"
            fill="none"
            stroke="#0252D7"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </div>

      <!-- X-Axis labels (show last 6) -->
      <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[10px] font-bold text-slate-400 px-4">
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

// Show last 6 months for readability
const displayLabels = computed(() => props.labels.slice(-6));
const displayData = computed(() => props.data.slice(-6));

const maxVal = computed(() => Math.max(1, ...displayData.value));

function barHeight(val) {
  return Math.round((val / maxVal.value) * 90);
}

const svgLinePoints = computed(() => {
  const d = displayData.value;
  if (!d.length) return '';
  return d
    .map((v, i) => {
      const x = d.length === 1 ? 50 : (i / (d.length - 1)) * 94 + 3;
      const y = 96 - (v / maxVal.value) * 88;
      return `${x},${y}`;
    })
    .join(' ');
});
</script>
