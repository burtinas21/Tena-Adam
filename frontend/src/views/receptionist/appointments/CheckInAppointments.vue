<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Appointments</h1>
          <p class="text-xs text-gray-500 mt-0.5">Book and manage appointments for patients at this hospital.</p>
        </div>
        <button @click="openBookModal"
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
          <CalendarPlus class="w-3.5 h-3.5" />
          Book Appointment
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-0">
          <Search class="w-4 h-4 text-gray-400 flex-shrink-0" />
          <input v-model="searchQuery" type="text"
            placeholder="Search by patient or doctor name..."
            class="flex-1 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent min-w-0" />
        </div>
        <select v-model="statusFilter"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 transition">
          <option value="">All statuses</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input v-model="dateFilter" type="date"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 transition" />
      </div>

      <!-- Status tab pills -->
      <div class="flex gap-2 mb-5 flex-wrap">
        <button v-for="tab in STATUS_TABS" :key="tab.key"
          @click="statusFilter = tab.key === 'all' ? '' : tab.key"
          :class="(statusFilter === '' && tab.key === 'all') || statusFilter === tab.key
            ? 'bg-[#004795] text-white shadow-sm'
            : 'bg-white text-gray-600 border border-gray-200 hover:border-[#004795]/40'"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
          {{ tab.label }}
          <span class="ml-1 opacity-70">({{ tabCount(tab.key) }})</span>
        </button>
      </div>

      <!-- Appointment table -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
          <span class="text-sm font-bold text-gray-800">All Appointments</span>
          <span class="text-xs text-gray-400">{{ filteredAppointments.length }} results</span>
        </div>

        <div v-if="loadingAppts && !store.appointments.length" class="divide-y divide-gray-50">
          <div v-for="n in 5" :key="n" class="px-5 py-4 flex items-center gap-4">
            <div class="w-9 h-9 rounded-full bg-gray-100 animate-pulse flex-shrink-0" />
            <div class="flex-1 space-y-2">
              <div class="h-3 bg-gray-100 animate-pulse rounded w-2/5" />
              <div class="h-2.5 bg-gray-100 animate-pulse rounded w-1/4" />
            </div>
          </div>
        </div>

        <div v-else-if="!filteredAppointments.length" class="py-16 text-center text-gray-400">
          <CalendarDays class="w-8 h-8 mx-auto mb-2 text-gray-300" />
          <p class="text-sm font-medium">No appointments match your filters</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Patient</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Doctor</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Date & Time</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Type</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="appt in filteredAppointments" :key="appt.id" class="hover:bg-gray-50/60 transition-colors">
                <td class="px-5 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#004795]/10 flex items-center justify-center text-xs font-bold text-[#004795] flex-shrink-0">
                      {{ initials(appt.patient?.user) }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800">
                        {{ appt.patient?.user?.first_name }} {{ appt.patient?.user?.last_name }}
                      </p>
                      <p class="text-xs text-gray-400">{{ appt.patient?.user?.phone || appt.patient?.user?.email || '—' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3">
                  <p class="font-medium text-gray-800">Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}</p>
                  <p class="text-xs text-gray-400">{{ appt.department?.name || appt.doctor?.department?.name || '—' }}</p>
                </td>
                <td class="px-5 py-3">
                  <p class="font-medium text-gray-800">{{ formatDate(appt.scheduled_time) }}</p>
                  <p class="text-xs text-gray-400">{{ formatTime(appt.scheduled_time) }}</p>
                </td>
                <td class="px-5 py-3">
                  <span :class="appt.is_telehealth
                    ? 'bg-purple-50 text-purple-600 border-purple-200'
                    : 'bg-blue-50 text-blue-600 border-blue-200'"
                    class="text-[11px] font-semibold px-2 py-0.5 rounded-full border">
                    {{ appt.is_telehealth ? 'Telehealth' : 'In-person' }}
                  </span>
                </td>
                <td class="px-5 py-3">
                  <span :class="statusClass(appt.status)"
                    class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border capitalize">
                    {{ appt.status?.replace('_', ' ') }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- BOOK APPOINTMENT MODAL                                                 -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div v-if="showBookModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeBookModal">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[92vh] flex flex-col">

        <!-- Modal header -->
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Book Appointment</h3>
            <p class="text-xs text-gray-400 mt-0.5">Book for a registered patient at this hospital.</p>
          </div>
          <button @click="closeBookModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Modal body -->
        <form @submit.prevent="submitBooking" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

          <!-- Error -->
          <div v-if="bookError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ bookError }}
          </div>

          <!-- Step 1: Select patient -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Patient <span class="text-red-500">*</span>
            </label>
            <!-- Search input — hidden once a patient is selected -->
            <div v-if="!selectedPatient" class="relative">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
              <input v-model="patientSearch" @input="onPatientSearch" type="text"
                placeholder="Search by name, email or phone..."
                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <!-- Search results dropdown -->
            <div v-if="patientResults.length && !selectedPatient"
              class="border border-gray-200 rounded-xl shadow-lg mt-1 max-h-40 overflow-y-auto bg-white">
              <div v-for="p in patientResults" :key="p.id"
                @click="selectPatient(p)"
                class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer flex items-center gap-3 transition">
                <div class="w-7 h-7 rounded-full bg-[#004795]/10 flex items-center justify-center text-xs font-bold text-[#004795] flex-shrink-0">
                  {{ (p.first_name?.[0] ?? '') + (p.last_name?.[0] ?? '') }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ p.first_name }} {{ p.last_name }}</p>
                  <p class="text-xs text-gray-400">{{ p.phone || p.email }}</p>
                </div>
              </div>
            </div>
            <!-- Selected patient badge — shown instead of input -->
            <div v-if="selectedPatient"
              class="mt-2 flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
              <div class="w-8 h-8 rounded-full bg-[#004795] flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                {{ (selectedPatient.first_name?.[0] ?? '') + (selectedPatient.last_name?.[0] ?? '') }}
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">{{ selectedPatient.first_name }} {{ selectedPatient.last_name }}</p>
                <p class="text-xs text-gray-400">{{ selectedPatient.phone || selectedPatient.email }}</p>
              </div>
              <button type="button" @click="clearPatient" class="text-gray-400 hover:text-gray-600">
                <X class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <!-- Step 2: Select doctor (hospital-scoped) -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Doctor <span class="text-red-500">*</span>
            </label>
            <select v-model="bookForm.doctor_id" @change="onDoctorChange" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
              <option value="" disabled>Select a doctor</option>
              <option v-for="doc in store.doctors" :key="doc.id" :value="doc.id">
                Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }}
                {{ doc.department?.name ? `— ${doc.department.name}` : '' }}
              </option>
            </select>
          </div>

          <!-- Step 3: Select date (only available working days shown) -->
          <div v-if="bookForm.doctor_id">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Appointment Date <span class="text-red-500">*</span>
            </label>
            <input v-model="bookForm.appointment_date" type="date"
              :min="today" @change="onDateChange" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            <p v-if="selectedSchedule" class="text-xs text-gray-500 mt-1">
              Working hours: {{ fmtScheduleTime(selectedSchedule.start_time) }} – {{ fmtScheduleTime(selectedSchedule.end_time) }}
              · {{ selectedSchedule.slot_duration_min }} min slots
            </p>
            <p v-else-if="bookForm.appointment_date && !selectedSchedule" class="text-xs text-red-500 mt-1">
              Doctor does not work on this day. Please choose another date.
            </p>
          </div>

          <!-- Step 4: Select time slot -->
          <div v-if="availableSlots.length">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Time Slot <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-4 gap-2 max-h-32 overflow-y-auto">
              <button v-for="slot in availableSlots" :key="slot"
                type="button"
                @click="bookForm.appointment_time = slot"
                :class="bookForm.appointment_time === slot
                  ? 'bg-[#004795] text-white border-[#004795]'
                  : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795]/50'"
                class="border rounded-lg py-1.5 text-xs font-semibold transition">
                {{ slot }}
              </button>
            </div>
          </div>

          <!-- Step 5: Reason -->
          <div v-if="bookForm.appointment_time">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Reason for Visit <span class="text-red-500">*</span>
            </label>
            <textarea v-model="bookForm.reason" rows="2" required
              placeholder="e.g. Chest pain, follow-up, general checkup..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>

          <!-- Notes (optional) -->
          <div v-if="bookForm.reason?.trim()">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Notes (optional)</label>
            <textarea v-model="bookForm.notes" rows="2"
              placeholder="Any additional notes..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>

        </form>

        <!-- Modal footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <button type="button" @click="closeBookModal"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Cancel
          </button>
          <button @click="submitBooking" :disabled="bookingLoading || !canSubmit"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2">
            <Loader2 v-if="bookingLoading" class="w-3.5 h-3.5 animate-spin" />
            Confirm Booking
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  CalendarPlus, CalendarDays, Search, X, AlertCircle, Loader2,
} from "lucide-vue-next";
import { useReceptionistStore } from "../../../stores/receptionistStore";
import { useToast } from "../../../composables/useToast";
import receptionistApi from "../../../api/receptionistApi";

