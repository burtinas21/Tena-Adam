<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <h2 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h2>

    <div class="flex flex-col gap-y-2.5">
      <RouterLink
        to="/hospital-admin/departments"
        class="w-full bg-[#004795] hover:bg-[#003670] text-white font-semibold text-xs py-3 px-4 rounded-lg flex items-center justify-center gap-x-2 transition shadow-sm"
      >
        <PlusCircle class="w-4 h-4" />
        Add New Department
      </RouterLink>

      <RouterLink
        to="/hospital-admin/doctors"
        class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2.5 px-4 rounded-lg flex items-center justify-center gap-x-2 transition"
      >
        <UserPlus2 class="w-4 h-4 text-gray-500" />
        Register Doctor
      </RouterLink>

      <RouterLink
        to="/hospital-admin/appointments"
        class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2.5 px-4 rounded-lg flex items-center justify-center gap-x-2 transition"
      >
        <CalendarRange class="w-4 h-4 text-gray-500" />
        Manage Schedule
      </RouterLink>

      <RouterLink
        to="/hospital-admin/queue"
        class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2.5 px-4 rounded-lg flex items-center justify-center gap-x-2 transition"
      >
        <MonitorPlay class="w-4 h-4 text-gray-500" />
        Live Queue Monitor
      </RouterLink>

      <RouterLink
        to="/hospital-admin/reports"
        class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2.5 px-4 rounded-lg flex items-center justify-center gap-x-2 transition"
      >
        <FileBarChart class="w-4 h-4 text-gray-500" />
        View Reports
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
import { PlusCircle, UserPlus2, CalendarRange, MonitorPlay, FileBarChart } from "lucide-vue-next";
</script>
