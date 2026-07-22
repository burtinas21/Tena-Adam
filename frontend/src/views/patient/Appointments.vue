<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-5xl mx-auto">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            My Appointments
          </h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Book and manage your medical appointments
          </p>
        </div>

        <!-- Book button: disabled + tooltip when profile incomplete -->
        <div class="relative group">
          <button
            @click="openBook"
            :disabled="!profileActive"
            :class="
              profileActive
                ? 'bg-[#004795] hover:bg-[#003670] text-white cursor-pointer shadow-sm'
                : 'bg-gray-200 text-gray-400 cursor-not-allowed'
            "
            class="font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-2 transition"
          >
            <Plus class="w-3.5 h-3.5" /> Book Appointment
          </button>
          <!-- Tooltip shown when button is disabled -->
          <div
            v-if="!profileActive"
            class="absolute right-0 top-full mt-2 z-10 w-64 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
          >
            Complete your profile first — add your address, occupation and a
            primary emergency contact.
            <div
              class="absolute -top-1 right-4 w-2 h-2 bg-gray-900 rotate-45"
            ></div>
          </div>
        </div>
      </div>

      <!-- Stat pills -->
      <div class="flex flex-wrap gap-3 mb-6">
        <StatPill label="Pending" :count="store.pending.length" color="amber" />
        <StatPill
          label="Confirmed"
          :count="store.confirmed.length"
          color="blue"
        />
        <StatPill
          label="Completed"
          :count="store.completed.length"
          color="emerald"
        />
        <StatPill
          label="Cancelled"
          :count="store.cancelled.length"
          color="red"
        />
      </div>

      <!-- Error -->
      <div
        v-if="store.error && !showBook"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />{{ store.error }}
      </div>

      <!-- Profile incomplete banner -->
      <div
        v-if="!profileActive"
        class="mb-4 flex items-center justify-between gap-4 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium rounded-xl px-4 py-3"
      >
        <div class="flex items-center gap-2">
          <AlertCircle class="w-4 h-4 flex-shrink-0 text-amber-500" />
          <span
            >Your profile is incomplete. Add your address, occupation and a
            primary emergency contact to book appointments.</span
          >
        </div>
        <router-link
          to="/patient/profile"
          class="flex-shrink-0 font-bold text-[#004795] underline hover:no-underline"
        >
          Complete Profile
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="store.loading && !store.appointments.length" class="space-y-3">
        <div
          v-for="n in 3"
          :key="n"
          class="h-20 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
      </div>

      <!-- Empty -->
      <div
        v-else-if="!store.appointments.length"
        class="bg-white rounded-xl border border-gray-100 py-16 flex flex-col items-center text-gray-400"
      >
        <CalendarDays class="w-10 h-10 mb-3 text-gray-300" />
        <p class="text-sm font-medium">No appointments yet</p>
        <router-link
          v-if="!profileActive"
          to="/patient/profile"
          class="mt-3 text-xs text-amber-600 font-semibold hover:underline flex items-center gap-1"
        >
          <AlertCircle class="w-3 h-3" /> Complete your profile to book
        </router-link>
        <button
          v-else
          @click="openBook"
          class="mt-3 text-xs text-[#004795] font-semibold hover:underline"
        >
          Book your first appointment
        </button>
      </div>

      <!-- Appointment list -->
      <div v-else class="space-y-3">
        <div
          v-for="appt in store.appointments"
          :key="appt.id"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:shadow-md transition-shadow"
        >
          <div class="flex items-start gap-4">
            <!-- Doctor avatar -->
            <div
              class="w-10 h-10 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0 overflow-hidden"
            >
              <img
                v-if="appt.doctor?.profile_picture_url"
                :src="appt.doctor.profile_picture_url"
                class="w-10 h-10 rounded-full object-cover"
              />
              <span v-else class="text-xs font-bold text-[#004795]">
                {{ initials(appt.doctor) }}
              </span>
            </div>
            <div>
              <p class="font-semibold text-gray-800 text-sm">
                Dr. {{ appt.doctor?.user?.first_name }}
                {{ appt.doctor?.user?.last_name }}
              </p>
              <p class="text-xs text-gray-500 mt-0.5">
                {{
                  appt.department?.name ?? appt.doctor?.department?.name ?? "—"
                }}
              </p>
              <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                  <CalendarDays class="w-3.5 h-3.5 text-gray-400" />
                  {{ formatDate(appt.scheduled_time) }}
                </span>
                <span class="flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-gray-400" />
                  {{ formatTime(appt.scheduled_time) }}
                </span>
                <span
                  v-if="appt.is_telehealth"
                  class="flex items-center gap-1 text-blue-600"
                >
                  <Monitor class="w-3.5 h-3.5" /> Telemedicine
                </span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0">
            <span
              :class="statusClass(appt.status)"
              class="text-xs font-semibold px-2.5 py-0.5 rounded-full border capitalize"
            >
              {{ appt.status }}
            </span>
            <!-- Cancel pending -->
            <button
              v-if="appt.status === 'pending'"
              @click="handleCancel(appt.id)"
              :disabled="store.loading"
              class="text-xs font-semibold text-red-500 hover:text-red-700 transition"
            >
              Cancel
            </button>
            <!-- Reschedule pending -->
            <button
              v-if="appt.status === 'pending'"
              @click="openReschedule(appt)"
              class="text-xs font-semibold text-[#004795] hover:underline transition"
            >
              Reschedule
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Book Appointment Modal ─────────────────────────────────────── -->
    <div
      v-if="showBook"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="closeBook"
    >
      <div
        class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col"
      >
        <div
          class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0"
        >
          <h3 class="text-sm font-bold text-gray-800">Book Appointment</h3>
          <button
            @click="closeBook"
            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <!-- Error -->
          <div
            v-if="bookError"
            class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
          >
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
              bookError
            }}
          </div>

          <!-- Step 1: Doctor -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Select Doctor <span class="text-red-500">*</span>
            </label>
            <select
              v-model="bookForm.doctor_id"
              required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              @change="onDoctorChange"
            >
              <option value="" disabled>Choose a doctor</option>
              <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                Dr. {{ doc.user?.first_name }} {{ doc.user?.last_name }} —
                {{ doc.department?.name }}
              </option>
            </select>
          </div>

          <!-- Step 2: Date -->
          <div v-if="bookForm.doctor_id">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Appointment Date <span class="text-red-500">*</span>
            </label>
            <input
              v-model="bookForm.appointment_date"
              type="date"
              required
              :min="today"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
              @change="loadAvailableSlots"
            />
          </div>

          <!-- Step 3: Available slots -->
          <div v-if="bookForm.appointment_date && bookForm.doctor_id">
            <label class="block text-xs font-semibold text-gray-700 mb-2">
              Available Time Slots <span class="text-red-500">*</span>
            </label>
            <div
              v-if="slotsLoading"
              class="flex items-center gap-2 text-xs text-gray-500"
            >
              <Loader2 class="w-3.5 h-3.5 animate-spin" /> Loading slots...
            </div>
            <div
              v-else-if="!availableSlots.length"
              class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 flex items-center gap-2"
            >
              <AlertCircle class="w-3.5 h-3.5 text-amber-500" />
              No available slots on this date. Please pick another day.
            </div>
            <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-2">
              <button
                v-for="slot in availableSlots"
                :key="slot.id || slot.start_time"
                type="button"
                @click="selectSlot(slot)"
                :class="
                  selectedSlotTime === formatSlotTime(slot.start_time)
                    ? 'bg-[#004795] text-white border-[#004795]'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
                "
                class="py-2 text-xs font-semibold rounded-lg border transition"
              >
                {{ formatSlotTime(slot.start_time) }}
              </button>
            </div>
          </div>

          <!-- Step 4: Reason -->
          <div v-if="bookForm.appointment_time">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              Reason <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="bookForm.reason"
              rows="3"
              required
              placeholder="Describe your symptoms or reason for visit..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none"
            />
          </div>

          <!-- Telemedicine toggle -->
          <div
            v-if="
              bookForm.appointment_time &&
              selectedDoctorObj?.is_telehealth_available
            "
            class="flex items-center gap-3"
          >
            <button
              type="button"
              @click="bookForm.is_telehealth = !bookForm.is_telehealth"
              :class="bookForm.is_telehealth ? 'bg-[#004795]' : 'bg-gray-200'"
              class="relative w-10 h-5 rounded-full transition-colors duration-200"
            >
              <span
                :class="
                  bookForm.is_telehealth ? 'translate-x-5' : 'translate-x-0.5'
                "
                class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
              />
            </button>
            <span class="text-sm text-gray-700 font-medium"
              >Telemedicine appointment</span
            >
          </div>
        </div>

        <div
          class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0"
        >
          <button
            type="button"
            @click="closeBook"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
            Cancel
          </button>
          <button
            @click="handleBook"
            :disabled="!canBook || bookSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
          >
            <Loader2 v-if="bookSaving" class="w-3.5 h-3.5 animate-spin" />
            Confirm Booking
          </button>
        </div>
      </div>
    </div>
  </main>

  <!-- ── Reschedule Modal ───────────────────────────────────────────── -->
  <div
    v-if="showReschedule"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="closeReschedule"
  >
    <div
      class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[85vh] flex flex-col"
    >
      <div
        class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0"
      >
        <div>
          <h3 class="text-sm font-bold text-gray-800">
            Reschedule Appointment
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">
            Pick a new date and time slot
          </p>
        </div>
        <button
          @click="closeReschedule"
          class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
        >
          <X class="w-4 h-4" />
        </button>
      </div>
      <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
        <div
          v-if="rescheduleError"
          class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
        >
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{
            rescheduleError
          }}
        </div>
        <!-- New date -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            New Date <span class="text-red-500">*</span>
          </label>
          <input
            v-model="rescheduleDate"
            type="date"
            :min="today"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            @change="loadRescheduleSlots"
          />
        </div>
        <!-- Available slots -->
        <div v-if="rescheduleDate">
          <label class="block text-xs font-semibold text-gray-700 mb-2">
            Available Slots <span class="text-red-500">*</span>
          </label>
          <div
            v-if="rescheduleSlotsLoading"
            class="flex items-center gap-2 text-xs text-gray-500"
          >
            <Loader2 class="w-3.5 h-3.5 animate-spin" /> Loading slots...
          </div>
          <div
            v-else-if="!rescheduleSlots.length"
            class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 flex items-center gap-2"
          >
            <AlertCircle class="w-3.5 h-3.5 text-amber-500" />
            No available slots on this date.
          </div>
          <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-2">
            <button
              v-for="slot in rescheduleSlots"
              :key="slot.id || slot.start_time"
              type="button"
              @click="
                rescheduleTime = slot.start_time.substring(11, 16);
                selectedRescheduleSlotTime = formatSlotTime(slot.start_time);
                selectedRescheduleSlotId = slot.id;
              "
              :class="
                selectedRescheduleSlotTime === formatSlotTime(slot.start_time)
                  ? 'bg-[#004795] text-white border-[#004795]'
                  : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'
              "
              class="py-2 text-xs font-semibold rounded-lg border transition"
            >
              {{ formatSlotTime(slot.start_time) }}
            </button>
          </div>
        </div>
      </div>
      <div
        class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0"
      >
        <button
          @click="closeReschedule"
          class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
        >
          Cancel
        </button>
        <button
          @click="handleReschedule"
          :disabled="!selectedRescheduleSlotId || rescheduleSaving"
          class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2"
        >
          <Loader2 v-if="rescheduleSaving" class="w-3.5 h-3.5 animate-spin" />
          Confirm Reschedule
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, defineComponent, h } from "vue";
import { useRoute } from "vue-router";
import {
  Plus,
  CalendarDays,
  Clock,
  Monitor,
  AlertCircle,
  X,
  Loader2,
} from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";
import doctorApi from "../../api/doctorApi";
import slotApi from "../../api/slotApi";
import api from "../../api/axios";