const store = useReceptionistStore();
const { showToast } = useToast();

// ── Appointment list filters ──────────────────────────────────────────────────
const loadingAppts = ref(false);
const searchQuery  = ref("");
const statusFilter = ref("");
const dateFilter   = ref("");

const STATUS_TABS = [
  { key: "all",       label: "All"       },
  { key: "pending",   label: "Pending"   },
  { key: "confirmed", label: "Confirmed" },
  { key: "completed", label: "Completed" },
  { key: "cancelled", label: "Cancelled" },
];

function tabCount(key) {
  if (key === "all") return store.appointments.length;
  return store.appointments.filter((a) => a.status === key).length;
}

const filteredAppointments = computed(() => {
  let list = store.appointments;
  if (statusFilter.value) list = list.filter((a) => a.status === statusFilter.value);
  if (dateFilter.value)   list = list.filter((a) => a.scheduled_time?.startsWith(dateFilter.value));
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter((a) => {
      const patient = `${a.patient?.user?.first_name ?? ""} ${a.patient?.user?.last_name ?? ""}`.toLowerCase();
      const doctor  = `${a.doctor?.user?.first_name ?? ""} ${a.doctor?.user?.last_name ?? ""}`.toLowerCase();
      return patient.includes(q) || doctor.includes(q);
    });
  }
  return [...list].sort((a, b) => new Date(b.scheduled_time) - new Date(a.scheduled_time));
});

