<template>
  <div class="flex items-center justify-between px-3 py-2.5 border-t border-gray-100 dark:border-slate-700 text-xs text-gray-500 dark:text-slate-400">
    <!-- Info -->
    <span class="hidden sm:block">
      {{ from }}–{{ to }} of {{ total }}
    </span>
    <span class="block sm:hidden">{{ page }}/{{ totalPages }}</span>

    <!-- Controls -->
    <div class="flex items-center gap-1">
      <button
        @click="$emit('prev')"
        :disabled="page <= 1"
        class="px-2 py-1 rounded border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700
               disabled:opacity-30 disabled:cursor-not-allowed transition text-[11px] font-semibold"
      >‹ Prev</button>

      <!-- Page pills — show max 5 -->
      <template v-for="p in visiblePages" :key="p">
        <button
          v-if="p !== '…'"
          @click="$emit('goTo', p)"
          :class="p === page
            ? 'bg-[#004795] text-white border-[#004795]'
            : 'bg-white dark:bg-slate-800 text-gray-600 dark:text-slate-300 border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700'"
          class="min-w-[28px] px-1.5 py-1 rounded border text-[11px] font-semibold transition"
        >{{ p }}</button>
        <span v-else class="px-1 text-gray-400">…</span>
      </template>

      <button
        @click="$emit('next')"
        :disabled="page >= totalPages"
        class="px-2 py-1 rounded border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700
               disabled:opacity-30 disabled:cursor-not-allowed transition text-[11px] font-semibold"
      >Next ›</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  page:       { type: Number, required: true },
  totalPages: { type: Number, required: true },
  total:      { type: Number, required: true },
  perPage:    { type: Number, required: true },
});
defineEmits(["prev", "next", "goTo"]);

const from = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1);
const to   = computed(() => Math.min(props.page * props.perPage, props.total));

const visiblePages = computed(() => {
  const t = props.totalPages;
  const c = props.page;
  if (t <= 7) return Array.from({ length: t }, (_, i) => i + 1);
  if (c <= 4) return [1, 2, 3, 4, 5, "…", t];
  if (c >= t - 3) return [1, "…", t - 4, t - 3, t - 2, t - 1, t];
  return [1, "…", c - 1, c, c + 1, "…", t];
});
</script>