// ── Tiny StatPill component ───────────────────────────────────────────────
const colorMap = {
  amber: "bg-amber-50 text-amber-700 border-amber-200",
  blue: "bg-blue-50 text-blue-700 border-blue-200",
  emerald: "bg-emerald-50 text-emerald-700 border-emerald-200",
  red: "bg-red-50 text-red-600 border-red-200",
};
const StatPill = defineComponent({
  props: { label: String, count: Number, color: String },
  setup(p) {
    return () =>
      h(
        "div",
        {
          class: `inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold ${colorMap[p.color]}`,
        },
        [h("span", {}, p.count), h("span", {}, p.label)],
      );
  },
});

const store = useAppointmentStore();
const route = useRoute();
const doctors = ref([]);
const today = new Date().toISOString().split("T")[0];

// ── Profile active check (reads patient_status from backend) ─────────────
const profileActive = ref(null); // null = loading, true = active, false = pending/incomplete
const profileLoading = ref(false);

async function checkPatientProfile() {
  profileLoading.value = true;
  try {
    const res = await api.get("/patient/profile");
    // backend returns data.patient_status: 'active' | 'pending'
    const patient = res.data?.data ?? res.data;
    profileActive.value = patient?.patient_status === "active";
  } catch {
    // 404 = no patient profile yet → definitely not active
    profileActive.value = false;
  } finally {
    profileLoading.value = false;
  }
}

