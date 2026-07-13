<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-lg mx-auto">
      <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">My Queue Status</h1>
        <p class="text-xs text-gray-500 mt-0.5">Real-time position in your doctor's queue</p>
      </div>

      <!-- Loading -->
      <div v-if="loading && !queueEntry"
        class="bg-white rounded-xl border border-gray-100 p-8 flex items-center justify-center">
        <Loader2 class="w-6 h-6 animate-spin text-[#004795]" />
      </div>

      <!-- No queue entry found -->
      <div v-else-if="!queueEntry"
        class="bg-white rounded-xl border border-gray-100 shadow-sm py-16 flex flex-col items-center text-gray-400">
        <ClipboardList class="w-12 h-12 mb-3 text-gray-200" />
        <p class="text-sm font-semibold text-gray-500">No active queue entry</p>
        <p class="text-xs text-gray-400 mt-1 text-center px-8">
          <span v-if="nearestAppointment">
            You have an appointment on {{ formatDate(nearestAppointment.scheduled_time) }},
            but the doctor's queue has not been opened yet.
          </span>
          <span v-else-if="hasPendingAppointment">
            Your appointment is still <strong>pending</strong> — it must be confirmed by the doctor before you enter the queue.
          </span>
          <span v-else>You have no upcoming appointments.</span>
        </p>
        <router-link to="/patient/appointments"
          class="mt-4 text-xs font-semibold text-[#004795] hover:underline">
          View Appointments
        </router-link>
      </div>

      <!-- Active queue entry -->
      <template v-else>

        <!-- ── WAITING state ─────────────────────────────────────── -->
        <div v-if="queueEntry.status === 'waiting'"
          class="bg-white rounded-xl border border-amber-200 shadow-sm p-6 mb-5">

          <div class="flex items-center justify-between mb-5">
            <span class="text-xs font-bold px-3 py-1 rounded-full border bg-amber-50 text-amber-700 border-amber-200">
              Waiting
            </span>
            <button @click="refresh" :disabled="loading"
              class="p-2 rounded-lg text-gray-400 hover:bg-gray-50 transition">
              <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
            </button>
          </div>

          <div class="text-center py-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Your Queue Number</p>
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-4 border-amber-400 bg-amber-50 mb-4">
              <span class="text-5xl font-extrabold text-amber-700">{{ queueEntry.queue_number }}</span>
            </div>

            <!-- Patients ahead -->
            <div class="bg-amber-50 border border-amber-100 rounded-xl px-5 py-4 mt-2">
              <p v-if="patientsAhead === 0" class="text-sm font-bold text-amber-700">
                You're next! 🎉
              </p>
              <p v-else class="text-sm font-semibold text-gray-700">
                <span class="text-amber-600 font-bold">{{ patientsAhead }}</span>
                patient{{ patientsAhead !== 1 ? 's' : '' }} ahead of you
              </p>
              <p class="text-xs text-gray-400 mt-1">
                Estimated wait: ~{{ estimatedWait }} minutes
              </p>
              <!-- Progress bar -->
              <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-amber-400 h-1.5 rounded-full transition-all duration-500"
                  :style="{ width: progressWidth + '%' }" />
              </div>
              <p class="text-[10px] text-gray-400 mt-1.5">
                Position {{ queueEntry.queue_number }} of {{ totalInQueue }}
              </p>
            </div>
          </div>
        </div>

        <!-- ── IN CONSULTATION state ──────────────────────────────── -->
        <div v-else-if="queueEntry.status === 'in_consultation'"
          class="bg-blue-600 text-white rounded-xl shadow-md p-6 mb-5">
          <div class="flex items-center justify-between mb-5">
            <span class="text-xs font-bold px-3 py-1 rounded-full border bg-white/20 text-white border-white/30">
              In Consultation
            </span>
            <button @click="refresh" :disabled="loading"
              class="p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition">
              <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
            </button>
          </div>
          <div class="text-center py-4">
            <p class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-3">Your Queue Number</p>
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-4 border-white/50 bg-white/20 mb-4">
              <span class="text-5xl font-extrabold text-white">{{ queueEntry.queue_number }}</span>
            </div>
            <div class="bg-white/20 border border-white/30 rounded-xl px-5 py-4">
              <p class="text-base font-bold text-white">🩺 It's your turn!</p>
              <p class="text-sm text-blue-100 mt-1">Please proceed to the consultation room.</p>
            </div>
          </div>
        </div>

        <!-- ── COMPLETED state ───────────────────────────────────── -->
        <div v-else-if="queueEntry.status === 'completed'"
          class="bg-white rounded-xl border border-emerald-200 shadow-sm p-6 mb-5">
          <div class="flex items-center justify-between mb-5">
            <span class="text-xs font-bold px-3 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">
              Completed
            </span>
            <button @click="refresh" class="p-2 rounded-lg text-gray-400 hover:bg-gray-50 transition">
              <RefreshCw class="w-4 h-4" />
            </button>
          </div>
          <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
              <CheckCircle class="w-8 h-8 text-emerald-600" />
            </div>
            <p class="text-base font-bold text-gray-800">Consultation complete</p>
            <p class="text-xs text-gray-400 mt-1">Your visit has been recorded. Feel better soon!</p>
          </div>
        </div>

        <!-- ── SKIPPED / NO SHOW state ───────────────────────────── -->
        <div v-else
          class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-5">
          <div class="flex items-center justify-between mb-5">
            <span :class="statusBannerClass" class="text-xs font-bold px-3 py-1 rounded-full border capitalize">
              {{ queueEntry.status?.replace("_", " ") }}
            </span>
            <button @click="refresh" class="p-2 rounded-lg text-gray-400 hover:bg-gray-50 transition">
              <RefreshCw class="w-4 h-4" />
            </button>
          </div>
          <div class="text-center py-4">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full border-4 border-gray-200 bg-gray-50 mb-4">
              <span class="text-4xl font-extrabold text-gray-400">{{ queueEntry.queue_number }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-2">
              <span v-if="queueEntry.status === 'skipped'">You were skipped. Please check with the receptionist.</span>
              <span v-else>You were marked as no-show. Please contact the hospital.</span>
            </p>
          </div>
        </div>

        <!-- ── Appointment info card ─────────────────────────────── -->
        <div v-if="nearestAppointment" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-5">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Your Appointment</h3>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
              <Stethoscope class="w-5 h-5 text-[#004795]" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">
                Dr. {{ nearestAppointment.doctor?.user?.first_name ?? '' }}
                {{ nearestAppointment.doctor?.user?.last_name ?? '' }}
              </p>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ nearestAppointment.department?.name ?? nearestAppointment.doctor?.department?.name ?? '' }}
              </p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ formatTime(nearestAppointment.scheduled_time) }}
                <span class="mx-1 text-gray-300">·</span>
                {{ formatDate(nearestAppointment.scheduled_time) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 flex items-start gap-3">
          <Info class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" />
          <p class="text-xs text-blue-600 leading-relaxed">
            Stay nearby when your number is close. This page auto-refreshes every 30 seconds.
            You can also tap the refresh button to get the latest status.
          </p>
        </div>
      </template>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
  Loader2, ClipboardList, RefreshCw,
  Stethoscope, Info, CheckCircle,
} from "lucide-vue-next";
import { useAuthStore } from "../../../stores/authStore";
import queueApi from "../../../api/queueApi";
import api from "../../../api/axios";

const authStore  = useAuthStore();
const loading    = ref(false);
const queueEntry = ref(null);
const allEntries = ref([]);
const nearestAppointment = ref(null);
let   pollTimer  = null;
const today      = new Date().toISOString().split("T")[0];

const patientId = computed(() => authStore.user?.id);

// Track whether the patient has a pending (not yet confirmed) appointment
const hasPendingAppointment = ref(false);

// ── Computed queue stats ──────────────────────────────────────────────────

// Count entries still waiting with a lower queue number than mine
const patientsAhead = computed(() => {
  if (!queueEntry.value || queueEntry.value.status !== "waiting") return 0;
  return allEntries.value.filter(
    (e) => e.status === "waiting" && e.queue_number < queueEntry.value.queue_number
  ).length;
});

// Total active entries (waiting + in_consultation)
const totalInQueue = computed(() => {
  return allEntries.value.filter(
    (e) => e.status === "waiting" || e.status === "in_consultation"
  ).length;
});

// Progress: how far through the queue the patient is (100% = their turn)
const progressWidth = computed(() => {
  if (!queueEntry.value || totalInQueue.value === 0) return 0;
  const position = patientsAhead.value + 1; // 1-based
  const total    = totalInQueue.value;
  return Math.max(5, Math.round(((total - position + 1) / total) * 100));
});

const estimatedWait = computed(() => patientsAhead.value * 10);

const statusBannerClass = computed(() => ({
  waiting:         "bg-amber-50 text-amber-700 border-amber-200",
  in_consultation: "bg-blue-50 text-blue-700 border-blue-100",
  completed:       "bg-emerald-50 text-emerald-700 border-emerald-200",
  skipped:         "bg-orange-50 text-orange-600 border-orange-200",
  no_show:         "bg-gray-100 text-gray-500 border-gray-200",
}[queueEntry.value?.status] ?? "bg-gray-100 text-gray-500 border-gray-200"));

// ── Data loading ──────────────────────────────────────────────────────────
async function refresh() {
  if (!patientId.value) return;
  loading.value = true;
  try {
    const apptRes = await api.get("/appointments");
    const appts   = apptRes.data?.data ?? apptRes.data ?? [];

    // Only confirmed appointments enter the queue.
    // Pending = awaiting doctor approval — no queue entry exists for them.
    const upcoming = appts
      .filter((a) => a.status === "confirmed")
      .sort((a, b) => new Date(a.scheduled_time) - new Date(b.scheduled_time));

    // Track pending appointments so we can show the right empty-state message
    hasPendingAppointment.value = appts.some((a) => a.status === "pending");

    if (!upcoming.length) {
      queueEntry.value = null;
      allEntries.value = [];
      nearestAppointment.value = null;
      return;
    }

    // Walk through confirmed appointments in order, find the first one that
    // has an active (non-terminal) queue entry — waiting or in_consultation.
    // If all found entries are completed/skipped/no_show, show the most
    // recent terminal entry as a fallback so the screen isn't empty.
    const TERMINAL_STATUSES = ["completed", "skipped", "no_show"];

    let activeMatch      = null;  // waiting or in_consultation
    let fallbackMatch    = null;  // completed/skipped/no_show (last resort)
    let activeAppt       = null;
    let fallbackAppt     = null;
    let activeAllEntries = [];

    for (const appt of upcoming) {
      // Determine which date to query
      const apptDate = new Date(appt.scheduled_time).toISOString().split("T")[0];

      // Try today first, then fall back to the appointment's own date
      const datesToTry = apptDate === today ? [today] : [today, apptDate];

      let list = [];
      for (const d of datesToTry) {
        const qRes = await queueApi.getDoctorQueue(appt.doctor_id, d);
        list = qRes.data?.data ?? qRes.data ?? [];
        if (list.some((e) => e.appointment_id === appt.id)) break;
      }

      const entry = list.find((e) => e.appointment_id === appt.id) ?? null;

      if (entry && !TERMINAL_STATUSES.includes(entry.status)) {
        // Found a live entry — use it and stop searching
        activeMatch      = entry;
        activeAppt       = appt;
        activeAllEntries = list;
        break;
      }

      // Keep the first terminal entry as a fallback (covers the "all done" case)
      if (entry && !fallbackMatch) {
        fallbackMatch = entry;
        fallbackAppt  = appt;
        activeAllEntries = list;
      }
    }

    if (activeMatch) {
      queueEntry.value       = activeMatch;
      nearestAppointment.value = activeAppt;
      allEntries.value       = activeAllEntries;
    } else if (fallbackMatch) {
      // No active entry found — show the most recent terminal state
      queueEntry.value       = fallbackMatch;
      nearestAppointment.value = fallbackAppt;
      allEntries.value       = activeAllEntries;
    } else {
      queueEntry.value       = null;
      nearestAppointment.value = upcoming[0] ?? null;
      allEntries.value       = [];
    }
  } catch {
    queueEntry.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await refresh();
  pollTimer = setInterval(refresh, 30_000);
});

onUnmounted(() => clearInterval(pollTimer));

// ── Helpers ───────────────────────────────────────────────────────────────
function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
}

function formatDate(dt) {
  if (!dt) return "";
  return new Date(dt).toLocaleDateString("en-ET", {
    weekday: "short", month: "short", day: "numeric",
  });
}
</script>
