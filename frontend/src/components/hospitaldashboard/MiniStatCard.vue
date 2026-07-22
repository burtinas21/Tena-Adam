<template>
  <div
    :class="[
      'p-4 rounded-xl border flex flex-col justify-between shadow-sm min-h-[105px] transition-all',
      isAlert ? 'bg-red-50 border-red-200' : 'bg-white border-gray-100 hover:border-gray-200',
    ]"
  >
    <!-- Label + Icon -->
    <div class="flex items-start justify-between">
      <span
        :class="[
          'text-[11px] font-bold tracking-wider uppercase',
          isAlert ? 'text-red-700' : 'text-gray-400',
        ]"
      >{{ label }}</span>
      <component
        :is="icon"
        :class="['w-4 h-4', isAlert ? 'text-red-600' : 'text-[#0A3D80]']"
      />
    </div>

    <!-- Value + badge -->
    <div class="flex items-baseline justify-between mt-2">
      <!-- Skeleton while loading -->
      <div v-if="loading" class="h-7 w-16 bg-gray-100 rounded animate-pulse"></div>
      <h3
        v-else
        :class="[
          'text-2xl font-bold tracking-tight',
          isAlert ? 'text-red-900' : 'text-gray-800',
        ]"
      >{{ displayValue }}</h3>

      <span
        v-if="trend && !loading"
        :class="['text-[11px] font-semibold', trendColor]"
      >{{ trend }}</span>

      <span
        v-if="isAlert && !loading"
        class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide"
      >High</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  label:      String,
  value:      [String, Number],
  icon:       Object,
  trend:      String,
  trendColor: { type: String, default: "text-emerald-500" },
  isAlert:    { type: Boolean, default: false },
  loading:    { type: Boolean, default: false },
});

const displayValue = computed(() => {
  const v = props.value;
  if (v === null || v === undefined) return "—";
  const n = Number(v);
  if (isNaN(n)) return String(v);
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)}M`;
  if (n >= 1000)      return `${(n / 1000).toFixed(1)}k`;
  return n.toLocaleString();
});
</script>
