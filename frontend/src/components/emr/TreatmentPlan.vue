<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
  >
    <!-- Header -->
    <div
      class="flex items-center space-x-2.5 px-6 py-4 bg-slate-50/50 border-b border-slate-100"
    >
      <ClipboardList class="w-4 h-4 text-blue-600" />
      <h3 class="text-sm font-bold text-slate-900">Treatment Plan</h3>
    </div>

    <!-- Step Items Container -->
    <div class="p-6 space-y-5">
      <!-- Step 1 -->
      <div class="flex items-start space-x-4">
        <div
          class="w-6 h-6 flex items-center justify-center bg-cyan-50 border border-cyan-100 text-cyan-600 font-bold text-xs rounded-full shrink-0 mt-0.5 shadow-sm"
        >
          1
        </div>
        <div>
          <h4 class="text-sm font-bold text-slate-800">
            Medication Management
          </h4>
          <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
            Begin prescribed Dextromethorphan-Guaifenesin syrup tonight.
            Continue Acetaminophen 500mg every 6 hours as needed for fever or
            aches.
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="flex items-start space-x-4">
        <div
          class="w-6 h-6 flex items-center justify-center bg-cyan-50 border border-cyan-100 text-cyan-600 font-bold text-xs rounded-full shrink-0 mt-0.5 shadow-sm"
        >
          2
        </div>
        <div>
          <h4 class="text-sm font-bold text-slate-800">Hydration & Diet</h4>
          <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
            Increase water intake to at least 2.5 liters per day. Consume warm
            liquids (tea, broth) to soothe the throat.
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="flex items-start space-x-4">
        <div
          class="w-6 h-6 flex items-center justify-center bg-cyan-50 border border-cyan-100 text-cyan-600 font-bold text-xs rounded-full shrink-0 mt-0.5 shadow-sm"
        >
          3
        </div>
        <div>
          <h4 class="text-sm font-bold text-slate-800">Rest</h4>
          <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
            Strict bed rest for the next 48 hours. Avoid strenuous physical
            activity until symptoms fully resolve.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ClipboardList } from "lucide-vue-next";
</script>
