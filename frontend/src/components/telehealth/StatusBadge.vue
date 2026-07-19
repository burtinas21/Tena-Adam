<template>
  <span :class="badgeClass" class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide">
    <span :class="dotClass" class="w-1.5 h-1.5 rounded-full"></span>
    <span>{{ label }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    default: 'scheduled',
  },
});

const config = {
  scheduled: {
    label: 'Scheduled',
    badge: 'bg-slate-50 border border-slate-200 text-slate-500',
    dot: 'bg-slate-400',
  },
  active: {
    label: 'Active',
    badge: 'bg-blue-50 border border-blue-100 text-blue-600',
    dot: 'bg-blue-500 animate-pulse',
  },
  completed: {
    label: 'Completed',
    badge: 'bg-emerald-50 border border-emerald-100 text-emerald-600',
    dot: 'bg-emerald-500',
  },
  cancelled: {
    label: 'Cancelled',
    badge: 'bg-red-50 border border-red-100 text-red-500',
    dot: 'bg-red-400',
  },
};

const label = computed(() => config[props.status]?.label ?? props.status);
const badgeClass = computed(() => config[props.status]?.badge ?? '');
const dotClass = computed(() => config[props.status]?.dot ?? '');
</script>
