<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Today's Schedule</h2>
      <RouterLink to="/doctor/appointments" class="text-xs font-semibold text-blue-600 hover:underline">
        View All
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-y-3">
      <div v-for="i in 3" :key="i" class="h-16 bg-gray-50 rounded-xl animate-pulse border border-gray-100"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="!items.length" class="py-10 flex flex-col items-center gap-y-2">
      <CalendarDays class="w-8 h-8 text-gray-200" />
      <p class="text-xs text-gray-400 font-medium">No appointments scheduled for today.</p>
    </div>

    <!-- Timeline rows -->
    <div v-else class="flex flex-col gap-y-3">
      <div
        v-for="appt in items"
        :key="appt.id"
        class="flex items-center justify-between p-3.5 border rounded-xl transition-colors"
        :class="rowClass(appt.status)"
      >
        <!-- Time + patient info -->
        <div class="flex items-center gap-x-4 min-w-0">
          <div class="text-center min-w-[72px] border-r pr-3 shrink-0" :class="appt.status === 'in_progress' ? 'border-blue-100' : 'border-gray-100'">
            <p class="text-xs font-bold" :class="appt.status === 'in_progress' ? 'text-blue-700' : 'text-gray-800'">
              {{ appt.time }}
            </p>
            <p class="text-[10px] font-medium mt-0.5" :class="appt.status === 'in_progress' ? 'text-blue-400' : 'text-gray-400'">
              {{ appt.duration }}
            </p>
          </div>
          <div class="min-w-0">
            <h4 class="text-xs font-bold text-gray-900 truncate">{{ appt.patient }}</h4>
            <p class="text-[11px] font-medium mt-0.5 truncate" :class="appt.status === 'in_progress' ? 'text-blue-600' : 'text-gray-400'">
              {{ appt.type === 'video' ? '💻 Video' : '👤 Physical' }}
              {{ appt.reason ? '• ' + appt.reason : '' }}
            </p>
          </div>
        </div>

        <!-- Status + action -->
        <div class="flex items-center gap-x-2 shrink-0 ml-3">
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-md" :class="statusBadge(appt.status)">
            {{ statusLabel(appt.status) }}
          </span>
          <RouterLink
            v-if="appt.type === 'video' && appt.status === 'in_progress'"
            to="/doctor/telehealth"
            class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-[10px] px-3 py-1 rounded-md transition shadow-sm"
          >
            Join
          </RouterLink>
          <RouterLink
            v-else
            :to="`/doctor/medicalencounter`"
            class="text-gray-400 hover:text-blue-600 font-bold text-xs transition"
          >
            ⋮
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { CalendarDays } from "lucide-vue-next";
import { useDoctorDashboardStore } from "../../stores/doctorDashboardStore";

const store   = useDoctorDashboardStore();
const loading = computed(() => store.loading);
const items   = computed(() => store.todaySchedule);

function rowClass(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "in_progress") return "border-blue-100 bg-blue-50/20";
  if (s === "completed")   return "border-gray-100 bg-gray-50/30";
  return "border-gray-100 hover:bg-gray-50/50";
}

function statusLabel(status) {
  const map = {
    completed:   "Completed",
    in_progress: "In Progress",
    scheduled:   "Upcoming",
    confirmed:   "Confirmed",
    cancelled:   "Cancelled",
    pending:     "Pending",
  };
  return map[(status ?? "").toLowerCase()] ?? status;
}

function statusBadge(status) {
  const s = (status ?? "").toLowerCase();
  if (s === "completed")   return "bg-emerald-50 text-emerald-600";
  if (s === "in_progress") return "bg-[#004795] text-white";
  if (s === "cancelled")   return "bg-red-50 text-red-500";
  if (s === "confirmed")   return "bg-indigo-50 text-indigo-600";
  return "bg-blue-50 text-blue-600";
}
</script>
