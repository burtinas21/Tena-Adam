<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Appointment Operations Center</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Manage, monitor, and coordinate daily hospital appointments.
          </p>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-2">
            <CalendarDays class="w-5 h-5 text-blue-500" />
          </div>
          <p class="text-2xl font-black text-gray-900">{{ todayCount }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Today's</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center mb-2">
            <Clock class="w-5 h-5 text-amber-500" />
          </div>
          <p class="text-2xl font-black text-gray-900">{{ store.pending.length }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Pending</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center mb-2">
            <CheckCircle class="w-5 h-5 text-emerald-500" />
          </div>
          <p class="text-2xl font-black text-gray-900">{{ store.completed.length }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Completed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center mb-2">
            <XCircle class="w-5 h-5 text-red-400" />
          </div>
          <p class="text-2xl font-black text-gray-900">{{ store.cancelled.length }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Cancelled</p>
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
            <input v-model="search" type="text" placeholder="Search patient or doctor..."
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
          <select v-model="typeFilter"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]">
            <option value="">All Types</option>
            <option value="telehealth">Telemedicine</option>
            <option value="inperson">In-Person</option>
          </select>
        </div>

        <!-- Loading -->
        <div v-if="store.loading && !store.appointments.length" class="p-5 space-y-3">
          <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left min-w-[750px]">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Doctor</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date & Time</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Department</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Type</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="!filtered.length">
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                  <CalendarDays class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                  <p class="text-sm font-medium">No appointments found</p>
                </td>
              </tr>
              <tr v-for="appt in filtered" :key="appt.id" class="hover:bg-gray-50/60 transition-colors">
                <!-- Patient -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                      <span class="text-[10px] font-bold text-[#004795]">{{ personInitials(appt.patient) }}</span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800 text-xs">{{ personName(appt.patient) }}</p>
                    </div>
                  </div>
                </td>
                <!-- Doctor -->
                <td class="px-5 py-4">
                  <p class="text-xs font-medium text-gray-700">
                    Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}
                  </p>
                  <p class="text-[10px] text-gray-400 mt-0.5">{{ appt.doctor?.department?.name ?? '—' }}</p>
                </td>
                <!-- Date/time -->
                <td class="px-5 py-4">
                  <p class="text-xs font-medium text-gray-700">{{ formatDate(appt.scheduled_time) }}</p>
                  <p class="text-[10px] text-gray-400 mt-0.5">{{ formatTime(appt.scheduled_time) }}</p>
                </td>
                <!-- Department -->
                <td class="px-5 py-4">
                  <span class="text-xs text-gray-600">{{ appt.department?.name ?? appt.doctor?.department?.name ?? '—' }}</span>
                </td>
                <!-- Type -->
                <td class="px-5 py-4">
                  <span v-if="appt.is_telehealth"
                    class="inline-flex items-center gap-1 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded">
                    <Monitor class="w-3 h-3" /> Telemed
                  </span>
                  <span v-else class="text-xs text-gray-500">In-Person</span>
                </td>
                <!-- Status -->
                <td class="px-5 py-4">
                  <span :class="statusClass(appt.status)"
                    class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                    {{ appt.status }}
                  </span>
                </td>
                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button v-if="appt.status === 'pending'"
                      @click="handleAction(appt.id, 'confirmed')"
                      :disabled="store.loading"
                      class="px-2 py-1 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                      Confirm
                    </button>
                    <button v-if="appt.status === 'confirmed'"
                      @click="handleAction(appt.id, 'completed')"
                      :disabled="store.loading"
                      class="px-2 py-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                      Complete
                    </button>
                    <button v-if="['pending','confirmed'].includes(appt.status)"
                      @click="handleAction(appt.id, 'cancelled')"
                      :disabled="store.loading"
                      class="px-2 py-1 text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                      Cancel
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
  CalendarDays, Clock, CheckCircle, XCircle,
  AlertCircle, Search, Monitor,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";

const store = useAppointmentStore();
const search       = ref("");
const statusFilter = ref("");
const typeFilter   = ref("");

onMounted(() => store.fetchAll());

const todayCount = computed(() =>
  store.appointments.filter((a) =>
    new Date(a.scheduled_time).toDateString() === new Date().toDateString()
  ).length
);

const filtered = computed(() => {
  let list = store.appointments;
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((a) =>
      personName(a.patient).toLowerCase().includes(q) ||
      `${a.doctor?.user?.first_name ?? ""} ${a.doctor?.user?.last_name ?? ""}`.toLowerCase().includes(q)
    );
  }
  if (statusFilter.value) list = list.filter((a) => a.status === statusFilter.value);
  if (typeFilter.value === "telehealth") list = list.filter((a) => a.is_telehealth);
  if (typeFilter.value === "inperson")   list = list.filter((a) => !a.is_telehealth);
  return list;
});

function personName(p) {
  if (!p) return "—";
  return `${p.first_name ?? ""} ${p.last_name ?? ""}`.trim() || p.email || "—";
}
function personInitials(p) {
  if (!p) return "?";
  return ((p.first_name?.[0] ?? "") + (p.last_name?.[0] ?? "")).toUpperCase() || "?";
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

async function handleAction(id, status) {
  try { await store.updateStatus(id, status); } catch { /* store.error shows it */ }
}
</script>
