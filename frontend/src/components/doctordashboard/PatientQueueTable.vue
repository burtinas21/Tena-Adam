<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-50 flex items-center justify-between">
      <div class="flex items-center gap-x-2">
        <h3 class="text-sm font-bold text-gray-800">Patient Queue</h3>
        <span
          v-if="waiting > 0"
          class="w-5 h-5 bg-red-600 text-white font-bold text-xs rounded-full flex items-center justify-center"
        >{{ waiting }}</span>
      </div>
      <RouterLink to="/doctor/queue" class="text-xs font-semibold text-blue-600 hover:underline">
        Manage Queue
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-8 flex justify-center items-center gap-x-2">
      <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <span class="text-xs text-gray-400">Loading queue…</span>
    </div>

    <!-- Empty -->
    <div v-else-if="!rows.length" class="py-10 flex flex-col items-center gap-y-2">
      <Users class="w-8 h-8 text-gray-200" />
      <p class="text-xs text-gray-400 font-medium">No patients in queue right now.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#f8fafc] dark:bg-[#0f172a] dark:bg-[#0f172a] text-gray-400 font-bold text-[10px] uppercase tracking-wider border-b border-gray-100">
            <th class="py-3 px-5">No.</th>
            <th class="py-3 px-5">Patient Name</th>
            <th class="py-3 px-5">Waiting Time</th>
            <th class="py-3 px-5">Priority</th>
            <th class="py-3 px-5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="text-xs font-medium text-gray-700 divide-y divide-gray-50">
          <tr v-for="row in rows" :key="row.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="py-3 px-5 font-bold text-gray-400">{{ row.number }}</td>
            <td class="py-3 px-5 font-bold text-gray-900">{{ row.patient }}</td>
            <td class="py-3 px-5" :class="row.waitMins >= 20 ? 'text-red-600 font-semibold' : 'text-gray-600'">
              {{ row.waitMins }} min
            </td>
            <td class="py-3 px-5">
              <span
                class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                :class="priorityBadge(row.priority)"
              >{{ row.priority.toUpperCase() }}</span>
            </td>
            <td class="py-3 px-5 text-right">
              <RouterLink
                to="/doctor/queue"
                class="text-blue-600 font-bold hover:underline text-xs"
              >Call In</RouterLink>
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
import { Users } from "lucide-vue-next";
import { useDoctorDashboardStore } from "../../stores/doctorDashboardStore";

const store   = useDoctorDashboardStore();
const loading = computed(() => store.loading);
const rows    = computed(() => store.queueList.filter((r) => r.status === "waiting"));
const waiting = computed(() => store.waitingCount);

function priorityBadge(priority) {
  const p = (priority ?? "normal").toLowerCase();
  if (p === "high"   || p === "urgent") return "bg-red-50 text-red-600";
  if (p === "medium" || p === "normal") return "bg-blue-50 text-blue-600";
  return "bg-gray-100 text-gray-500";
}
</script>
