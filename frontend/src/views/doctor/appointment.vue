<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Appointment Management</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            View and manage your patient appointments.
          </p>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div v-for="stat in stats" :key="stat.label"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
              :class="stat.bg">
              <component :is="stat.icon" class="w-4.5 h-4.5" :class="stat.color" />
            </div>
          </div>
          <p class="text-2xl font-black text-gray-900">{{ stat.value }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Error -->
      <div v-if="store.error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ store.error }}
      </div>

      <!-- Main panel -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <!-- Filter bar -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input v-model="search" type="text" placeholder="Search patient name..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]" />
          </div>
          <select v-model="statusFilter"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Loading -->
        <div v-if="store.loading && !store.appointments.length" class="p-5 space-y-3">
          <div v-for="n in 3" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
  <table class="w-full min-w-[500px] sm:min-w-[600px] text-left text-[10px] sm:text-xs md:text-sm">
    <thead>
      <tr class="border-b border-gray-100">
        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight">
          Patient
        </th>

        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight">
          Date
          <br />
          &amp; Time
        </th>

        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight">
          Reason
        </th>

        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight">
          Type
        </th>

        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight">
          Status
        </th>

        <th
          class="px-1 sm:px-2 md:px-3 lg:px-5 py-2 text-[8px] sm:text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-normal leading-tight text-right">
          Action
          <br />
          s
        </th>
      </tr>
    </thead>

    <tbody class="divide-y divide-gray-50">
      <tr v-if="!filtered.length">
        <td colspan="6" class="px-2 py-10 text-center text-gray-400">
          <CalendarDays class="w-7 h-7 mx-auto mb-2 text-gray-300" />
          <p class="text-xs font-medium">No appointments found</p>
        </td>
      </tr>

      <tr
        v-for="appt in filtered"
        :key="appt.id"
        class="hover:bg-gray-50/60 transition-colors"
      >
        <!-- Patient -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3">
          <div class="flex items-center gap-1 sm:gap-2 md:gap-3">
            <div
              class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
              <span class="text-[9px] sm:text-[10px] md:text-xs font-bold text-[#004795]">
                {{ patientInitials(appt) }}
              </span>
            </div>

            <div class="min-w-0">
              <p class="font-semibold text-gray-800 text-[10px] sm:text-xs md:text-sm">
                {{ patientName(appt) }}
              </p>

              <p class="text-[9px] sm:text-[10px] md:text-xs text-gray-400 break-all">
                {{ appt.patient?.email ?? '—' }}
              </p>
            </div>
          </div>
        </td>

        <!-- Date -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3">
          <p class="text-[10px] sm:text-xs md:text-sm font-medium text-gray-700">
            {{ formatDate(appt.scheduled_time) }}
          </p>

          <p class="text-[9px] sm:text-[10px] md:text-xs text-gray-400">
            {{ formatTime(appt.scheduled_time) }}
          </p>
        </td>

        <!-- Reason -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3 max-w-[120px] sm:max-w-xs">
          <p class="text-[10px] sm:text-xs md:text-sm text-gray-600 break-words">
            {{ appt.reason ?? '—' }}
          </p>
        </td>

        <!-- Type -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3">
          <span
            v-if="appt.is_telehealth"
            class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] md:text-xs font-medium text-blue-600 bg-blue-50 border border-blue-100 px-1 py-0.5 rounded"
          >
            <Monitor class="w-2.5 h-2.5 sm:w-3 sm:h-3" />
            Telemedicine
          </span>

          <span
            v-else
            class="text-[9px] sm:text-[10px] md:text-xs text-gray-500"
          >
            In-Person
          </span>
        </td>

        <!-- Status -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3">
          <span
            :class="statusClass(appt.status)"
            class="text-[9px] sm:text-[10px] md:text-xs font-semibold px-1.5 sm:px-2 py-0.5 rounded-full border capitalize"
          >
            {{ appt.status }}
          </span>
        </td>

        <!-- Actions -->
        <td class="px-1 sm:px-2 md:px-3 lg:px-5 py-3 text-right">
          <div class="flex items-center justify-end gap-1">
            <button
              v-if="appt.status === 'confirmed'"
              @click="handleComplete(appt.id)"
              :disabled="store.loading"
              title="Mark completed"
              class="px-1 py-0.5 sm:px-2 sm:py-1 text-[9px] sm:text-[10px] md:text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition"
            >
              Complete
            </button>

            <button
              v-if="appt.status === 'pending'"
              @click="handleConfirm(appt.id)"
              :disabled="store.loading"
              title="Confirm"
              class="px-1 py-0.5 sm:px-2 sm:py-1 text-[9px] sm:text-[10px] md:text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition"
            >
              Confirm
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  AlertCircle, Search, CalendarDays, Clock,
  Monitor, CheckCircle, CalendarCheck,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";

const store = useAppointmentStore();
const search       = ref("");
const statusFilter = ref("");

onMounted(() => store.fetchAll());

const stats = computed(() => [
  { label: "Today's", value: store.appointments.filter((a) => isToday(a.scheduled_time)).length,
    icon: CalendarDays, bg: "bg-blue-50", color: "text-blue-500" },
  { label: "Upcoming", value: store.appointments.filter((a) => ["pending","confirmed"].includes(a.status)).length,
    icon: CalendarCheck, bg: "bg-amber-50", color: "text-amber-500" },
  { label: "Completed", value: store.completed.length,
    icon: CheckCircle, bg: "bg-emerald-50", color: "text-emerald-500" },
  { label: "Cancelled", value: store.cancelled.length,
    icon: Clock, bg: "bg-red-50", color: "text-red-400" },
]);

const filtered = computed(() => {
  let list = store.appointments;
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((a) => patientName(a).toLowerCase().includes(q));
  }
  if (statusFilter.value) list = list.filter((a) => a.status === statusFilter.value);
  return list;
});

function patientName(appt) {
  const p = appt.patient;
  return p ? `${p.first_name ?? ""} ${p.last_name ?? ""}`.trim() : "—";
}
function patientInitials(appt) {
  const p = appt.patient;
  return p ? ((p.first_name?.[0] ?? "") + (p.last_name?.[0] ?? "")).toUpperCase() : "?";
}
function isToday(dt) {
  if (!dt) return false;
  return new Date(dt).toDateString() === new Date().toDateString();
}
function formatDate(dt) {
  return dt ? new Date(dt).toLocaleDateString("en-ET", { day: "numeric", month: "short", year: "numeric" }) : "—";
}
function formatTime(dt) {
  return dt ? new Date(dt).toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" }) : "—";
}
function statusClass(status) {
  return {
    pending:   "bg-amber-50 text-amber-700 border-amber-200",
    confirmed: "bg-blue-50 text-blue-700 border-blue-200",
    completed: "bg-emerald-50 text-emerald-700 border-emerald-200",
    cancelled: "bg-red-50 text-red-600 border-red-200",
    no_show:   "bg-gray-50 text-gray-500 border-gray-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

async function handleConfirm(id) {
  try { await store.updateStatus(id, "confirmed"); } catch { /* error shown */ }
}
async function handleComplete(id) {
  try { await store.updateStatus(id, "completed"); } catch { /* error shown */ }
}
</script>
