<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full"
  >
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Recent Activity</h2>
      <a href="#" class="text-xs font-semibold text-blue-600 hover:underline"
        >View All</a
      >
    </div>

    <!-- Activity Log List -->
    <div class="flex flex-col gap-y-3 flex-1 overflow-y-auto">
      <!-- Activity Item 1 -->
      <div class="flex items-start gap-x-3 p-3 bg-[#F4F7FC] rounded-lg">
        <div
          class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <Building2 class="w-4 h-4 text-blue-600" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium">
            <span class="font-bold text-gray-900">St. Paul's Hospital</span>
            registered successfully on the platform.
          </p>
          <span class="text-[10px] text-gray-400 block mt-1">10 mins ago</span>
        </div>
      </div>

      <!-- Activity Item 2 -->
      <div class="flex items-start gap-x-3 p-3 bg-[#F4F7FC] rounded-lg">
        <div
          class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <UserCheck class="w-4 h-4 text-emerald-600" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium">
            New Doctor
            <span class="font-bold text-gray-900">Sarah Jenkins</span> joined
            MedCenter.
          </p>
          <span class="text-[10px] text-gray-400 block mt-1">45 mins ago</span>
        </div>
      </div>

      <!-- Activity Item 3 -->
      <div class="flex items-start gap-x-3 p-3 bg-[#F4F7FC] rounded-lg">
        <div
          class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <RefreshCw class="w-4 h-4 text-purple-600" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium">
            System Update
            <span class="font-bold text-gray-900">v2.4.1</span> completed
            successfully.
          </p>
          <span class="text-[10px] text-gray-400 block mt-1">1 hour ago</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Building2, UserCheck, RefreshCw } from "lucide-vue-next";
</script>