// ── Book appointment modal ────────────────────────────────────────────────────
const showBookModal  = ref(false);
const bookError      = ref(null);
const bookingLoading = ref(false);

// Doctor schedules for selected doctor (keyed by day_of_week 0-6)
const doctorSchedules = ref([]);

const bookForm = ref({
  doctor_id:        "",
  appointment_date: "",
  appointment_time: "",
  reason:           "",
  notes:            "",
});

// Patient search inside modal
const patientSearch    = ref("");
const patientResults   = ref([]);
const selectedPatient  = ref(null);
let   patientTimer     = null;

const today = new Date().toISOString().slice(0, 10);

// The schedule entry for the chosen date's day-of-week
const selectedSchedule = computed(() => {
  if (!bookForm.value.appointment_date) return null;
  const dow = new Date(bookForm.value.appointment_date + "T00:00:00").getDay(); // 0=Sun
  return doctorSchedules.value.find((s) => s.day_of_week === dow && s.is_available !== false) ?? null;
});

// Generate time slots from the doctor's schedule for the selected day
const availableSlots = computed(() => {
  const sched = selectedSchedule.value;
  if (!sched) return [];

  const slots = [];
  const [sh, sm] = sched.start_time.split(":").map(Number);
  const [eh, em] = sched.end_time.split(":").map(Number);
  const dur = sched.slot_duration_min ?? 30;

  // Lunch break bounds (optional)
  let lunchStart = null, lunchEnd = null;
  if (sched.lunch_start && sched.lunch_end) {
    lunchStart = sched.lunch_start.slice(0, 5);
    lunchEnd   = sched.lunch_end.slice(0, 5);
  }

  let cur = sh * 60 + sm;
  const end = eh * 60 + em;

  while (cur + dur <= end) {
    const hh  = String(Math.floor(cur / 60)).padStart(2, "0");
    const mm  = String(cur % 60).padStart(2, "0");
    const t   = `${hh}:${mm}`;

    // Skip lunch break
    if (!lunchStart || t < lunchStart || t >= lunchEnd) {
      slots.push(t);
    }
    cur += dur;
  }
  return slots;
});

