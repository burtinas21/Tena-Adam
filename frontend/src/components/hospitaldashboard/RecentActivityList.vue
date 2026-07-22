<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Recent Appointments</h2>
      <button
        class="text-gray-400 hover:text-gray-600 transition"
        :class="{ 'animate-spin': store.loading }"
        @click="store.fetchAll()"
      >
        <RotateCw class="w-4 h-4" />
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="flex-1 flex items-center justify-center">
      <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="!items.length" class="flex-1 flex items-center justify-center">
      <p class="text-xs text-gray-400 font-medium text-center">No recent appointments found.</p>
    </div>

    <!-- Real activity list -->
    <div v-else class="flex flex-col gap-y-4 flex-1 overflow-hidden">
      <div v-for="item in items" :key="item.id" class="flex gap-x-3 items-start">
        <!-- Icon by status -->
        <div
          class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
          :class="iconBg(item.status)"
        >
          <component :is="iconFor(item.status)" class="w-3.5 h-3.5" :class="iconColor(item.status)" />
        </div>
        <div class="min-w-0">
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            <span class="font-bold text-gray-900">{{ item.patientName }}</span>
            — appointment with
            <span class="font-bold text-gray-900">{{ item.doctorName }}</span>
            <span v-if="item.department" class="text-gray-500"> ({{ item.department }})</span>
          </p>
          <div class="flex items-center gap-x-2 mt-0.5">
            <span
              class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded-full"
              :class="statusBadge(item.status)"
            >{{ item.status }}</span>
            <span class="text-[10px] text-gray-400">{{ timeAgo(item.createdAt) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer link -->
    <div class="mt-4 pt-3 border-t border-gray-50 text-center">
      <RouterLink
        to="/hospital-admin/appointments"
        class="text-xs font-semibold text-blue-600 hover:underline"
      >View All Appointments</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { RotateCw, User, CheckCircle, Clock, XCircle, CalendarCheck } from "lucide-vue-next";
import { useHospitalDashboardStore } from "../../stores/hospitalDashboardStore";

const store = useHospitalDashboardStore();
const items = computed(() => store.recentActivityList);

function iconFor(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "completed")  return CheckCircle;
  if (s === "cancelled")  return XCircle;
  if (s === "scheduled" || s === "confirmed") return Clock;
  return CalendarCheck;
}

function iconBg(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "completed")  return "bg-emerald-50";
  if (s === "cancelled")  return "bg-red-50";
  if (s === "scheduled" || s === "confirmed") return "bg-blue-50";
  return "bg-gray-100";
}

function iconColor(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "completed")  return "text-emerald-600";
  if (s === "cancelled")  return "text-red-500";
  if (s === "scheduled" || s === "confirmed") return "text-blue-600";
  return "text-gray-500";
}

function statusBadge(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "completed")  return "bg-emerald-50 text-emerald-700";
  if (s === "cancelled")  return "bg-red-50 text-red-600";
  if (s === "scheduled")  return "bg-blue-50 text-blue-600";
  if (s === "confirmed")  return "bg-indigo-50 text-indigo-600";
  return "bg-gray-100 text-gray-500";
}

function timeAgo(iso) {
  if (!iso) return "";
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 60)   return `${Math.round(diff)}s ago`;
  if (diff < 3600) return `${Math.round(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.round(diff / 3600)}h ago`;
  return `${Math.round(diff / 86400)}d ago`;
}
</script>
