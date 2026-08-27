<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-3 sm:p-5 overflow-y-auto font-sans dark:text-slate-200">
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
        <div v-else>
          <table class="w-full text-xs text-left table-fixed">
            <colgroup>
              <col style="width:22%" />
              <col style="width:20%" class="hidden sm:table-column" />
              <col style="width:18%" />
              <col style="width:14%" class="hidden md:table-column" />
              <col style="width:14%" />
              <col style="width:12%" />
            </colgroup>
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Doctor</th>
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date & Time</th>
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Type</th>
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-2 sm:px-3 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Act.</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="!paginated.length">
                <td colspan="6" class="px-3 py-10 text-center text-gray-400">
                  <CalendarDays class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                  <p class="text-sm font-medium">No appointments found</p>
                </td>
              </tr>
              <tr v-for="appt in paginated" :key="appt.id" class="hover:bg-gray-50/60 transition-colors">
                <td class="px-2 sm:px-3 py-2.5">
                  <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                      <span class="text-[9px] font-bold text-[#004795]">{{ personInitials(appt.patient) }}</span>
                    </div>
                    <p class="font-semibold text-gray-800 truncate">{{ personName(appt.patient) }}</p>
                  </div>
                </td>
                <td class="px-2 sm:px-3 py-2.5 hidden sm:table-cell">
                  <p class="font-medium text-gray-700 truncate">Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}</p>
                  <p class="text-[10px] text-gray-400 truncate">{{ appt.doctor?.department?.name ?? '—' }}</p>
                </td>
                <td class="px-2 sm:px-3 py-2.5">
                  <p class="font-medium text-gray-700 whitespace-nowrap">{{ formatDate(appt.scheduled_time) }}</p>
                  <p class="text-[10px] text-gray-400">{{ formatTime(appt.scheduled_time) }}</p>
                </td>
                <td class="px-2 sm:px-3 py-2.5 hidden md:table-cell">
                  <span v-if="appt.is_telehealth" class="inline-flex items-center gap-1 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded whitespace-nowrap">
                    <Monitor class="w-3 h-3" /> Telemed
                  </span>
                  <span v-else class="text-[10px] text-gray-500 whitespace-nowrap">In-Person</span>
                </td>
                <td class="px-2 sm:px-3 py-2.5">
                  <span :class="statusClass(appt.status)" class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full border capitalize whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="statusDotClass(appt.status)" />
                    {{ appt.status }}
                  </span>
                </td>
                <td class="px-2 sm:px-3 py-2.5 text-right">
                  <div class="relative inline-block" @click.stop>
                    <button @click="toggleMenu(appt.id)" :disabled="isTerminal(appt.status)"
                      class="p-1 rounded-lg transition"
                      :class="isTerminal(appt.status) ? 'text-gray-200 cursor-not-allowed' : 'text-gray-400 hover:text-gray-700 hover:bg-gray-100'">
                      <MoreVertical class="w-3.5 h-3.5" />
                    </button>
                    <div v-if="openMenuId === appt.id && !isTerminal(appt.status)"
                      class="absolute right-1 bottom-full mb-2 w-28 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1">
                      <button v-if="appt.status === 'pending'" @click="handleAction(appt.id,'confirmed');closeMenu()" :disabled="store.loading"
                        class="flex items-center gap-2 w-full px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50 transition disabled:opacity-50">
                        <CheckCircle class="w-3 h-3" /> Confirm
                      </button>
                      <button v-if="appt.status === 'confirmed'" @click="handleAction(appt.id,'completed');closeMenu()" :disabled="store.loading"
                        class="flex items-center gap-2 w-full px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition disabled:opacity-50">
                        <CheckCircle class="w-3 h-3" /> Complete
                      </button>
                      <button v-if="['pending','confirmed'].includes(appt.status)" @click="handleAction(appt.id,'cancelled');closeMenu()" :disabled="store.loading"
                        class="flex items-center gap-2 w-full px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition disabled:opacity-50">
                        <XCircle class="w-3 h-3" /> Cancel
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- Pagination -->
          <TablePagination
            :page="page" :total-pages="totalPages" :total="total" :per-page="perPage"
            @prev="prev" @next="next" @go-to="goTo"
          />
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import {
  CalendarDays, Clock, CheckCircle, XCircle,
  AlertCircle, Search, Monitor, MoreVertical,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";
import { usePagination } from "../../composables/usePagination";
import TablePagination from "../../components/common/TablePagination.vue";
import { useToast } from "../../composables/useToast";

const { showToast } = useToast();

const store = useAppointmentStore();
const search       = ref("");
const statusFilter = ref("");
const typeFilter   = ref("");
const openMenuId   = ref(null);

// ── Filtered list — must be declared BEFORE usePagination ────────────────────
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

const { page, perPage, total, totalPages, paginated, reset, prev, next, goTo } = usePagination(filtered, 10);
watch(filtered, reset);

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
  const labels = { confirmed: "confirmed", completed: "completed", cancelled: "cancelled" };
  try {
    await store.updateStatus(id, status);
    showToast(`Appointment ${labels[status] ?? status} successfully`, "success");
  } catch (err) {
    const msg = err.response?.data?.message || `Failed to update appointment status.`;
    showToast(msg, "error");
  }
}
</script>
