<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <h2 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      <button class="border border-gray-100 hover:border-blue-200 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all">
        <Stethoscope class="w-5 h-5 text-blue-600 group-hover:scale-105 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Start Consult</span>
      </button>

      <button class="border border-gray-100 hover:border-blue-200 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all">
        <FolderHeart class="w-5 h-5 text-blue-600 group-hover:scale-105 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Open Record</span>
      </button>

      <button class="border border-gray-100 hover:border-blue-200 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all">
        <FileCode class="w-5 h-5 text-blue-600 group-hover:scale-105 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Create Rx</span>
      </button>

      <button class="border border-gray-100 hover:border-blue-200 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all">
        <Video class="w-5 h-5 text-blue-600 group-hover:scale-105 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Join Telemed</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { Stethoscope, FolderHeart, FileCode, Video } from 'lucide-vue-next'
</script>
