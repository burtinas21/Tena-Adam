<template>
  <div
    class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] space-y-3"
  >
    <!-- Header Block -->
    <div
      class="flex items-center justify-between border-b border-slate-50 pb-2"
    >
      <div class="flex items-center space-x-2 text-slate-700">
        <FileSpreadsheet class="w-4 h-4 text-blue-600" />
        <h4 class="text-xs font-bold uppercase tracking-wider">
          Consultation Notes
        </h4>
      </div>
      <div class="flex items-center space-x-1">
        <button
          class="p-1 hover:bg-slate-50 rounded text-slate-400 hover:text-slate-600 transition"
        >
          <ReceiptText class="w-3.5 h-3.5" />
        </button>
        <button
          class="p-1 hover:bg-slate-50 rounded text-slate-400 hover:text-slate-600 transition"
        >
          <Download class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- Note Text Area Content -->
    <textarea
      class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 placeholder-slate-400 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 min-h-[140px] resize-none"
      placeholder="Type clinical notes here... (e.g., Patient reports mild headache and fatigue starting 2 days ago...)"
    ></textarea>
  </div>
</template>

<script setup>
import { FileSpreadsheet, ReceiptText, Download } from "lucide-vue-next";
</script>
