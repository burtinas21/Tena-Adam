<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">Appointment Operations Center</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Manage, monitor, and coordinate daily hospital appointments.
          </p>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 sm:p-5">
          <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center mb-2">
            <CalendarDays class="w-5 h-5 text-blue-500" />
          </div>
          <p class="text-xl sm:text-2xl font-black text-gray-900">{{ todayCount }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Today's</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 sm:p-5">
          <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center mb-2">
            <Clock class="w-5 h-5 text-amber-500" />
          </div>
          <p class="text-xl sm:text-2xl font-black text-gray-900">{{ store.pending.length }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Pending</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 sm:p-5">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center mb-2">
            <CheckCircle class="w-5 h-5 text-emerald-500" />
          </div>
          <p class="text-xl sm:text-2xl font-black text-gray-900">{{ store.completed.length }}</p>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Completed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 sm:p-5">
          <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center mb-2">
            <XCircle class="w-5 h-5 text-red-400" />
          </div>
          <p class="text-xl sm:text-2xl font-black text-gray-900">{{ store.cancelled.length }}</p>
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
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 sm:px-5 py-4 border-b border-gray-100">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input v-model="search" type="text" placeholder="Search patient or doctor..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#004795]" />
          </div>
          <div class="flex gap-2">
            <select v-model="statusFilter"
              class="border border-gray-200 rounded-lg px-3 py-2 text-xs bg-white focus:outline-none focus:border-[#004795] flex-1 sm:flex-none">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <select v-model="typeFilter"
              class="border border-gray-200 rounded-lg px-3 py-2 text-xs bg-white focus:outline-none focus:border-[#004795] flex-1 sm:flex-none">
              <option value="">All Types</option>
              <option value="telehealth">Telemedicine</option>
              <option value="inperson">In-Person</option>
            </select>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="store.loading && !store.appointments.length" class="p-5 space-y-3">
          <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-xs text-left min-w-[640px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Doctor</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date & Time</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Department</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Type</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-12 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="!filtered.length">
                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                  <CalendarDays class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                  <p class="text-sm font-medium">No appointments found</p>
                </td>
              </tr>
              <tr v-for="appt in filtered" :key="appt.id" class="hover:bg-gray-50/60 transition-colors">
                <!-- Patient -->
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                      <span class="text-[10px] font-bold text-[#004795]">{{ personInitials(appt.patient) }}</span>
                    </div>
                    <p class="font-semibold text-gray-800 truncate max-w-[80px] sm:max-w-none">{{ personName(appt.patient) }}</p>
                  </div>
                </td>
                <!-- Doctor -->
                <td class="px-4 py-3 hidden sm:table-cell">
                  <p class="font-medium text-gray-700 truncate max-w-[110px]">
                    Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}
                  </p>
                  <p class="text-[10px] text-gray-400 mt-0.5">{{ appt.doctor?.department?.name ?? '—' }}</p>
                </td>
                <!-- Date/time -->
                <td class="px-4 py-3">
                  <p class="font-medium text-gray-700">{{ formatDate(appt.scheduled_time) }}</p>
                  <p class="text-[10px] text-gray-400 mt-0.5">{{ formatTime(appt.scheduled_time) }}</p>
                </td>
                <!-- Department -->
                <td class="px-4 py-3 hidden lg:table-cell">
                  <span class="text-gray-600 truncate max-w-[100px] block">{{ appt.department?.name ?? appt.doctor?.department?.name ?? '—' }}</span>
                </td>
                <!-- Type -->
                <td class="px-4 py-3 hidden md:table-cell">
                  <span v-if="appt.is_telehealth"
                    class="inline-flex items-center gap-1 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded">
                    <Monitor class="w-3 h-3" /> Telemed
                  </span>
                  <span v-else class="text-gray-500">In-Person</span>
                </td>
                <!-- Status -->
                <td class="px-4 py-3">
                  <span :class="statusClass(appt.status)"
                    class="inline-flex items-center gap-1 text-[10px] font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(appt.status)" />
                    {{ appt.status }}
                  </span>
                </td>
                <!-- 3-dot actions -->
                <td class="px-4 py-3 text-right">
                  <div class="relative inline-block" @click.stop>
                    <button
                      @click="toggleMenu(appt.id)"
                      :disabled="isTerminal(appt.status)"
                      class="p-1.5 rounded-lg transition"
                      :class="isTerminal(appt.status)
                        ? 'text-gray-200 cursor-not-allowed'
                        : 'text-gray-400 hover:text-gray-700 hover:bg-gray-100'"
                    >
                      <MoreVertical class="w-4 h-4" />
                    </button>
                    <div
                      v-if="openMenuId === appt.id && !isTerminal(appt.status)"
                      class="absolute right-0 mt-1 w-40 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1"
                    >
                      <button
                        v-if="appt.status === 'pending'"
                        @click="handleAction(appt.id, 'confirmed'); closeMenu()"
                        :disabled="store.loading"
                        class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-50 transition disabled:opacity-50"
                      >
                        <CheckCircle class="w-3.5 h-3.5" /> Confirm
                      </button>
                      <button
                        v-if="appt.status === 'confirmed'"
                        @click="handleAction(appt.id, 'completed'); closeMenu()"
                        :disabled="store.loading"
                        class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition disabled:opacity-50"
                      >
                        <CheckCircle class="w-3.5 h-3.5" /> Complete
                      </button>
                      <button
                        v-if="['pending', 'confirmed'].includes(appt.status)"
                        @click="handleAction(appt.id, 'cancelled'); closeMenu()"
                        :disabled="store.loading"
                        class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition disabled:opacity-50"
                      >
                        <XCircle class="w-3.5 h-3.5" /> Cancel
                      </button>
                    </div>
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
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
  CalendarDays, Clock, CheckCircle, XCircle,
  AlertCircle, Search, Monitor, MoreVertical,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";

const store = useAppointmentStore();
const search       = ref("");
const statusFilter = ref("");
const typeFilter   = ref("");
const openMenuId   = ref(null);

onMounted(() => {
  store.fetchAll();
  document.addEventListener("click", handleOutsideClick);
});
onUnmounted(() => document.removeEventListener("click", handleOutsideClick));

function toggleMenu(id) {
  openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() { openMenuId.value = null; }
function handleOutsideClick() { openMenuId.value = null; }

/** Completed / cancelled / no_show = no more actions available */
function isTerminal(status) {
  return ["completed", "cancelled", "no_show"].includes(status);
}

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
function statusDotClass(status) {
  return {
    pending:   "bg-amber-500",
    confirmed: "bg-blue-500",
    completed: "bg-emerald-500",
    cancelled: "bg-red-500",
    no_show:   "bg-gray-400",
  }[status] ?? "bg-gray-400";
}

async function handleAction(id, status) {
  try { await store.updateStatus(id, status); } catch { /* store.error shows it */ }
}
</script>
