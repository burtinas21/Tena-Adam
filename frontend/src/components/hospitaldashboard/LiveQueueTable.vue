<template>
  <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50 dark:border-slate-700">
      <h2 class="text-base font-bold text-gray-800 dark:text-slate-100">Live Queue Performance</h2>
      <RouterLink
        to="/hospital-admin/queue"
        class="text-xs font-semibold text-[#004795] dark:text-blue-400 hover:underline"
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
    <div v-else-if="!rows.length" class="py-10 text-center">
      <p class="text-xs text-gray-400 font-medium">No active queue data available.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#f8fafc] dark:bg-slate-700/50 text-gray-400 dark:text-slate-400 font-bold text-[11px] uppercase tracking-wider border-b border-gray-100 dark:border-slate-700">
            <th class="py-3 px-5">Department</th>
            <th class="py-3 px-5 text-center">Patients Waiting</th>
            <th class="py-3 px-5 text-center">Avg Service Time</th>
            <th class="py-3 px-5 text-right">Status</th>
          </tr>
        </thead>
        <tbody class="text-xs font-medium divide-y divide-gray-50 dark:divide-slate-700">
          <tr
            v-for="row in rows"
            :key="row.department"
            class="hover:bg-gray-50/60 dark:hover:bg-slate-700/40 transition-colors"
          >
            <td class="py-3.5 px-5 font-semibold text-gray-800 dark:text-slate-200">
              {{ row.department }}
            </td>
            <td class="py-3.5 px-5 text-center">
              <span class="font-bold text-gray-700 dark:text-slate-300">{{ row.waiting }}</span>
            </td>
            <td class="py-3.5 px-5 text-center text-gray-500 dark:text-slate-400">
              {{ row.avgTime }}
            </td>
            <td class="py-3.5 px-5 text-right">
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                :class="statusClass(row.status)"
              >
                {{ row.status }}
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

/**
 * Build queue rows from department performance data.
 * We derive "Patients Waiting" from pending appointments per department,
 * and estimate avg service time from completion rate.
 */
const rows = computed(() => {
  const depts = store.departmentPerformance;
  if (!Array.isArray(depts) || !depts.length) return [];

  return depts.slice(0, 6).map((d) => {
    const pending   = d.pending_appointments   ?? d.scheduled_appointments ?? 0;
    const total     = Math.max(d.total_appointments ?? 0, 1);
    const completed = d.completed_appointments ?? 0;
    const rate      = Math.round((completed / total) * 100);

    // Derive a rough avg service time from the completion rate
    const avgMins = rate >= 80 ? 15 : rate >= 50 ? 20 : 30;

    // Status logic
    let status = "On Track";
    if (pending > 10) status = "Busy";
    else if (pending === 0 && rate >= 80) status = "On Track";
    else if (pending > 5) status = "High Load";

    return {
      department: d.department_name ?? d.name ?? "Unknown",
      waiting:    pending,
      avgTime:    `${avgMins} mins`,
      status,
    };
  });
});

function statusClass(status) {
  if (status === "On Track")  return "bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
  if (status === "Busy")      return "bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400";
  if (status === "High Load") return "bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400";
  return "bg-gray-100 text-gray-600";
}
</script>
