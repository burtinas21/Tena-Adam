<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-6xl mx-auto">

      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Doctor Leave Requests</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Review and process leave requests. Approve or reject, then handle any affected appointments.
        </p>
      </div>

      <!-- Error banner -->
      <div v-if="error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ error }}
      </div>

      <!-- Leave list -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <!-- Filter bar -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-100">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input v-model="search" type="text" placeholder="Search doctor..."
              class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]" />
          </div>
          <select v-model="statusFilter"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading && !leaves.length" class="p-5 space-y-3">
          <div v-for="n in 4" :key="n" class="h-14 bg-gray-50 rounded-lg animate-pulse" />
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left min-w-[700px]">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Doctor</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Leave Date</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Type</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reason</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="!filtered.length">
                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                  <CalendarOff class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                  <p class="text-sm font-medium">No leave requests found</p>
                </td>
              </tr>
              <tr v-for="leave in filtered" :key="leave.id" class="hover:bg-gray-50/60 transition-colors">
                <!-- Doctor -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
                      <span class="text-[10px] font-bold text-[#004795]">
                        {{ doctorInitials(leave.doctor) }}
                      </span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800 text-xs">
                        Dr. {{ leave.doctor?.user?.first_name }} {{ leave.doctor?.user?.last_name }}
                      </p>
                      <p class="text-[10px] text-gray-400">{{ leave.doctor?.department?.name ?? '—' }}</p>
                    </div>
                  </div>
                </td>
                <!-- Date -->
                <td class="px-5 py-4">
                  <p class="text-xs font-medium text-gray-700">{{ formatDate(leave.leave_date) }}</p>
                </td>
                <!-- Type -->
                <td class="px-5 py-4">
                  <span class="text-xs text-gray-600 capitalize">{{ leave.leave_type ?? '—' }}</span>
                </td>
                <!-- Reason -->
                <td class="px-5 py-4">
                  <p class="text-xs text-gray-600 max-w-[180px] truncate" :title="leave.reason">
                    {{ leave.reason ?? '—' }}
                  </p>
                </td>
                <!-- Status -->
                <td class="px-5 py-4">
                  <span :class="statusClass(leave.status)"
                    class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                    {{ leave.status }}
                  </span>
                </td>
                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div v-if="leave.status === 'pending'" class="flex items-center justify-end gap-1">
                    <button @click="processLeave(leave, 'approved')" :disabled="processing === leave.id"
                      class="px-2 py-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                      Approve
                    </button>
                    <button @click="processLeave(leave, 'rejected')" :disabled="processing === leave.id"
                      class="px-2 py-1 text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                      Reject
                    </button>
                  </div>
                  <span v-else class="text-[10px] text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ─── Reassign Modal ─────────────────────────────────────────────────── -->
    <Teleport to="body">
      <div v-if="reassignModal.open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

          <!-- Modal header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
              <h2 class="text-base font-bold text-gray-800">Reassign Affected Appointments</h2>
              <p class="text-xs text-amber-600 font-medium mt-0.5">
                {{ reassignModal.appointments.length }} confirmed appointment(s) need a replacement doctor
                on {{ formatDate(reassignModal.leaveDate) }}.
              </p>
            </div>
            <button @click="closeReassignModal"
              class="p-1.5 rounded-lg hover:bg-gray-100 transition text-gray-400 hover:text-gray-600">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Appointment list -->
          <div class="overflow-y-auto flex-1 px-6 py-4 space-y-4">
            <div v-for="appt in reassignModal.appointments" :key="appt.id"
              class="rounded-xl border border-gray-200 p-4">

              <!-- Appointment info -->
              <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                  <p class="text-xs font-bold text-gray-800">
                    Patient: {{ appt.patient?.first_name }} {{ appt.patient?.last_name }}
                  </p>
                  <p class="text-[11px] text-gray-500 mt-0.5">
                    Original doctor: Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}
                    &nbsp;·&nbsp; {{ formatTime(appt.scheduled_time) }}
                  </p>
                </div>
                <!-- Reassigned badge -->
                <span v-if="reassignedIds.has(appt.id)"
                  class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                  <CheckCircle class="w-3 h-3" /> Reassigned
                </span>
              </div>

              <!-- Doctor + slot picker (hidden once reassigned) -->
              <template v-if="!reassignedIds.has(appt.id)">
                <!-- Loading available doctors -->
                <div v-if="loadingSlots === appt.id" class="text-xs text-gray-400 flex items-center gap-2 py-2">
                  <Loader2 class="w-4 h-4 animate-spin" /> Loading available doctors...
                </div>

                <template v-else>
                  <!-- Doctor picker -->
                  <div class="mb-2">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                      Select replacement doctor
                    </label>
                    <select v-model="picks[appt.id].doctorIdx"
                      @change="picks[appt.id].slotId = ''"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795]">
                      <option value="">— choose doctor —</option>
                      <option v-for="(d, idx) in (availableSlots[appt.id] ?? [])" :key="idx" :value="idx">
                        Dr. {{ d.doctor.name }}
                        ({{ d.available_slots.length }} slot{{ d.available_slots.length !== 1 ? 's' : '' }} available)
                      </option>
                    </select>
                  </div>

                  <!-- No doctors available -->
                  <p v-if="(availableSlots[appt.id] ?? []).length === 0"
                    class="text-xs text-red-500 font-medium">
                    No available doctors in the same department on this date.
                  </p>

                  <!-- Slot picker -->
                  <template v-if="picks[appt.id].doctorIdx !== ''">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                      Select time slot
                    </label>
                    <div class="flex flex-wrap gap-2">
                      <button v-for="slot in availableSlots[appt.id][picks[appt.id].doctorIdx]?.available_slots"
                        :key="slot.id"
                        @click="picks[appt.id].slotId = slot.id"
                        :class="[
                          'px-3 py-1 text-xs font-semibold rounded-lg border transition',
                          picks[appt.id].slotId === slot.id
                            ? 'bg-[#004795] text-white border-[#004795]'
                            : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
                        ]">
                        {{ formatTime(slot.start_time) }}
                      </button>
                    </div>
                  </template>

                  <!-- Confirm reassign button -->
                  <div class="mt-3 flex items-center gap-2">
                    <button
                      @click="doReassign(appt)"
                      :disabled="!picks[appt.id]?.slotId || submitting === appt.id"
                      class="px-4 py-1.5 text-xs font-bold bg-[#004795] text-white rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#003a7a] transition">
                      <Loader2 v-if="submitting === appt.id" class="w-3.5 h-3.5 animate-spin inline mr-1" />
                      Confirm Reassignment
                    </button>
                    <span v-if="reassignError[appt.id]" class="text-xs text-red-500">
                      {{ reassignError[appt.id] }}
                    </span>
                  </div>
                </template>
              </template>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-500">
              {{ reassignedIds.size }} / {{ reassignModal.appointments.length }} reassigned
            </p>
            <button @click="closeReassignModal"
              class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
              Done
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import {
  Search, AlertCircle, CalendarOff, CheckCircle,
  X, Loader2
} from "lucide-vue-next";
import leaveApi      from "../../api/leaveApi";
import appointmentApi from "../../api/appointmentApi";

