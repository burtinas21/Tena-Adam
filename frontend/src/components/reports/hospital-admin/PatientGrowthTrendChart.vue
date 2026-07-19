<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between">
    <!-- Header Controls -->
    <div class="flex items-center justify-between pb-4">
      <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Patient & Appointment Trends</h3>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="h-[240px] flex items-center justify-center">
      <div class="w-full h-full bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state -->
    <div v-else-if="!labels.length" class="h-[240px] flex items-center justify-center">
      <span class="text-xs text-slate-400 font-medium">No trend data available</span>
    </div>

    <!-- Chart canvas -->
    <div v-else class="relative h-[240px] w-full mt-2 flex flex-col justify-between">
      <!-- Y-axis grid labels (auto-scaled to maxValue) -->
      <div class="absolute left-0 top-0 bottom-6 w-full flex flex-col justify-between text-[10px] font-bold text-slate-300 pointer-events-none select-none">
        <div v-for="tick in yTicks" :key="tick"
          class="border-b border-dashed border-slate-100 pb-1 w-full flex justify-between">
          <span>{{ tick }}</span>
        </div>
      </div>

      <!-- SVG polyline chart -->
      <div class="absolute bottom-6 left-10 right-2 top-4 overflow-hidden pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="patientGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#0252D7" stop-opacity="0.12" />
              <stop offset="100%" stop-color="#0252D7" stop-opacity="0.0" />
            </linearGradient>
            <linearGradient id="apptGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#10B981" stop-opacity="0.10" />
              <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
            </linearGradient>
          </defs>

          <!-- Patient registrations area -->
          <polygon
            :points="areaPoints(patientData, '#0252D7')"
            fill="url(#patientGradient)"
          />
          <!-- Patient registrations line -->
          <polyline
            :points="linePoints(patientData)"
            fill="none"
            stroke="#0252D7"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />

          <!-- Appointments line -->
          <polyline
            :points="linePoints(apptData)"
            fill="none"
            stroke="#10B981"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-dasharray="none"
          />
        </svg>
      </div>

      <!-- X-Axis Labels -->
      <div class="flex items-center justify-between pl-10 pr-2 pt-2 border-t border-slate-100 mt-auto text-[10px] font-bold text-slate-400">
        <span v-for="label in labels" :key="label">{{ label }}</span>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex items-center gap-5 mt-3 text-[11px] font-semibold text-slate-500">
      <div class="flex items-center gap-1.5">
        <span class="w-3 h-0.5 bg-[#0252D7] rounded inline-block"></span>
        <span>Patients</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-3 h-0.5 bg-emerald-500 rounded inline-block"></span>
        <span>Appointments</span>
      </div>
    </div>

    <!-- Export buttons -->
    <div class="mt-5 pt-3.5 border-t border-slate-50 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-500">
      <span class="text-slate-400">Export:</span>
      <button
        @click="$emit('export', 'pdf')"
        :disabled="exportLoading"
        class="flex items-center space-x-1.5 px-2.5 py-1 hover:bg-slate-50 rounded border border-slate-100 transition shadow-sm bg-white disabled:opacity-50"
      >
        <FileText class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-700">PDF</span>
      </button>
      <button
        @click="$emit('export', 'excel')"
        :disabled="exportLoading"
        class="flex items-center space-x-1.5 px-2.5 py-1 hover:bg-slate-50 rounded border border-slate-100 transition shadow-sm bg-white disabled:opacity-50"
      >
        <Sheet class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-700">Excel</span>
      </button>
      <button
        @click="$emit('export', 'csv')"
        :disabled="exportLoading"
        class="flex items-center space-x-1.5 px-2.5 py-1 hover:bg-slate-50 rounded border border-slate-100 transition shadow-sm bg-white disabled:opacity-50"
      >
        <FileSpreadsheet class="w-3.5 h-3.5 text-slate-400" />
        <span class="text-slate-700">CSV</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { FileText, Sheet, FileSpreadsheet } from 'lucide-vue-next';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  patientData: { type: Array, default: () => [] },
  apptData: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  exportLoading: { type: Boolean, default: false },
});

defineEmits(['export']);

const maxValue = computed(() =>
  Math.max(1, ...props.patientData, ...props.apptData)
);

// Generate 6 evenly-spaced Y-axis ticks
const yTicks = computed(() => {
  const max = maxValue.value;
  const step = Math.ceil(max / 5) || 1;
  return Array.from({ length: 6 }, (_, i) => (5 - i) * step);
});

function toSvgY(value) {
  return (1 - value / maxValue.value) * 90;
}

function toSvgX(index, total) {
  return total <= 1 ? 50 : (index / (total - 1)) * 100;
}

function linePoints(data) {
  if (!data.length) return '';
  return data
    .map((v, i) => `${toSvgX(i, data.length)},${toSvgY(v)}`)
    .join(' ');
}

function areaPoints(data) {
  if (!data.length) return '';
  const line = linePoints(data);
  const last = `${toSvgX(data.length - 1, data.length)},100`;
  return `${line} ${last} 0,100`;
}
</script>
