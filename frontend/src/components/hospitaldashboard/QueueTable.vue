<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
    <!-- Header -->
    <div class="p-5 flex items-center justify-between border-b border-gray-50">
      <h2 class="text-base font-bold text-gray-800">Doctor Appointment Summary</h2>
      <RouterLink
        to="/hospital-admin/appointments"
        class="text-xs font-semibold text-blue-600 hover:underline"
      >
        View All
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-10 flex justify-center items-center gap-2">
      <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <span class="text-xs text-gray-400 font-medium">Loading…</span>
    </div>

    <!-- Empty -->
    <div v-else-if="!rows.length" class="py-10 flex justify-center items-center">
      <p class="text-xs text-gray-400 font-medium">No appointment data available.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#f8fafc] dark:bg-[#0f172a] dark:bg-[#0f172a] text-gray-400 font-bold text-[11px] uppercase tracking-wider border-b border-gray-100">
            <th class="py-3 px-5">Doctor</th>
            <th class="py-3 px-5">Department</th>
            <th class="py-3 px-5 text-center">Total Appts</th>
            <th class="py-3 px-5 text-center">Completed</th>
            <th class="py-3 px-5 text-right">Completion Rate</th>
          </tr>
        </thead>
        <tbody class="text-xs font-medium text-gray-700 divide-y divide-gray-50">
          <tr v-for="row in rows" :key="row.name" class="hover:bg-gray-50/50 transition-colors">
            <td class="py-3.5 px-5 font-bold text-gray-900">{{ row.name }}</td>
            <td class="py-3.5 px-5 text-gray-500">{{ row.department || "—" }}</td>
            <td class="py-3.5 px-5 text-center">{{ row.appointments }}</td>
            <td class="py-3.5 px-5 text-center">{{ row.completed }}</td>
            <td class="py-3.5 px-5 text-right">
              <span
                class="font-semibold px-2 py-0.5 rounded-full text-[10px]"
                :class="rateClass(row)"
              >
                {{ completionRate(row) }}%
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { useHospitalDashboardStore } from "../../stores/hospitalDashboardStore";

const store   = useHospitalDashboardStore();
const loading = computed(() => store.loading);
const rows    = computed(() => store.doctorWorkloadList);

function completionRate(row) {
  const total = row.appointments || 1;
  return Math.round((row.completed / total) * 100);
}

function rateClass(row) {
  const rate = completionRate(row);
  if (rate >= 80) return "bg-emerald-50 text-emerald-600";
  if (rate >= 50) return "bg-amber-50 text-amber-600";
  return "bg-red-50 text-red-600";
}
</script>
