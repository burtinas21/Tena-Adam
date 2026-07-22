<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.01)] p-5 flex flex-col h-[300px]"
  >
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 flex-shrink-0">
      <div class="space-y-0.5">
        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
          Doctor Activity
        </h3>
        <p class="text-[10px] text-slate-400 font-medium">
          Completed appointments by day &amp; time slot
        </p>
      </div>
      <button class="p-1 text-slate-400 hover:text-slate-600 transition">
        <HelpCircle class="w-4 h-4" />
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="flex-1 flex items-center justify-center">
      <div class="w-full h-40 bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty / no data -->
    <div
      v-else-if="!heatmapData"
      class="flex-1 flex items-center justify-center"
    >
      <span class="text-xs text-slate-400 font-medium">No activity data available</span>
    </div>

    <!-- Heatmap grid -->
    <div v-else class="flex-1 mt-3 flex flex-col justify-between overflow-hidden">
      <!-- Time slot rows -->
      <div class="flex gap-2 flex-1">
        <!-- Y-axis labels (time slots) -->
        <div class="flex flex-col justify-between py-0.5">
          <span
            v-for="slot in timeSlots"
            :key="slot"
            class="text-[9px] font-bold text-slate-400 leading-none"
          >{{ slot }}</span>
        </div>

        <!-- Grid cells: rows = time slots, cols = days -->
        <div class="flex-1 flex flex-col gap-1.5">
          <div
            v-for="(slot, rowIdx) in timeSlots"
            :key="slot"
            class="flex gap-1.5 flex-1"
          >
            <div
              v-for="day in days"
              :key="day"
              class="flex-1 rounded-sm transition-colors duration-300"
              :style="{ background: cellColor(heatmapData[day]?.[rowIdx] ?? 0) }"
              :title="`${day} ${slot}: ${rawValues[day]?.[rowIdx] ?? 0} appts`"
            ></div>
          </div>
        </div>
      </div>

      <!-- X-axis day labels -->
      <div class="flex gap-1.5 mt-2 pl-7">
        <div
          v-for="day in days"
          :key="day"
          class="flex-1 text-center text-[10px] font-bold text-slate-400"
        >
          {{ day.charAt(0) }}
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div v-if="heatmapData" class="flex items-center gap-1.5 pt-2 flex-shrink-0">
      <span class="text-[9px] text-slate-400 font-medium">Low</span>
      <div class="flex gap-0.5">
        <div
          v-for="step in legendSteps"
          :key="step"
          class="w-3 h-2 rounded-sm"
          :style="{ background: cellColor(step) }"
        ></div>
      </div>
      <span class="text-[9px] text-slate-400 font-medium">High</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { HelpCircle } from 'lucide-vue-next';
import { useReportStore } from '../../stores/reportStore';

const store = useReportStore();

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
const legendSteps = [0, 20, 40, 60, 80, 100];

// Normalized heatmap (0-100 intensity per cell)
const heatmapData = computed(() => store.doctorActivityHeatmap?.heatmap ?? null);

// Raw counts for tooltips
const rawValues = computed(() => {
  // We'll use normalized as proxy; real raw would need separate field
  return heatmapData.value ?? {};
});

const timeSlots = computed(() =>
  store.doctorActivityHeatmap?.time_slots ?? ['8-10', '10-12', '12-14', '14-16', '16-18']
);

function cellColor(intensity) {
  // intensity: 0–100
  if (intensity === 0) return 'rgba(241,245,249,1)'; // slate-100
  const alpha = 0.15 + (intensity / 100) * 0.85;
  return `rgba(2,82,215,${alpha.toFixed(2)})`;
}
</script>
