<template>
  <div class="flex items-center gap-2">
    <button
      @click="$emit('export', 'pdf')"
      :disabled="loading"
      class="flex items-center space-x-1.5 px-2.5 py-1.5 hover:bg-slate-50 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 transition shadow-sm bg-white disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <FileText class="w-3.5 h-3.5 text-red-400" />
      <span>PDF</span>
    </button>
    <button
      @click="$emit('export', 'excel')"
      :disabled="loading"
      class="flex items-center space-x-1.5 px-2.5 py-1.5 hover:bg-slate-50 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 transition shadow-sm bg-white disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <Sheet class="w-3.5 h-3.5 text-emerald-500" />
      <span>Excel</span>
    </button>
    <button
      @click="$emit('export', 'csv')"
      :disabled="loading"
      class="flex items-center space-x-1.5 px-2.5 py-1.5 hover:bg-slate-50 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 transition shadow-sm bg-white disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <FileSpreadsheet class="w-3.5 h-3.5 text-blue-400" />
      <span>CSV</span>
    </button>
  </div>
</template>

<script setup>
import { FileText, Sheet, FileSpreadsheet } from 'lucide-vue-next';

defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['export']);
</script>
