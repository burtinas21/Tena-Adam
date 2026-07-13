<template>
  <div class="bg-white border border-gray-200/90 rounded-2xl p-5 shadow-sm">
    <h4 class="text-sm font-bold text-gray-800 tracking-tight">Active Consultations</h4>
    <p class="text-[10px] text-gray-400 font-medium mt-0.5">Real-time status of current sessions</p>

    <!-- Circular progress ring -->
    <div class="flex justify-center items-center my-6 relative">
      <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="transparent" />
        <circle cx="50" cy="50" r="40" stroke="#0d9488" stroke-width="8" fill="transparent"
          stroke-dasharray="251.2"
          :stroke-dashoffset="dashOffset"
          stroke-linecap="round"
          style="transition: stroke-dashoffset 0.5s ease" />
      </svg>
      <div class="absolute text-center">
        <span class="text-2xl font-black text-gray-900 block tracking-tight">{{ activeCount }}</span>
        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wide">Active</span>
      </div>
    </div>

    <!-- Stats footer -->
    <div class="grid grid-cols-2 gap-2 mt-2 text-center text-xs font-semibold">
      <div class="bg-slate-50 border border-gray-100 rounded-xl p-2">
        <span class="text-[10px] font-medium text-gray-400 block">Avg Session</span>
        <span class="text-gray-700 font-bold block mt-0.5">{{ avgSession }} min</span>
      </div>
      <div class="bg-blue-50/40 border border-blue-100/50 rounded-xl p-2">
        <span class="text-[10px] font-medium text-gray-400 block">Overtime</span>
        <span class="font-bold block mt-0.5" :class="overtime > 0 ? 'text-rose-600' : 'text-emerald-600'">
          {{ overtime }} {{ overtime === 1 ? 'room' : 'rooms' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  entries: { type: Array, default: () => [] },
});

const activeCount = computed(() =>
  props.entries.filter((e) => e.status === "in_consultation").length
);

// Circumference = 2πr = 251.2, offset = circumference * (1 - ratio)
const maxRooms = 20;
const dashOffset = computed(() => {
  const ratio = Math.min(activeCount.value / maxRooms, 1);
  return 251.2 * (1 - ratio);
});

const avgSession = computed(() => {
  const done = props.entries.filter((e) => e.started_at && e.ended_at);
  if (!done.length) return 0;
  const totalMs = done.reduce((acc, e) => {
    return acc + (new Date(e.ended_at) - new Date(e.started_at));
  }, 0);
  return Math.round(totalMs / done.length / 60000);
});

// Count consultations running > 30 minutes
const overtime = computed(() => {
  const now = Date.now();
  return props.entries.filter((e) => {
    if (e.status !== "in_consultation" || !e.started_at) return false;
    return (now - new Date(e.started_at).getTime()) > 30 * 60 * 1000;
  }).length;
});
</script>
