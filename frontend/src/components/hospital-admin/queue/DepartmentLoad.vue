<template>
  <div class="bg-white border border-gray-200/90 rounded-2xl p-5 shadow-sm">
    <h4 class="text-sm font-bold text-gray-800 tracking-tight mb-4">Department Load</h4>

    <div v-if="!departments.length" class="py-4 text-center text-xs text-gray-400">
      No department data available
    </div>

    <div v-else class="space-y-3">
      <div v-for="dept in departments" :key="dept.name"
        class="flex items-center justify-between border-b border-gray-50 pb-2.5 last:border-0 last:pb-0">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg flex items-center justify-center border bg-blue-50 border-blue-100 text-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-gray-700">{{ dept.name }}</span>
        </div>
        <div class="text-right">
          <span class="text-[11px] font-bold text-amber-600">{{ dept.waiting }} waiting</span>
          <span class="text-[10px] text-gray-400 ml-2">/ {{ dept.total }} total</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  entries: { type: Array, default: () => [] },
});

const departments = computed(() => {
  const map = {};
  props.entries.forEach((e) => {
    const name = e._department ?? "Unknown";
    if (!map[name]) map[name] = { name, total: 0, waiting: 0 };
    map[name].total++;
    if (e.status === "waiting") map[name].waiting++;
  });
  return Object.values(map).sort((a, b) => b.waiting - a.waiting);
});
</script>
