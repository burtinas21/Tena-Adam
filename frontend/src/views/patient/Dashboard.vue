<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Top Block -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2">
        <WelcomeBanner
          :name="firstName"
          :completion="profileCompletion"
        />
      </div>
      <div class="grid grid-cols-2 gap-3">
        <MiniMetricCard
          :title="$t('dashboard.upcoming')"
          :value="apptStore.pending.length + apptStore.confirmed.length"
          :icon="Calendar"
          color="blue"
        />
        <MiniMetricCard
          :title="$t('dashboard.completed')"
          :value="apptStore.completed.length"
          :icon="CheckSquare"
          color="emerald"
        />
        <MiniMetricCard
          :title="$t('dashboard.active_rx')"
          :value="activePrescriptions"
          :icon="Pill"
          color="amber"
        />
        <MiniMetricCard
          :title="$t('dashboard.records')"
          :value="medicalRecords"
          :icon="FolderHeart"
          color="gray"
        />
      </div>
    </div>

    <!-- Middle Block -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2">
        <NextAppointmentCard
          :appointment="nextAppointment"
          :loading="apptStore.loading"
          @reschedule="openReschedule"
        />
      </div>
      <div>
        <UpdatesFeed :appointment="nextAppointment" />
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <ActionShortcut :label="$t('action.book_appointment')"  :icon="CalendarPlus" to="/patient/appointments" />
      <ActionShortcut :label="$t('action.join_telemedicine')" :icon="Video"        to="/patient/telemedicine" />
      <ActionShortcut :label="$t('action.search_doctors')"    :icon="UserSearch"   to="/patient/doctors" />
      <ActionShortcut :label="$t('action.view_records')"      :icon="FolderHeart"  to="/patient/medical-records" />
    </div>
  </main>

  <!-- ── Reschedule Modal ───────────────────────────────────────────── -->
  <div v-if="showReschedule"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="closeReschedule">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[85vh] flex flex-col">
      <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
        <div>
          <h3 class="text-sm font-bold text-gray-800">{{ $t('reschedule.title') }}</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $t('reschedule.subtitle') }}</p>
        </div>
        <button @click="closeReschedule" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
          <X class="w-4 h-4" />
        </button>
      </div>
      <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
        <div v-if="rescheduleError"
          class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5">
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ rescheduleError }}
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            {{ $t('reschedule.new_date') }} <span class="text-red-500">*</span>
          </label>
          <input v-model="rescheduleDate" type="date" :min="today"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
            @change="loadRescheduleSlots" />
        </div>
        <div v-if="rescheduleDate">
          <label class="block text-xs font-semibold text-gray-700 mb-2">
            {{ $t('reschedule.available_slots') }} <span class="text-red-500">*</span>
          </label>
          <div v-if="rescheduleSlotsLoading" class="flex items-center gap-2 text-xs text-gray-500">
            <Loader2 class="w-3.5 h-3.5 animate-spin" /> {{ $t('reschedule.loading_slots') }}
          </div>
          <div v-else-if="!rescheduleSlots.length"
            class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 flex items-center gap-2">
            <AlertCircle class="w-3.5 h-3.5 text-amber-500" /> {{ $t('reschedule.no_slots') }}
          </div>
          <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-2">
            <button v-for="slot in rescheduleSlots" :key="slot.id || slot.start_time"
              type="button"
              @click="selectedRescheduleSlotTime = formatSlotTime(slot.start_time); selectedRescheduleSlotId = slot.id"
              :class="selectedRescheduleSlotId === slot.id
                ? 'bg-[#004795] text-white border-[#004795]'
                : 'bg-white text-gray-700 border-gray-200 hover:border-[#004795] hover:text-[#004795]'"
              class="py-2 text-xs font-semibold rounded-lg border transition">
              {{ formatSlotTime(slot.start_time) }}
            </button>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
        <button @click="closeReschedule"
          class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
          {{ $t('button.cancel') }}
        </button>
        <button @click="handleReschedule"
          :disabled="!selectedRescheduleSlotId || rescheduleSaving"
          class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2">
          <Loader2 v-if="rescheduleSaving" class="w-3.5 h-3.5 animate-spin" />
          {{ $t('reschedule.confirm') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import {
  Calendar, CheckSquare, Pill, FolderHeart,
  CalendarPlus, Video, UserSearch, X, AlertCircle, Loader2,
} from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import { useAppointmentStore } from "../../stores/appointmentStore";
import api from "../../api/axios";
import slotApi from "../../api/slotApi";

import WelcomeBanner from "../../components/patientdashboard/WelcomeBanner.vue";
import MiniMetricCard from "../../components/patientdashboard/MiniMetricCard.vue";
import NextAppointmentCard from "../../components/patientdashboard/NextAppointmentCard.vue";
import UpdatesFeed from "../../components/patientdashboard/UpdatesFeed.vue";
import ActionShortcut from "../../components/patientdashboard/ActionShortcut.vue";

const authStore = useAuthStore();
const apptStore = useAppointmentStore();
const { t } = useI18n();

// ── Patient profile state ─────────────────────────────────────────────────
const patient = ref(null);
const activePrescriptions = ref(0);
const medicalRecords = ref(0);
const today = new Date().toISOString().split("T")[0];

// ── Computed ──────────────────────────────────────────────────────────────
const firstName = computed(() => {
  const u = authStore.user;
  return u?.first_name || u?.name?.split(" ")[0] || "there";
});

const profileCompletion = computed(() => {
  if (!patient.value) return 0;
  const p = patient.value;
  const fields = [
    p.address,
    p.occupation,
    p.gender,
    p.date_of_birth,
    p.blood_type,
    p.national_id,
  ];
  const filled = fields.filter(Boolean).length;
  const hasEmergencyContact =
    (p.emergency_contacts?.length ?? p.emergencyContacts?.length ?? 0) > 0;
  // 6 profile fields + emergency contact = 7 items
  return Math.round(((filled + (hasEmergencyContact ? 1 : 0)) / 7) * 100);
});

// Next upcoming appointment (pending or confirmed, soonest first)
const nextAppointment = computed(() => {
  const upcoming = apptStore.appointments.filter(
    (a) => a.status === "pending" || a.status === "confirmed"
  );
  if (!upcoming.length) return null;
  return upcoming.sort(
    (a, b) => new Date(a.scheduled_time) - new Date(b.scheduled_time)
  )[0];
});

// ── Load data ─────────────────────────────────────────────────────────────
async function loadPatientProfile() {
  try {
    const res = await api.get("/patient/profile");
    patient.value = res.data?.data ?? res.data;
  } catch {
    /* silent — fallback to auth user name */
  }
}

async function loadPrescriptions() {
  try {
    const res = await api.get("/prescriptions");
    const list = res.data?.data ?? res.data ?? [];
    activePrescriptions.value = Array.isArray(list)
      ? list.filter((p) => p.status === "active").length
      : 0;
  } catch {
    activePrescriptions.value = 0;
  }
}

async function loadMedicalRecords() {
  try {
    const res = await api.get("/medical-encounters");
    const list = res.data?.data ?? res.data ?? [];
    medicalRecords.value = Array.isArray(list) ? list.length : 0;
  } catch {
    medicalRecords.value = 0;
  }
}

onMounted(async () => {
  await Promise.all([
    apptStore.fetchAll(),
    loadPatientProfile(),
    loadPrescriptions(),
    loadMedicalRecords(),
  ]);
});

// ── Reschedule modal ──────────────────────────────────────────────────────
const showReschedule = ref(false);
const rescheduleAppointment = ref(null);
const rescheduleDate = ref("");
const rescheduleSlots = ref([]);
const rescheduleSlotsLoading = ref(false);
const rescheduleSaving = ref(false);
const rescheduleError = ref(null);
const selectedRescheduleSlotTime = ref("");
const selectedRescheduleSlotId = ref(null);

function openReschedule(appt) {
  rescheduleAppointment.value = appt;
  rescheduleDate.value = "";
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
  rescheduleSlots.value = [];
  selectedRescheduleSlotTime.value = "";
  selectedRescheduleSlotId.value = null;
  rescheduleSlotsLoading.value = true;
  try {
    const slots = await slotApi.getAvailable(
      rescheduleAppointment.value.doctor_id,
      rescheduleDate.value
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
    await apptStore.reschedule(
      rescheduleAppointment.value.id,
      selectedRescheduleSlotId.value
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

function formatSlotTime(startTime) {
  if (!startTime) return "";
  const d = new Date(startTime);
  return isNaN(d)
    ? startTime.substring(11, 16)
    : d.toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
}
</script>
