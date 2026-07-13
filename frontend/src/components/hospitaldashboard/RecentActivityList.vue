<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full"
  >
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Recent Activities</h2>
      <button class="text-gray-400 hover:text-gray-600 transition">
        <RotateCw class="w-4 h-4" />
      </button>
    </div>

    <!-- Timeline Wrapper Box Container -->
    <div class="flex flex-col gap-y-4 flex-1">
      <!-- Event 1: New Patient -->
      <div class="flex gap-x-3 items-start">
        <div
          class="w-7 h-7 rounded-full bg-[#EAF2FF] flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <User class="w-3.5 h-3.5 text-[#0052CC]" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            New patient registered in
            <span class="font-bold text-gray-900">Cardiology</span>
          </p>
          <span class="text-[10px] text-gray-400 block mt-0.5">2 mins ago</span>
        </div>
      </div>

      <!-- Event 2: Doctor Task -->
      <div class="flex gap-x-3 items-start">
        <div
          class="w-7 h-7 rounded-full bg-[#E6F9F2] flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <Check class="w-3.5 h-3.5 text-[#00A36C]" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            <span class="font-bold text-gray-900">Dr. Smith</span> completed 5
            appointments
          </p>
          <span class="text-[10px] text-gray-400 block mt-0.5"
            >15 mins ago</span
          >
        </div>
      </div>

      <!-- Event 3: Urgent Capacity System Alert -->
      <div class="flex gap-x-3 items-start">
        <div
          class="w-7 h-7 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <AlertTriangle class="w-3.5 h-3.5 text-red-600" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            System Alert:
            <span class="font-bold text-red-600">ER Capacity High</span>
          </p>
          <span class="text-[10px] text-gray-400 block mt-0.5"
            >34 mins ago</span
          >
        </div>
      </div>

      <!-- Event 4: Server Scheduled Task -->
      <div class="flex gap-x-3 items-start">
        <div
          class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5"
        >
          <Database class="w-3.5 h-3.5 text-gray-500" />
        </div>
        <div>
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            Server maintenance scheduled
          </p>
          <span class="text-[10px] text-gray-400 block mt-0.5">1 hour ago</span>
        </div>
      </div>
    </div>

    <!-- Bottom Footer Route Trigger Link -->
    <div class="mt-4 pt-3 border-t border-gray-50 text-center">
      <a href="#" class="text-xs font-semibold text-blue-600 hover:underline"
        >View Full Log</a
      >
    </div>
  </div>
</template>

<script setup>
import {
  RotateCw,
  User,
  Check,
  AlertTriangle,
  Database,
} from "lucide-vue-next";
</script>
