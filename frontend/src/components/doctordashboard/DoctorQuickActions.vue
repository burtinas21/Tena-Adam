<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <h2 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h2>

    <div class="grid grid-cols-2 gap-3">
      <RouterLink
        to="/doctor/medicalencounter"
        class="border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all"
      >
        <Stethoscope class="w-5 h-5 text-blue-600 group-hover:scale-110 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Start Consult</span>
      </RouterLink>

      <RouterLink
        to="/doctor/appointments"
        class="border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all"
      >
        <FolderHeart class="w-5 h-5 text-blue-600 group-hover:scale-110 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Appointments</span>
      </RouterLink>

      <RouterLink
        to="/doctor/prescription"
        class="border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all"
      >
        <FileCode class="w-5 h-5 text-blue-600 group-hover:scale-110 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Create Rx</span>
      </RouterLink>

      <RouterLink
        to="/doctor/telehealth"
        class="border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group transition-all"
      >
        <Video class="w-5 h-5 text-blue-600 group-hover:scale-110 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Join Telemed</span>
      </RouterLink>

      <RouterLink
        to="/doctor/queue"
        class="col-span-2 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/20 p-3 rounded-xl flex items-center justify-center gap-x-2 group transition-all"
      >
        <Users class="w-4 h-4 text-blue-600 group-hover:scale-110 transition-transform" />
        <span class="text-xs font-bold text-gray-700">Queue Management</span>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
import { Stethoscope, FolderHeart, FileCode, Video, Users } from "lucide-vue-next";
</script>