// ── State ────────────────────────────────────────────────────────────────────
const leaves      = ref([]);
const loading     = ref(false);
const processing  = ref(null);   // leave id currently being approved/rejected
const error       = ref(null);
const search      = ref("");
const statusFilter = ref("");

// Reassign modal
const reassignModal = reactive({
  open: false,
  leaveDate: null,
  leaveDoctor: null,   // { hospital_id, department_id, id }
  appointments: [],
});
const availableSlots = reactive({});  // appt.id → [{ doctor, available_slots }]
const loadingSlots   = ref(null);     // appt.id being loaded
const picks          = reactive({});  // appt.id → { doctorIdx, slotId }
const submitting     = ref(null);     // appt.id being submitted
const reassignedIds  = ref(new Set());
const reassignError  = reactive({});

// ── Fetch leaves ─────────────────────────────────────────────────────────────
async function fetchLeaves() {
  loading.value = true;
  error.value   = null;
  try {
    const res  = await leaveApi.getAll();
    leaves.value = res.data.data ?? [];
  } catch (e) {
    error.value = e.response?.data?.message ?? "Failed to load leave requests.";
  } finally {
    loading.value = false;
  }
}
onMounted(fetchLeaves);

// ── Filtered list ─────────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = leaves.value;
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter((l) => {
      const name = `${l.doctor?.user?.first_name ?? ""} ${l.doctor?.user?.last_name ?? ""}`.toLowerCase();
      return name.includes(q);
    });
  }
  if (statusFilter.value) {
    list = list.filter((l) => l.status === statusFilter.value);
  }
  return list;
});

