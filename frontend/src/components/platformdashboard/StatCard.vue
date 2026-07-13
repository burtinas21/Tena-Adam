<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between"
  >
    <!-- Top row: Icon and Trend Badge -->
    <div class="flex items-center justify-between">
      <div
        :class="[
          'w-9 h-9 rounded-lg flex items-center justify-center',
          iconBgColor,
        ]"
      >
        <component :is="icon" class="w-5 h-5" :class="iconColor" />
      </div>
      <!-- Trend Percentage Indicator Badge -->
      <div
        :class="[
          'flex items-center gap-x-1 px-2 py-0.5 rounded-full text-xs font-semibold',
          trendBgColor,
          trendTextColor,
        ]"
      >
        <TrendingUp class="w-3 h-3" />
        {{ trend }}
      </div>
    </div>

    <!-- Bottom row: Labels and Figures -->
    <div class="mt-4">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">
        {{ title }}
      </p>
      <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ value }}</h3>
    </div>
  </div>
</template>

<script setup>
import { TrendingUp } from "lucide-vue-next";

defineProps({
  title: String,
  value: String,
  trend: String,
  icon: Object,
  iconColor: { type: String, default: "text-blue-600" },
  iconBgColor: { type: String, default: "bg-blue-50" },
  trendTextColor: { type: String, default: "text-emerald-600" },
  trendBgColor: { type: String, default: "bg-emerald-50" },
});
</script>
