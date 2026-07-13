<script setup>
const stats = [
  {
    label: "TODAY'S TOTAL",
    value: 142,
    color: "text-blue-600",
    bg: "bg-blue-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>',
  },
  {
    label: "CONFIRMED",
    value: 98,
    color: "text-emerald-600",
    bg: "bg-emerald-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
  },
  {
    label: "PENDING",
    value: 15,
    color: "text-amber-600",
    bg: "bg-amber-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
  },
  {
    label: "COMPLETED",
    value: 24,
    color: "text-purple-600",
    bg: "bg-purple-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
  },
  {
    label: "CANCELLED",
    value: 3,
    color: "text-rose-600",
    bg: "bg-rose-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 9-6 6"/><path d="m9 9 6 6"/><circle cx="12" cy="12" r="10"/></svg>',
  },
  {
    label: "NO-SHOW",
    value: 2,
    color: "text-gray-600",
    bg: "bg-gray-50",
    svg: '<svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
  },
];
</script>

<template>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div
      v-for="stat in stats"
      :key="stat.label"
      class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between"
    >
      <div class="flex justify-between items-start mb-2">
        <span
          class="text-[10px] font-bold text-gray-400 tracking-wider uppercase"
          >{{ stat.label }}</span
        >
        <span
          :class="[stat.bg, stat.color]"
          class="w-7 h-7 flex items-center justify-center rounded-lg"
          v-html="stat.svg"
        ></span>
      </div>
      <span class="text-3xl font-bold text-gray-900 tracking-tight">{{
        stat.value
      }}</span>
    </div>
  </div>
</template>