// ── Approve / Reject ──────────────────────────────────────────────────────────
async function processLeave(leave, status) {
  processing.value = leave.id;
  error.value      = null;
  try {
    const res = await leaveApi.approve(leave.id, status);

    // Update this leave's status in the list
    const idx = leaves.value.findIndex((l) => l.id === leave.id);
    if (idx !== -1) {
      leaves.value[idx] = { ...leaves.value[idx], ...res.data.data };
    }

    // If approved AND there are confirmed appointments → open reassign modal
    const affectedAppointments = res.data.appointments ?? [];
    if (
      status === "approved" &&
      res.data.appointments_to_reschedule > 0 &&
      affectedAppointments.length > 0
    ) {
      openReassignModal(res.data.data, affectedAppointments);
    }
  } catch (e) {
    error.value = e.response?.data?.message ?? "Failed to process leave request.";
  } finally {
    processing.value = null;
  }
}

// ── Reassign modal ────────────────────────────────────────────────────────────
async function openReassignModal(leave, appointments) {
  reassignModal.open         = true;
  reassignModal.leaveDate    = leave.leave_date;
  reassignModal.leaveDoctor  = leave.doctor;
  reassignModal.appointments = appointments;
  reassignedIds.value        = new Set();

  // Initialise picks + load available doctors for each appointment
  for (const appt of appointments) {
    picks[appt.id]         = { doctorIdx: "", slotId: "" };
    reassignError[appt.id] = null;
    // Use the leave_date string (YYYY-MM-DD) so the slot query targets the right day
    await loadAvailableSlots(appt, leave.leave_date);
  }
}

async function loadAvailableSlots(appt, leaveDate) {
  loadingSlots.value = appt.id;
  try {
    const res = await appointmentApi.getAvailableDoctorSlots({
      hospital_id:       appt.hospital_id,
      department_id:     appt.department_id,
      date:              leaveDate,
      exclude_doctor_id: appt.doctor_id,
    });
    availableSlots[appt.id] = res.data.data ?? [];
  } catch {
    availableSlots[appt.id] = [];
  } finally {
    loadingSlots.value = null;
  }
}

async function doReassign(appt) {
  const pick = picks[appt.id];
  if (!pick?.slotId) return;

  submitting.value       = appt.id;
  reassignError[appt.id] = null;
  try {
    await appointmentApi.adminReschedule(appt.id, pick.slotId);
    reassignedIds.value = new Set([...reassignedIds.value, appt.id]);
  } catch (e) {
    const errs = e.response?.data?.errors;
    reassignError[appt.id] = errs
      ? Object.values(errs).flat().join(" ")
      : (e.response?.data?.message ?? "Reassignment failed.");
  } finally {
    submitting.value = null;
  }
}

function closeReassignModal() {
  reassignModal.open = false;
  reassignModal.appointments = [];
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function doctorInitials(doctor) {
  const f = doctor?.user?.first_name?.[0] ?? "";
  const l = doctor?.user?.last_name?.[0] ?? "";
  return (f + l).toUpperCase() || "?";
}
function formatDate(d) {
  return d ? new Date(d).toLocaleDateString("en-ET", { day: "numeric", month: "short", year: "numeric" }) : "—";
}
function formatTime(dt) {
  return dt ? new Date(dt).toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" }) : "—";
}
function statusClass(status) {
  return {
    pending:  "bg-amber-50 text-amber-700 border-amber-200",
    approved: "bg-emerald-50 text-emerald-700 border-emerald-200",
    rejected: "bg-red-50 text-red-600 border-red-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}
</script>
