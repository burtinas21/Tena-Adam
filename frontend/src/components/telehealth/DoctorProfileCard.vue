<template>
  <div
    class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm space-y-4"
  >
    <div class="flex items-center space-x-3.5">
      <img
        src="https://unsplash.com"
        alt="Doctor avatar"
        class="w-12 h-12 rounded-xl object-cover ring-2 ring-gray-50 shadow-sm"
      />
      <div>
        <h3 class="text-base font-bold text-gray-900 tracking-tight">
          {{ name }}
        </h3>
        <div
          class="flex items-center space-x-1.5 mt-0.5 text-xs text-gray-500 font-medium"
        >
          <Stethoscope class="w-3.5 h-3.5 text-blue-500" />
          <span>{{ specialty }}</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3 pt-1">
      <div
        class="bg-blue-50/50 border border-blue-100/50 rounded-xl p-3 flex flex-col justify-center"
      >
        <span
          class="text-[10px] uppercase font-bold text-gray-400 tracking-wider"
          >Scheduled Time</span
        >
        <div
          class="flex items-center space-x-1.5 mt-1 text-sm font-semibold text-gray-800"
        >
          <Clock class="w-4 h-4 text-blue-600" />
          <span>{{ time }}</span>
        </div>
      </div>
      <div
        class="bg-indigo-50/40 border border-indigo-100/50 rounded-xl p-3 flex flex-col justify-center"
      >
        <span
          class="text-[10px] uppercase font-bold text-gray-400 tracking-wider"
          >Duration</span
        >
        <div
          class="flex items-center space-x-1.5 mt-1 text-sm font-semibold text-gray-800"
        >
          <Hourglass class="w-4 h-4 text-indigo-600" />
          <span>{{ duration }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Stethoscope, Clock, Hourglass } from "lucide-vue-next";

defineProps({
  name: String,
  specialty: String,
  time: String,
  duration: String,
});
</script>
