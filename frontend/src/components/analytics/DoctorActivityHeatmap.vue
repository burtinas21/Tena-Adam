<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.01)] p-5 flex flex-col justify-between h-[300px]"
  >
    <!-- Header Block Component -->
    <div class="flex items-center justify-between pb-2">
      <div class="space-y-0.5">
        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">
          Doctor Activity
        </h3>
        <p class="text-[10px] text-slate-400 font-medium">
          Peak consultation hours (Platform-wide)
        </p>
      </div>
      <button class="p-1 text-slate-400 hover:text-slate-600 transition">
        <HelpCircle class="w-4 h-4" />
      </button>
    </div>

    <!-- Grid Heatmap Canvas Elements array -->
    <div class="flex-1 mt-4 flex flex-col justify-between">
      <div class="grid grid-rows-5 gap-1.5 flex-1 pb-2">
        <!-- Mock Density Grid Loops Layer mapped to hex opacity weights -->
        <div class="grid grid-cols-5 gap-1.5">
          <div class="bg-blue-100 rounded-sm aspect-square"></div>
          <div class="bg-[#0252D7] rounded-sm aspect-square"></div>
          <div class="bg-blue-600 rounded-sm aspect-square"></div>
          <div class="bg-blue-500 rounded-sm aspect-square"></div>
          <div class="bg-blue-400 rounded-sm aspect-square"></div>
        </div>
        <div class="grid grid-cols-5 gap-1.5">
          <div class="bg-blue-400 rounded-sm aspect-square"></div>
          <div class="bg-blue-500 rounded-sm aspect-square"></div>
          <div class="bg-[#0252D7] rounded-sm aspect-square"></div>
          <div class="bg-blue-600 rounded-sm aspect-square"></div>
          <div class="bg-blue-300 rounded-sm aspect-square"></div>
        </div>
        <div class="grid grid-cols-5 gap-1.5">
          <div class="bg-blue-600 rounded-sm aspect-square"></div>
          <div class="bg-[#0252D7] rounded-sm aspect-square"></div>
          <div class="bg-blue-400 rounded-sm aspect-square"></div>
          <div class="bg-blue-500 rounded-sm aspect-square"></div>
          <div class="bg-blue-200 rounded-sm aspect-square"></div>
        </div>
        <div class="grid grid-cols-5 gap-1.5">
          <div class="bg-blue-500 rounded-sm aspect-square"></div>
          <div class="bg-blue-400 rounded-sm aspect-square"></div>
          <div class="bg-[#0252D7] rounded-sm aspect-square"></div>
          <div class="bg-blue-300 rounded-sm aspect-square"></div>
          <div class="bg-blue-400 rounded-sm aspect-square"></div>
        </div>
        <div class="grid grid-cols-5 gap-1.5">
          <div class="bg-blue-300 rounded-sm aspect-square"></div>
          <div class="bg-blue-500 rounded-sm aspect-square"></div>
          <div class="bg-blue-600 rounded-sm aspect-square"></div>
          <div class="bg-[#0252D7] rounded-sm aspect-square"></div>
          <div class="bg-blue-200 rounded-sm aspect-square"></div>
        </div>
      </div>

      <!-- Base X-Axis Weekday Indicators Label Row -->
      <div
        class="grid grid-cols-5 gap-1.5 text-center text-[10px] font-bold text-slate-400 pt-1"
      >
        <span>M</span>
        <span>T</span>
        <span>W</span>
        <span>T</span>
        <span>F</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { HelpCircle } from "lucide-vue-next";
</script>