// Re-check every time the user navigates to this route (e.g. coming back from profile page)
watch(
  () => route.fullPath,
  () => checkPatientProfile(),
);

// ── Book modal state ──────────────────────────────────────────────────────
const showBook = ref(false);
const bookError = ref(null);
const bookSaving = ref(false);
const slotsLoading = ref(false);
const availableSlots = ref([]);
const selectedSlotTime = ref("");

const bookForm = ref({
  doctor_id: "",
  appointment_date: "",
  appointment_time: "",
  reason: "",
  is_telehealth: false,
});

const selectedDoctorObj = computed(
  () => doctors.value.find((d) => d.id === bookForm.value.doctor_id) ?? null,
);

const canBook = computed(
  () =>
    bookForm.value.doctor_id &&
    bookForm.value.appointment_date &&
    bookForm.value.appointment_time &&
    bookForm.value.reason.trim(),
);

// ── Reschedule modal state ────────────────────────────────────────────────
const showReschedule = ref(false);
const rescheduleAppointment = ref(null);
const rescheduleDate = ref("");
const rescheduleTime = ref("");
const rescheduleSlots = ref([]);
const rescheduleSlotsLoading = ref(false);
const rescheduleSaving = ref(false);
const rescheduleError = ref(null);
const selectedRescheduleSlotTime = ref("");
const selectedRescheduleSlotId = ref(null);

