<template>
  <div
    class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full"
  >
    <h2 class="text-base font-bold text-gray-800 mb-5">Dept Performance</h2>

    <div class="flex flex-col gap-y-4 flex-1 justify-center">
      <!-- Cardiology Row -->
      <div>
        <div
          class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5"
        >
          <span>Cardiology</span>
          <span class="text-gray-800">92%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div class="bg-[#0052CC] h-full rounded-full w-[92%]"></div>
        </div>
      </div>

      <!-- Pediatrics Row -->
      <div>
        <div
          class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5"
        >
          <span>Pediatrics</span>
          <span class="text-gray-800">85%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div class="bg-[#0052CC] h-full rounded-full w-[85%]"></div>
        </div>
      </div>

      <!-- General Medicine Row -->
      <div>
        <div
          class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5"
        >
          <span>General</span>
          <span class="text-gray-800">78%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div class="bg-[#0052CC] h-full rounded-full w-[78%]"></div>
        </div>
      </div>

      <!-- Surgery Row -->
      <div>
        <div
          class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5"
        >
          <span>Surgery</span>
          <span class="text-gray-800">95%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div class="bg-[#0052CC] h-full rounded-full w-[95%]"></div>
        </div>
      </div>
    </div>
  </div>
</template>
