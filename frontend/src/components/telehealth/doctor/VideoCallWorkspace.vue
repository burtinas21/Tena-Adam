<template>
  <div
    class="bg-[#0B1329] rounded-2xl flex-1 flex flex-col justify-between p-5 relative overflow-hidden shadow-inner border border-slate-900"
  >
    <!-- Top Floating Meta Rows -->
    <div class="flex items-center justify-between z-10 pointer-events-none">
      <!-- Status Running counter badge -->
      <div
        class="flex items-center space-x-2 px-3 py-1.5 bg-slate-900/80 backdrop-blur-md rounded-xl border border-slate-800/80 shadow-md"
      >
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-bold text-white tracking-wide font-mono"
          >Sarah Jenkins</span
        >
        <span class="text-xs text-slate-400 font-medium ml-1">00:14:32</span>
      </div>

      <!-- Network Bitrate Bars -->
      <div
        class="p-2 bg-slate-900/80 backdrop-blur-md rounded-xl border border-slate-800/80 shadow-md"
      >
        <Signal class="w-4 h-4 text-emerald-400" />
      </div>
    </div>

    <!-- Main Subject Frame (Simulating patient device layout matching screenshot template backgrounds) -->
    <div
      class="absolute inset-0 flex items-center justify-center p-8 bg-[#1E293B]/20"
    >
      <div
        class="w-full max-w-[460px] aspect-[4/5] bg-white rounded-3xl p-4 shadow-2xl border border-slate-200/50 flex flex-col justify-between relative"
      >
        <!-- Tablet style simulated frame notches -->
        <div class="flex justify-center space-x-1.5 pt-1 opacity-40">
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
        </div>

        <!-- Live Picture Display Canvas -->
        <div
          class="flex-1 my-4 bg-slate-100 rounded-2xl overflow-hidden relative border border-slate-100"
        >
          <img
            src="https://unsplash.com"
            alt="Patient Stream Feed"
            class="w-full h-full object-cover filter contrast-[1.01]"
          />
        </div>

        <div
          class="w-10 h-1 bg-slate-200 mx-auto rounded-full pb-1 opacity-60"
        ></div>
      </div>
    </div>

    <!-- Overlay User Mini PiP View Box (Bottom Right) -->
    <div
      class="absolute bottom-5 right-5 w-36 h-44 bg-slate-900 rounded-xl overflow-hidden border-2 border-slate-700/60 shadow-2xl z-20"
    >
      <img
        src="https://unsplash.com"
        alt="Doctor Feed Picture"
        class="w-full h-full object-cover"
      />
      <div
        class="absolute bottom-1.5 left-2 px-1.5 py-0.5 bg-slate-900/80 rounded border border-slate-800"
      >
        <span class="text-[9px] font-bold text-slate-300"
          >You (Dr. Jameson)</span
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { Signal } from "lucide-vue-next";
</script>
