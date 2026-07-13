<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col"
  >
    <h3 class="text-sm font-bold text-gray-800 mb-4">Notifications</h3>

    <div class="flex flex-col gap-y-3 divide-y divide-gray-50 text-xs">
      <!-- High Priority Lab Alert Item Block -->
      <div class="pb-3 flex gap-x-3 items-start">
        <AlertTriangle class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" />
        <div>
          <h4 class="font-bold text-red-600">Lab Results Ready</h4>
          <p class="text-gray-500 font-medium mt-0.5">
            Urgent CBC results for Michael Chang are available.
          </p>
          <span class="text-[9px] text-gray-400 font-bold block mt-1 uppercase"
            >10 mins ago</span
          >
        </div>
      </div>

      <!-- Schedule Update Alert Notification Row Element -->
      <div class="py-3 flex gap-x-3 items-start">
        <CalendarX class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" />
        <div>
          <h4 class="font-bold text-gray-800">Appointment Canceled</h4>
          <p class="text-gray-500 font-medium mt-0.5">
            2:00 PM slot opened up (David Miller).
          </p>
          <span class="text-[9px] text-gray-400 font-bold block mt-1 uppercase"
            >1 hour ago</span
          >
        </div>
      </div>
    </div>

    <button
      class="w-full text-center mt-3 pt-3 border-t border-gray-50 text-xs font-bold text-blue-600 hover:underline"
    >
      Mark all as read
    </button>
  </div>
</template>

<script setup>
import { AlertTriangle, CalendarX } from "lucide-vue-next";
</script>
