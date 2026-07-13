<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full"
  >
    <h2 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h2>

    <div class="grid grid-cols-2 gap-3 flex-1">
      <!-- Register Hospital Action Button -->
      <button
        class="bg-[#EEF4FF] hover:bg-[#E0ECFF] transition-colors p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group"
      >
        <Building2
          class="w-6 h-6 text-[#1E60B4] group-hover:scale-105 transition-transform"
        />
        <span class="text-xs font-semibold text-[#1E60B4]"
          >Register Hospital</span
        >
      </button>

      <!-- Manage Users Action Button -->
      <button
        class="bg-[#E6F9F2] hover:bg-[#D5F5E9] transition-colors p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group"
      >
        <UserPlus2
          class="w-6 h-6 text-[#00A36C] group-hover:scale-105 transition-transform"
        />
        <span class="text-xs font-semibold text-[#00A36C]">Manage Users</span>
      </button>

      <!-- View Analytics Action Button -->
      <button
        class="bg-[#F8F9FA] hover:bg-gray-100 transition-colors p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group"
      >
        <BarChart3
          class="w-6 h-6 text-gray-600 group-hover:scale-105 transition-transform"
        />
        <span class="text-xs font-semibold text-gray-600">View Analytics</span>
      </button>

      <!-- Config Settings Action Button -->
      <button
        class="bg-[#F4F2FF] hover:bg-[#EAE6FF] transition-colors p-4 rounded-xl flex flex-col items-center justify-center gap-y-2 text-center group"
      >
        <Sliders
          class="w-6 h-6 text-[#6366F1] group-hover:scale-105 transition-transform"
        />
        <span class="text-xs font-semibold text-[#6366F1]"
          >Config Settings</span
        >
      </button>
    </div>
  </div>
</template>

<script setup>
import { Building2, UserPlus2, BarChart3, Sliders } from "lucide-vue-next";
</script>
