<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full">
    <h2 class="text-base font-bold text-gray-800 mb-5">Dept Performance</h2>

    <!-- Loading -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="!depts.length" class="flex-1 flex items-center justify-center">
      <p class="text-xs text-gray-400 font-medium">No department data available.</p>
    </div>

    <!-- Real data rows -->
    <div v-else class="flex flex-col gap-y-4 flex-1 justify-center">
      <div v-for="dept in depts" :key="dept.name">
        <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1.5">
          <span>{{ dept.name }}</span>
          <span class="text-gray-800">{{ dept.percentage }}%</span>
        </div>
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div
            class="bg-[#0052CC] h-full rounded-full transition-all duration-500"
            :style="{ width: dept.percentage + '%' }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useHospitalDashboardStore } from "../../stores/hospitalDashboardStore";

const store   = useHospitalDashboardStore();
const loading = computed(() => store.loading);
const depts   = computed(() => store.deptPerformanceList);
</script>