function openReschedule(appt) {
  rescheduleAppointment.value = appt;
  rescheduleDate.value = "";
  rescheduleTime.value = "";
  rescheduleSlots.value = [];
  rescheduleError.value = null;
  selectedRescheduleSlotTime.value = "";
  selectedRescheduleSlotId.value = null;
  showReschedule.value = true;
}

function closeReschedule() {
  showReschedule.value = false;
  rescheduleError.value = null;
}

async function loadRescheduleSlots() {
  if (!rescheduleDate.value || !rescheduleAppointment.value) return;
  rescheduleTime.value = "";
  rescheduleSlots.value = [];
  selectedRescheduleSlotTime.value = "";
  selectedRescheduleSlotId.value = null;
  rescheduleSlotsLoading.value = true;
  try {
    const slots = await slotApi.getAvailable(
      rescheduleAppointment.value.doctor_id,
      rescheduleDate.value,
    );
    rescheduleSlots.value = slots;
  } catch {
    rescheduleSlots.value = [];
  } finally {
    rescheduleSlotsLoading.value = false;
  }
}

async function handleReschedule() {
  if (!selectedRescheduleSlotId.value || !rescheduleAppointment.value) return;
  rescheduleError.value = null;
  rescheduleSaving.value = true;
  try {
    await store.reschedule(
      rescheduleAppointment.value.id,
      selectedRescheduleSlotId.value,
    );
    closeReschedule();
  } catch (err) {
    const errors = err.response?.data?.errors;
    rescheduleError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Failed to reschedule appointment.";
  } finally {
    rescheduleSaving.value = false;
  }
}
async function loadDoctors() {
  try {
    const res = await doctorApi.getAll();
    doctors.value = res.data?.data ?? res.data ?? [];
  } catch {
    /* silent */
  }
}

