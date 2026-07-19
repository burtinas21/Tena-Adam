<template>
  <div
    class="bg-[#1E293B] border-t border-slate-800 px-6 py-4 flex items-center justify-between"
  >
    <!-- Left/Center Group Controls -->
    <div class="flex items-center space-x-2.5 mx-auto">
      <button
        class="p-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white rounded-xl transition shadow-sm"
      >
        <Mic class="w-4 h-4" />
      </button>
      <button
        class="p-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white rounded-xl transition shadow-sm"
      >
        <Video class="w-4 h-4" />
      </button>
      <button
        class="p-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white rounded-xl transition shadow-sm"
      >
        <MonitorUp class="w-4 h-4" />
      </button>

      <div class="w-px h-5 bg-slate-700 mx-1.5"></div>

      <button
        class="p-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white rounded-xl transition shadow-sm"
      >
        <FileText class="w-4 h-4" />
      </button>
      <button
        class="p-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white rounded-xl transition shadow-sm"
      >
        <Smile class="w-4 h-4" />
      </button>

      <div class="w-px h-5 bg-slate-700 mx-1.5"></div>

      <!-- End Call Red Action Button -->
      <button
        class="flex items-center space-x-2 px-5 py-2.5 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 shadow-md shadow-rose-600/10 active:scale-[0.98] transition"
      >
        <PhoneOff class="w-4 h-4" />
        <span>End Call</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import {
  Mic,
  Video,
  MonitorUp,
  FileText,
  Smile,
  PhoneOff,
} from "lucide-vue-next";
</script>
