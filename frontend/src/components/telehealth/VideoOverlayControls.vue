<template>
  <div
    class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center space-x-3 px-4 py-2.5 bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-700/50 shadow-2xl"
  >
    <button
      class="p-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-xl transition duration-200"
    >
      <Mic class="w-5 h-5" />
    </button>
    <button
      class="p-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-xl transition duration-200"
    >
      <Video class="w-5 h-5" />
    </button>
    <button
      class="p-2.5 text-slate-300 hover:bg-slate-800 hover:text-white rounded-xl transition duration-200"
    >
      <FileText class="w-5 h-5" />
    </button>
    <div class="w-px h-5 bg-slate-700 mx-1"></div>
    <button
      class="p-2.5 bg-rose-600 text-white hover:bg-rose-700 rounded-xl transition duration-200 shadow-lg shadow-rose-600/20"
    >
      <PhoneOff class="w-5 h-5" />
    </button>
  </div>
</template>

<script setup>
import { Mic, Video, FileText, PhoneOff } from "lucide-vue-next";
</script>
