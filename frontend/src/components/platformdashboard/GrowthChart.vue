<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full">
    <!-- Chart Header Info -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-base font-bold text-gray-800">Hospital & Patient Growth</h2>
      <!-- Legend Markers -->
      <div class="flex items-center gap-x-4">
        <div class="flex items-center gap-x-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-[#6594C0]"></span>
          <span class="text-xs text-gray-500 font-medium">Hospitals</span>
        </div>
        <div class="flex items-center gap-x-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-[#B2D6D4]"></span>
          <span class="text-xs text-gray-500 font-medium">Patients</span>
        </div>
      </div>
    </div>

    <!-- Visual Chart Simulators using Tailwind Flex Bars -->
    <div class="flex-1 flex items-end justify-between px-2 h-48 border-b border-gray-100 pb-2">
      <!-- Jan Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[30%]"></div>
          <div class="w-6 bg-[#6594C0] h-[20%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">Jan</span>
      </div>
      <!-- Feb Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[45%]"></div>
          <div class="w-6 bg-[#6594C0] h-[25%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">Feb</span>
      </div>
      <!-- Mar Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[50%]"></div>
          <div class="w-6 bg-[#6594C0] h-[30%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">Mar</span>
      </div>
      <!-- Apr Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[75%]"></div>
          <div class="w-6 bg-[#6594C0] h-[40%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">Apr</span>
      </div>
      <!-- May Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[85%]"></div>
          <div class="w-6 bg-[#6594C0] h-[45%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">May</span>
      </div>
      <!-- Jun Bar -->
      <div class="flex flex-col items-center gap-y-2 w-10">
        <div class="w-full flex flex-col items-center justify-end h-32 gap-y-0.5">
          <div class="w-6 bg-[#B2D6D4] rounded-t-sm h-[95%]"></div>
          <div class="w-6 bg-[#6594C0] h-[55%]"></div>
        </div>
        <span class="text-[10px] text-gray-400 font-medium">Jun</span>
      </div>
    </div>
  </div>
</template>

<script setup></script>
