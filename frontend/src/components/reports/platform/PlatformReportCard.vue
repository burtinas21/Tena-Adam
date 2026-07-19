<template>
  <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-[0_1px_2px_rgba(0,0,0,0.02)] flex flex-col justify-between h-[120px] relative overflow-hidden">
    <!-- Loading state -->
    <template v-if="loading">
      <div class="h-3 w-24 bg-slate-100 rounded animate-pulse" />
      <div class="mt-auto h-7 w-20 bg-slate-100 rounded animate-pulse" />
    </template>

    <template v-else>
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-500 tracking-tight leading-tight">{{ title }}</span>
        <div :class="['w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0', iconBg]">
          <component :is="icon" class="w-4 h-4" :class="iconColor" />
        </div>
      </div>

      <div class="mt-auto">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ value }}</h3>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ sub }}</p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: String,
  value: String,
  sub: String,
  icon: Object,
  color: {
    type: String,
    default: 'blue',
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const colorMap = {
  blue:   { bg: 'bg-blue-50',   text: 'text-blue-600' },
  emerald:{ bg: 'bg-emerald-50',text: 'text-emerald-600' },
  green:  { bg: 'bg-green-50',  text: 'text-green-600' },
  purple: { bg: 'bg-purple-50', text: 'text-purple-600' },
  amber:  { bg: 'bg-amber-50',  text: 'text-amber-600' },
  red:    { bg: 'bg-red-50',    text: 'text-red-500' },
  rose:   { bg: 'bg-rose-50',   text: 'text-rose-500' },
  slate:  { bg: 'bg-slate-100', text: 'text-slate-500' },
};

const iconBg = computed(() => (colorMap[props.color] ?? colorMap.blue).bg);
const iconColor = computed(() => (colorMap[props.color] ?? colorMap.blue).text);
</script>
