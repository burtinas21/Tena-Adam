<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_0_rgba(0,0,0,0.01)] p-5 flex flex-col h-[360px]">
    <div class="flex items-center justify-between pb-2 flex-shrink-0">
      <div>
        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Patient Growth</h3>
        <div class="flex items-center gap-3 mt-1">
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-sm bg-[#0252D7] opacity-50 inline-block"></span>
            <span class="text-[10px] font-bold text-slate-500">Registrations</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-4 h-0.5 bg-[#0252D7] inline-block rounded"></span>
            <span class="text-[10px] font-bold text-slate-500">Trend</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="w-full h-full bg-slate-100 rounded-lg animate-pulse" />
    </div>

    <!-- Empty state: no labels at all -->
    <div v-else-if="!displayLabels.length" class="flex-1 flex items-center justify-center">
      <span class="text-xs text-slate-400 font-medium">No trend data available</span>
    </div>

    <!-- Chart (renders even if all values are 0) -->
    <div v-else class="flex-1 flex flex-col min-h-0 mt-3">
      <!-- Chart drawing area -->
      <div class="relative flex-1 min-h-0 border-b border-slate-100">

        <!-- Y-axis gridlines -->
        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none px-4">
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="border-t border-dashed border-slate-100 w-full"></div>
          <div class="w-full"></div>
        </div>

        <!-- Bar columns -->
        <div class="absolute inset-x-4 bottom-0 top-0 flex items-end gap-1.5">
          <div
            v-for="(val, i) in displayData"
            :key="i"
            class="flex-1 rounded-t transition-all duration-700 ease-out"
            :style="{
              minHeight: '4px',
              height: hasData ? barHeight(val) + '%' : '4px',
              background: `rgba(2,82,215,${0.15 + (i / Math.max(displayData.length - 1, 1)) * 0.55})`,
            }"
          />
        </div>

        <!-- SVG trend line -->
        <svg
          v-if="displayData.length >= 2"
          class="absolute inset-x-4 bottom-0 top-0 w-[calc(100%-2rem)] h-full pointer-events-none"
          viewBox="0 0 100 100"
          preserveAspectRatio="none"
        >
          <!-- Gradient fill under line -->
          <defs>
            <linearGradient id="lineGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#0252D7" stop-opacity="0.12"/>
              <stop offset="100%" stop-color="#0252D7" stop-opacity="0"/>
            </linearGradient>
          </defs>
          <path
            :d="svgAreaPath"
            fill="url(#lineGrad)"
          />
          <polyline
            :points="svgLinePoints"
            fill="none"
            stroke="#0252D7"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <circle
            v-for="(pt, i) in svgPoints"
            :key="i"
            :cx="pt.x"
            :cy="pt.y"
            r="2"
            fill="#0252D7"
          />
        </svg>
      </div>

      <!-- X-axis labels -->
      <div class="flex items-center justify-between pt-2 text-[10px] font-bold text-slate-400 px-4 flex-shrink-0">
        <span v-for="label in displayLabels" :key="label">{{ label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  data:   { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const displayLabels = computed(() => props.labels.slice(-6));
const displayData   = computed(() => props.data.slice(-6));

const hasData = computed(() => displayData.value.some(v => v > 0));
const maxVal  = computed(() => Math.max(1, ...displayData.value));

function barHeight(val) {
  if (!hasData.value) return 4;
  return Math.max(4, Math.round((val / maxVal.value) * 88));
}

// SVG coordinates (viewBox 0 0 100 100, preserveAspectRatio="none")
const svgPoints = computed(() => {
  const d = displayData.value;
  if (!d.length) return [];
  const top = hasData.value ? 10 : 95;
  return d.map((v, i) => ({
    x: d.length === 1 ? 50 : (i / (d.length - 1)) * 96 + 2,
    y: hasData.value ? 92 - ((v / maxVal.value) * 80) : top,
  }));
});

const svgLinePoints = computed(() =>
  svgPoints.value.map(p => `${p.x},${p.y}`).join(' ')
);

const svgAreaPath = computed(() => {
  const pts = svgPoints.value;
  if (!pts.length) return '';
  const line = pts.map(p => `${p.x},${p.y}`).join(' L ');
  const first = pts[0];
  const last  = pts[pts.length - 1];
  return `M ${first.x},100 L ${line} L ${last.x},100 Z`;
});
</script>