const canSubmit = computed(() =>
  selectedPatient.value &&
  bookForm.value.doctor_id &&
  bookForm.value.appointment_date &&
  bookForm.value.appointment_time &&
  bookForm.value.reason?.trim() &&
  !!selectedSchedule.value
);

function openBookModal() {
  bookError.value  = null;
  patientSearch.value  = "";
  patientResults.value = [];
  selectedPatient.value = null;
  doctorSchedules.value = [];
  bookForm.value = { doctor_id: "", appointment_date: "", appointment_time: "", reason: "", notes: "" };
  showBookModal.value = true;
}

function closeBookModal() {
  showBookModal.value = false;
}

// Patient search
function onPatientSearch() {
  clearTimeout(patientTimer);
  patientResults.value = [];
  if (patientSearch.value.length < 2) return;
  patientTimer = setTimeout(async () => {
    try {
      const res = await receptionistApi.searchPatients(patientSearch.value);
      patientResults.value = res.data?.data ?? [];
    } catch { /* silent */ }
  }, 350);
}

function selectPatient(p) {
  selectedPatient.value = p;
  patientResults.value  = [];
  patientSearch.value   = "";
}

function clearPatient() {
  selectedPatient.value = null;
  patientSearch.value   = "";
}

// When doctor changes, load their schedules
async function onDoctorChange() {
  bookForm.value.appointment_date = "";
  bookForm.value.appointment_time = "";
  doctorSchedules.value = [];
  if (!bookForm.value.doctor_id) return;
  try {
    const res = await receptionistApi.getDoctorSchedules();
    const all = res.data?.data ?? res.data ?? [];
    // Filter to the selected doctor
    doctorSchedules.value = all.filter((s) => s.doctor_id === bookForm.value.doctor_id);
  } catch { /* silent */ }
}

// Reset time when date changes
function onDateChange() {
  bookForm.value.appointment_time = "";
}

async function submitBooking() {
  bookError.value = null;
  if (!canSubmit.value) return;
  try {
    bookingLoading.value = true;
    await receptionistApi.bookAppointment({
      patient_id:       selectedPatient.value.id,
      doctor_id:        bookForm.value.doctor_id,
      appointment_date: bookForm.value.appointment_date,
      appointment_time: bookForm.value.appointment_time,
      reason:           bookForm.value.reason,
      notes:            bookForm.value.notes || null,
      is_telehealth:    false,
    });
    closeBookModal();
    await store.fetchAppointments();
    const name = `${selectedPatient.value.first_name} ${selectedPatient.value.last_name}`.trim();
    showToast(`Appointment booked for ${name}`, "success");
  } catch (err) {
    const errors = err.response?.data?.errors;
    const msg = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to book appointment.";
    bookError.value = msg;
    showToast(msg, "error");
  } finally {
    bookingLoading.value = false;
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function initials(user) {
  return ((user?.first_name?.[0] ?? "") + (user?.last_name?.[0] ?? "")).toUpperCase() || "?";
}

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
}

function fmtScheduleTime(t) {
  if (!t) return "";
  const [h, m] = t.split(":");
  const d = new Date();
  d.setHours(+h, +m);
  return d.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
}

function statusClass(status) {
  return {
    pending:   "bg-amber-50 text-amber-700 border-amber-200",
    confirmed: "bg-emerald-50 text-emerald-700 border-emerald-200",
    completed: "bg-blue-50 text-blue-600 border-blue-200",
    cancelled: "bg-red-50 text-red-600 border-red-200",
    no_show:   "bg-gray-50 text-gray-500 border-gray-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

onMounted(async () => {
  loadingAppts.value = true;
  await Promise.all([store.fetchAppointments(), store.fetchDoctors()]);
  loadingAppts.value = false;
});
</script>