onMounted(async () => {
  await Promise.all([store.fetchAll(), loadDoctors(), checkPatientProfile()]);
});

// ── Slot loading ──────────────────────────────────────────────────────────
async function loadAvailableSlots() {
  if (!bookForm.value.doctor_id || !bookForm.value.appointment_date) return;
  selectedSlotTime.value = "";
  bookForm.value.appointment_time = "";
  availableSlots.value = [];
  slotsLoading.value = true;
  try {
    const slots = await slotApi.getAvailable(
      bookForm.value.doctor_id,
      bookForm.value.appointment_date,
    );
    availableSlots.value = slots;
  } catch {
    /* silent — show empty state */
  } finally {
    slotsLoading.value = false;
  }
}

function onDoctorChange() {
  bookForm.value.appointment_date = "";
  bookForm.value.appointment_time = "";
  selectedSlotTime.value = "";
  availableSlots.value = [];
}

function selectSlot(slot) {
  const t = formatSlotTime(slot.start_time);
  selectedSlotTime.value = t;
  bookForm.value.appointment_time = slot.start_time.substring(11, 16); // HH:MM
}

// ── Book ──────────────────────────────────────────────────────────────────
function openBook() {
  if (!profileActive.value) return; // guard: profile must be active
  bookForm.value = {
    doctor_id: "",
    appointment_date: "",
    appointment_time: "",
    reason: "",
    is_telehealth: false,
  };
  selectedSlotTime.value = "";
  availableSlots.value = [];
  bookError.value = null;
  showBook.value = true;
}

function closeBook() {
  showBook.value = false;
  bookError.value = null;
}

async function handleBook() {
  bookError.value = null;
  try {
    bookSaving.value = true;
    await store.create({
      doctor_id: bookForm.value.doctor_id,
      appointment_date: bookForm.value.appointment_date,
      appointment_time: bookForm.value.appointment_time,
      reason: bookForm.value.reason,
      is_telehealth: bookForm.value.is_telehealth,
    });
    closeBook();
  } catch (err) {
    const errors = err.response?.data?.errors;
    bookError.value = errors
      ? Object.values(errors).flat().join(" ")
      : err.response?.data?.message || "Something went wrong.";
  } finally {
    bookSaving.value = false;
  }
}

async function handleCancel(id) {
  try {
    await store.updateStatus(id, "cancelled");
  } catch {
    /* store.error shows it */
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────
function initials(doctor) {
  const u = doctor?.user;
  return u
    ? ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase()
    : "?";
}

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-ET", {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatSlotTime(startTime) {
  if (!startTime) return "";
  const d = new Date(startTime);
  return isNaN(d)
    ? startTime.substring(11, 16)
    : d.toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
}

function statusClass(status) {
  return (
    {
      pending: "bg-amber-50 text-amber-700 border-amber-200",
      confirmed: "bg-blue-50 text-blue-700 border-blue-200",
      completed: "bg-emerald-50 text-emerald-700 border-emerald-200",
      cancelled: "bg-red-50 text-red-600 border-red-200",
      no_show: "bg-gray-50 text-gray-500 border-gray-200",
    }[status] ?? "bg-gray-50 text-gray-500 border-gray-200"
  );
}
</script>
