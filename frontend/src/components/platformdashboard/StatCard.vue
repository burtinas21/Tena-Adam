<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between min-h-[110px]">
    <!-- Icon + Trend -->
    <div class="flex items-center justify-between">
      <div :class="['w-9 h-9 rounded-lg flex items-center justify-center', iconBgColor]">
        <component :is="icon" class="w-5 h-5" :class="iconColor" />
      </div>
      <div
        v-if="!loading"
        :class="['flex items-center gap-x-1 px-2 py-0.5 rounded-full text-xs font-semibold', trendBgColor, trendTextColor]"
      >
        <TrendingUp v-if="!trendDown" class="w-3 h-3" />
        <TrendingDown v-else class="w-3 h-3" />
        {{ trend }}
      </div>
    </div>

    <!-- Label + Value -->
    <div class="mt-4">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ title }}</p>
      <div v-if="loading" class="h-7 w-20 bg-gray-100 rounded mt-1 animate-pulse"></div>
      <h3 v-else class="text-2xl font-bold text-gray-800 mt-1">{{ value }}</h3>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { TrendingUp, TrendingDown } from "lucide-vue-next";

const props = defineProps({
  title:          String,
  value:          [String, Number],
  trend:          String,
  icon:           Object,
  iconColor:      { type: String, default: "text-blue-600" },
  iconBgColor:    { type: String, default: "bg-blue-50" },
  trendTextColor: { type: String, default: "text-emerald-600" },
  trendBgColor:   { type: String, default: "bg-emerald-50" },
  loading:        { type: Boolean, default: false },
});

const trendDown = computed(() => String(props.trend ?? "").startsWith("-"));
</script>
