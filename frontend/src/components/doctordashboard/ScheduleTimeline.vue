<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full"
  >
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Today's Schedule</h2>
      <a href="#" class="text-xs font-semibold text-blue-600 hover:underline"
        >View All</a
      >
    </div>

    <!-- Timeline List Rows -->
    <div class="flex flex-col gap-y-3 flex-1">
      <!-- Completed Appointment Item Row -->
      <div
        class="flex items-center justify-between p-3.5 border border-gray-100 rounded-xl hover:bg-gray-50/50 transition-colors"
      >
        <div class="flex items-center gap-x-4">
          <div class="text-center min-w-[70px] border-r border-gray-100 pr-2">
            <p class="text-xs font-bold text-gray-800">09:00 AM</p>
            <p class="text-[10px] text-gray-400 font-medium">30 min</p>
          </div>
          <div>
            <h4 class="text-xs font-bold text-gray-800">Sarah Jenkins</h4>
            <p class="text-[11px] text-gray-400 font-medium mt-0.5">
              👤 Physical • Annual Checkup
            </p>
          </div>
        </div>
        <div class="flex items-center gap-x-3">
          <span
            class="bg-emerald-50 text-emerald-600 font-bold px-2 py-0.5 rounded-md text-[10px]"
            >Completed</span
          >
          <button class="text-gray-400 hover:text-gray-600 text-sm font-bold">
            ⋮
          </button>
        </div>
      </div>

      <!-- In Progress Active Video Call Appointment Row -->
      <div
        class="flex items-center justify-between p-3.5 border border-blue-100 bg-blue-50/20 rounded-xl"
      >
        <div class="flex items-center gap-x-4">
          <div class="text-center min-w-[70px] border-r border-blue-100 pr-2">
            <p class="text-xs font-bold text-[#0D4FB3]">09:45 AM</p>
            <p class="text-[10px] text-blue-400 font-medium">15 min</p>
          </div>
          <div>
            <h4 class="text-xs font-bold text-gray-900">Michael Chang</h4>
            <p class="text-[11px] text-blue-600 font-medium mt-0.5">
              💻 Video • Follow-up
            </p>
          </div>
        </div>
        <div class="flex items-center gap-x-3">
          <span
            class="bg-[#004795] text-white font-bold px-2 py-0.5 rounded-md text-[10px]"
            >In Progress</span
          >
          <button
            class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs px-3 py-1 rounded-md transition shadow-sm"
          >
            Join
          </button>
        </div>
      </div>

      <!-- Waiting Status Patient Row -->
      <div
        class="flex items-center justify-between p-3.5 border border-gray-100 rounded-xl hover:bg-gray-50/50 transition-colors"
      >
        <div class="flex items-center gap-x-4">
          <div class="text-center min-w-[70px] border-r border-gray-100 pr-2">
            <p class="text-xs font-bold text-gray-800">10:15 AM</p>
            <p class="text-[10px] text-gray-400 font-medium">45 min</p>
          </div>
          <div>
            <h4 class="text-xs font-bold text-gray-800">Robert Davis</h4>
            <p class="text-[11px] text-gray-400 font-medium mt-0.5">
              👤 Physical • New Patient
            </p>
          </div>
        </div>
        <div class="flex items-center gap-x-3">
          <span
            class="bg-blue-50 text-blue-600 font-bold px-2 py-0.5 rounded-md text-[10px]"
            >Waiting</span
          >
          <button class="text-gray-400 hover:text-gray-600 text-sm font-bold">
            ⋮
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
