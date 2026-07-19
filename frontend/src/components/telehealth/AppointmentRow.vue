<template>
  <div
    class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-white border rounded-xl transition-all duration-200"
    :class="
      status === 'Ready to Join'
        ? 'border-[#0252D7] ring-1 ring-[#0252D7]'
        : 'border-gray-200'
    "
  >
    <!-- Left side: Patient Details -->
    <div class="flex items-center space-x-4">
      <div
        class="w-12 h-12 flex items-center justify-center rounded-full bg-[#F1F5F9] text-gray-600 font-semibold text-sm tracking-wider"
      >
        {{ initials }}
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <span class="text-xs text-gray-400">Patient:</span>
          <span class="text-sm font-semibold text-gray-800">{{
            patientName
          }}</span>
        </div>
        <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
          <div class="flex items-center space-x-1.5">
            <User class="w-3.5 h-3.5 text-gray-400" />
            <span>{{ doctorName }}</span>
          </div>
          <div class="flex items-center space-x-1.5">
            <Building2 class="w-3.5 h-3.5 text-gray-400" />
            <span>{{ location }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right side: Actions & Status metadata -->
    <div class="flex flex-wrap items-center gap-4 mt-4 md:mt-0">
      <!-- Time Slot -->
      <div
        class="flex items-center space-x-1.5 text-xs text-gray-700 font-medium"
      >
        <Clock class="w-4 h-4 text-gray-400" />
        <span>{{ timeSlot }}</span>
      </div>

      <!-- Platform Badge -->
      <div
        class="flex items-center space-x-1.5 px-2.5 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600"
      >
        <span
          class="w-2 h-2 rounded-full"
          :class="platform === 'Google Meet' ? 'bg-emerald-500' : 'bg-blue-400'"
        ></span>
        <span>{{ platform }}</span>
      </div>

      <!-- Status Badge -->
      <span
        class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border"
        :class="
          status === 'Ready to Join'
            ? 'bg-[#0252D7] text-white border-transparent'
            : 'bg-indigo-50 text-indigo-600 border-indigo-100'
        "
      >
        {{ status }}
      </span>

      <!-- Action Button matching image -->
      <button
        v-if="status === 'Ready to Join'"
        class="flex items-center space-x-2 px-5 py-2 bg-[#0252D7] text-white text-xs font-semibold rounded-lg hover:bg-blue-700 shadow-sm transition"
      >
        <Video class="w-4 h-4" />
        <span>Join Session</span>
      </button>

      <button
        v-else
        class="flex items-center space-x-2 px-5 py-2 bg-white border border-gray-200 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-50 transition"
      >
        <Eye class="w-4 h-4 text-gray-400" />
        <span>Details</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { User, Building2, Clock, Video, Eye } from "lucide-vue-next";

defineProps({
  patientName: String,
  initials: String,
  doctorName: String,
  location: String,
  timeSlot: String,
  platform: String,
  status: String,
});
</script>
