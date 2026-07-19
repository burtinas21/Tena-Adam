<template>
  <div
    class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] space-y-3.5"
  >
    <div
      class="flex items-center space-x-2 text-slate-700 border-b border-slate-50 pb-2"
    >
      <Pill class="w-4 h-4 text-blue-600" />
      <h4 class="text-xs font-bold uppercase tracking-wider">
        Active Medications
      </h4>
    </div>

    <div class="space-y-3">
      <!-- Item Row 1 -->
      <div
        class="flex items-start justify-between p-2.5 bg-slate-50/50 border border-slate-100 rounded-lg group"
      >
        <div>
          <h5 class="text-xs font-bold text-slate-800">Lisinopril 10mg</h5>
          <p class="text-[11px] text-slate-400 font-medium mt-0.5">
            1 tab daily (Hypertension)
          </p>
        </div>
        <button
          class="p-1 hover:bg-white border border-transparent hover:border-slate-200 rounded text-slate-400 hover:text-slate-600 transition shadow-sm"
        >
          <Pencil class="w-3 h-3" />
        </button>
      </div>

      <!-- Item Row 2 -->
      <div
        class="flex items-start justify-between p-2.5 bg-slate-50/50 border border-slate-100 rounded-lg group"
      >
        <div>
          <h5 class="text-xs font-bold text-slate-800">Atorvastatin 20mg</h5>
          <p class="text-[11px] text-slate-400 font-medium mt-0.5">
            1 tab at bedtime (Hyperlipidemia)
          </p>
        </div>
        <button
          class="p-1 hover:bg-white border border-transparent hover:border-slate-200 rounded text-slate-400 hover:text-slate-600 transition shadow-sm"
        >
          <Pencil class="w-3 h-3" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Pill, Pencil } from "lucide-vue-next";
</script>
