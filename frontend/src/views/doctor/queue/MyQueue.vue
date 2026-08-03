<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
      >
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span
              class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
            ></span>
            <span
              class="text-[10px] font-bold text-emerald-600 tracking-widest uppercase"
              >Live Queue</span
            >
          </div>
          <h1 class="text-xl font-bold text-gray-900">My Patient Queue</h1>
          <p class="text-xs text-gray-500 mt-0.5">{{ dateLabel }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap self-end sm:self-auto">
          <!-- Date picker -->
          <input
            v-model="selectedDate"
            type="date"
            @change="reload"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#004795] focus:ring-1 focus:ring-[#004795]/20"
          />

          <!-- Walk-in button -->
          <button
            @click="showWalkIn = true"
            class="flex items-center gap-1.5 bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs px-4 py-2.5 rounded-lg transition shadow-sm"
          >
            <UserPlus class="w-3.5 h-3.5" /> Add Walk-in
          </button>
          <!-- <a href="/doctor/medicalencounter">Medical Encounter</a> -->
          

          <!-- Refresh -->
          <button
            @click="reload"
            :disabled="store.loading"
            class="p-2.5 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition"
            title="Refresh queue"
          >
            <RefreshCw
              class="w-4 h-4"
              :class="{ 'animate-spin': store.loading }"
            />
          </button>
        </div>
      </div>

      <!-- KPI row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-2">
        <KpiCard label="Waiting" :value="store.totalWaiting" color="amber" />
        <KpiCard
          label="In Consultation"
          :value="store.inConsultation.length"
          color="blue"
        />
        <KpiCard
          label="Completed"
          :value="store.totalCompleted"
          color="emerald"
        />
        <KpiCard label="Skipped" :value="store.skipped.length" color="gray" />
      </div>

      <div class="flex justify-end p-4">
  <router-link 
    to="/doctor/medicalencounter" 
    class="inline-flex items-center justify-center px-4 py-2.5 bg-[#004795] hover:bg-[#003670] text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#004795]"
  >
    Medical Encounter
  </router-link>
</div>

      <div
        v-if="store.error"
        class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ store.error }}
      </div>

      <!-- ── Current patient (in consultation) ─────────────────────── -->
      <div
        v-if="store.currentPatient"
        class="bg-blue-600 text-white rounded-xl p-5 mb-5 shadow-md"
      >
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <p
              class="text-[10px] font-bold tracking-widest uppercase text-blue-200 mb-1"
            >
              Now Consulting
            </p>
            <p class="text-xl font-bold">
              #{{ store.currentPatient.queue_number }} —
              {{ patientName(store.currentPatient) }}
            </p>
            <p class="text-xs text-blue-200 mt-0.5">
              Started: {{ formatTime(store.currentPatient.started_at) }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="handleSkip(store.currentPatient)"
              :disabled="store.actionLoading"
              class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white border border-white/30 font-semibold text-xs px-4 py-2 rounded-lg transition"
            >
              <SkipForward class="w-3.5 h-3.5" /> Skip
            </button>
            <button
              @click="handleComplete(store.currentPatient)"
              :disabled="store.actionLoading"
              class="flex items-center gap-1.5 bg-white text-blue-700 font-bold text-xs px-5 py-2 rounded-lg hover:bg-blue-50 transition shadow-sm"
            >
              <CheckCircle class="w-3.5 h-3.5" />
              <span v-if="store.actionLoading"
                ><Loader2 class="w-3.5 h-3.5 animate-spin"
              /></span>
              <span v-else>Complete</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Call Next button (shown when no one is in consultation) -->
      <div v-else-if="store.totalWaiting > 0" class="mb-5">
        <button
          @click="handleCallNext"
          :disabled="store.actionLoading"
          class="w-full bg-[#004795] hover:bg-[#003670] text-white font-bold text-sm py-3.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2"
        >
          <Loader2 v-if="store.actionLoading" class="w-4 h-4 animate-spin" />
          <BellRing v-else class="w-4 h-4" />
          Call Next Patient
        </button>
      </div>

      <!-- Queue list -->
      <div v-if="store.loading && !store.entries.length" class="space-y-3">
        <div
          v-for="n in 5"
          :key="n"
          class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse"
        />
      </div>

      <div
        v-else-if="!store.entries.length && !store.loading"
        class="bg-white rounded-xl border border-gray-100 py-16 flex flex-col items-center text-gray-400"
      >
        <Users class="w-10 h-10 mb-3 text-gray-200" />
        <p class="text-sm font-medium text-gray-500">
          No queue entries for this date
        </p>
        <p class="text-xs mt-1">Add a walk-in patient to get started.</p>
      </div>

      <div
        v-else
        class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
      >
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50/80">
              <th
                class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-3 w-12"
              >
                #
              </th>
              <th
                class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-3"
              >
                Patient
              </th>
              <th
                class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-3 hidden sm:table-cell"
              >
                Type
              </th>
              <th
                class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-3"
              >
                Status
              </th>
              <th
                class="text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider px-4 py-3"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="entry in store.entries"
              :key="entry.id"
              class="hover:bg-gray-50/50 transition-colors"
              :class="{
                'bg-blue-50/50': entry.status === 'in_consultation',
                'bg-red-50/40':  entry.status === 'waiting' && entry.priority >= 100,
              }"
            >
              <td class="px-4 py-3">
                <span
                  class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-xs font-bold text-gray-600"
                >
                  {{ entry.queue_number }}
                </span>
              </td>
              <td class="px-4 py-3">
                <p class="font-semibold text-gray-800 text-sm">
                  {{ patientName(entry) }}
                </p>
                <p
                  v-if="entry.appointment?.patient?.user"
                  class="text-[11px] text-gray-400 mt-0.5"
                >
                  {{ entry.appointment.patient.user.phone ?? "" }}
                </p>
                <p
                  v-else-if="entry.walk_in_phone"
                  class="text-[11px] text-gray-400 mt-0.5"
                >
                  {{ entry.walk_in_phone }}
                </p>
              </td>
              <td class="px-4 py-3 hidden sm:table-cell">
                <div class="flex flex-col gap-1">
                  <span
                    class="text-[11px] font-semibold px-2 py-0.5 rounded w-fit"
                    :class="
                      entry.appointment_id
                        ? 'bg-blue-50 text-blue-700'
                        : 'bg-orange-50 text-orange-700'
                    "
                  >
                    {{ entry.appointment_id ? "Appointment" : "Walk-in" }}
                  </span>
                  <!-- Priority badge for urgent / walk-in priority -->
                  <span
                    v-if="entry.priority >= 100"
                    class="text-[10px] font-bold px-2 py-0.5 rounded w-fit bg-red-100 text-red-700 flex items-center gap-1"
                  >
                    ⚡ {{ entry.appointment_id ? "Urgent" : "Priority" }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-3">
                <StatusBadge :status="entry.status" />
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Recall skipped -->
                  <button
                    v-if="entry.status === 'skipped'"
                    @click="handleRecall(entry)"
                    :disabled="store.actionLoading"
                    class="text-[11px] font-semibold text-blue-600 hover:underline px-2 py-1 rounded hover:bg-blue-50 transition"
                  >
                    Recall
                  </button>

                  <!-- Skip waiting -->
                  <button
                    v-if="entry.status === 'waiting'"
                    @click="handleSkip(entry)"
                    :disabled="store.actionLoading"
                    class="text-[11px] font-semibold text-orange-500 hover:underline px-2 py-1 rounded hover:bg-orange-50 transition"
                  >
                    Skip
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- ── Walk-in modal ──────────────────────────────────────────────── -->
  <div
    v-if="showWalkIn"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
    @click.self="showWalkIn = false"
  >
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
      <div
        class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100"
      >
        <h3 class="text-sm font-bold text-gray-800">Add Walk-in Patient</h3>
        <button
          @click="showWalkIn = false"
          class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition"
        >
          <X class="w-4 h-4" />
        </button>
      </div>
      <form @submit.prevent="handleWalkIn" class="px-6 py-4 space-y-4">
        <div
          v-if="walkInError"
          class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-3 py-2.5"
        >
          <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
          {{ walkInError }}
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Patient Name <span class="text-red-500">*</span>
          </label>
          <input
            v-model="walkInForm.name"
            type="text"
            required
            placeholder="Full name"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"
            >Phone Number</label
          >
          <input
            v-model="walkInForm.phone"
            type="tel"
            placeholder="+251 911 000 000"
            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
        </div>
        <div class="flex items-center justify-end gap-3 pt-1">
          <button
            type="button"
            @click="showWalkIn = false"
            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="store.actionLoading"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-60 flex items-center gap-2"
          >
            <Loader2
              v-if="store.actionLoading"
              class="w-3.5 h-3.5 animate-spin"
            />
            Add to Queue
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, defineComponent, h } from "vue";
import {
  Users,
  UserPlus,
  RefreshCw,
  AlertCircle,
  CheckCircle,
  BellRing,
  SkipForward,
  Loader2,
  X,
} from "lucide-vue-next";
import { useAuthStore } from "../../../stores/authStore";
import { useQueueStore } from "../../../stores/queueStore";
import { echo } from "../../../plugins/echo";

// ── Tiny inline components ────────────────────────────────────────────────
const colorMap = {
  amber: "bg-amber-50 text-amber-700 border border-amber-200",
  blue: "bg-blue-50 text-blue-700 border border-blue-100",
  emerald: "bg-emerald-50 text-emerald-700 border border-emerald-200",
  gray: "bg-gray-50 text-gray-600 border border-gray-200",
};

const KpiCard = defineComponent({
  props: { label: String, value: Number, color: String },
  setup(p) {
    return () =>
      h(
        "div",
        {
          class:
            "bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col gap-1",
        },
        [
          h(
            "p",
            {
              class:
                "text-[10px] font-bold text-gray-400 uppercase tracking-wider",
            },
            p.label,
          ),
          h(
            "p",
            { class: "text-2xl font-bold text-gray-800 leading-none" },
            p.value ?? 0,
          ),
          h(
            "div",
            {
              class: `inline-flex w-fit items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-1 ${colorMap[p.color] ?? colorMap.gray}`,
            },
            p.label,
          ),
        ],
      );
  },
});

const statusColors = {
  waiting: "bg-amber-50 text-amber-700 border border-amber-200",
  in_consultation: "bg-blue-50 text-blue-700 border border-blue-100",
  completed: "bg-emerald-50 text-emerald-700 border border-emerald-200",
  skipped: "bg-orange-50 text-orange-600 border border-orange-200",
  no_show: "bg-gray-100 text-gray-500 border border-gray-200",
};

const StatusBadge = defineComponent({
  props: { status: String },
  setup(p) {
    return () =>
      h(
        "span",
        {
          class: `text-[10px] font-bold px-2 py-0.5 rounded-full capitalize ${statusColors[p.status] ?? statusColors.no_show}`,
        },
        p.status?.replace("_", " ") ?? "—",
      );
  },
});

// ── State ─────────────────────────────────────────────────────────────────
const authStore = useAuthStore();
const store = useQueueStore();

const doctorId = computed(() => authStore.user?.id);

// hospitalId: try login-cached data first, fall back to /doctor/me fetch
const hospitalIdRef = ref(null);
const hospitalId = computed(() => {
  const u = authStore.user;
  return (
    hospitalIdRef.value ??
    u?.healthcare_provider?.hospital?.id ??
    u?.healthcareProvider?.hospital?.id ??
    u?.healthcare_provider?.hospital_id ??
    u?.healthcareProvider?.hospital_id ??
    null
  );
});

const today = new Date().toISOString().split("T")[0];
const selectedDate = ref(today);
let pollInterval = null;

const dateLabel = computed(() =>
  new Date(selectedDate.value + "T00:00:00").toLocaleDateString("en-ET", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  }),
);

// Walk-in form
const showWalkIn = ref(false);
const walkInError = ref(null);
const walkInForm = ref({ name: "", phone: "" });

// ── Lifecycle ─────────────────────────────────────────────────────────────
onMounted(async () => {
  // Ensure hospital_id is available before any queue or walk-in operations
  if (!hospitalId.value && doctorId.value) {
    try {
      const { default: doctorApi } = await import("../../../api/doctorApi");
      const res = await doctorApi.getMe();
      const data = res.data?.data ?? res.data;
      // data.hospital.id comes from HealthcareProviderResource
      hospitalIdRef.value = data?.hospital?.id ?? null;
    } catch {
      /* silent — walk-in will still show a clear error if hospital_id is missing */
    }
  }

  await reload();
  // Poll every 30 seconds as a fallback when WebSocket is not configured
  pollInterval = setInterval(reload, 30_000);

  // ── WebSocket: real-time queue updates ────────────────────────────────
  // Listen on the private channel for this doctor so the queue refreshes
  // immediately when call-next / complete / skip is fired from any client.
  if (doctorId.value) {
    try {
      echo.private(`queue.${doctorId.value}`)
        .listen(".queue.updated", (e) => {
          if (e.queue) store.applyBroadcast(e.queue);
        });
    } catch {
      /* WebSocket unavailable — polling fallback is active */
    }
  }
});

onUnmounted(() => {
  clearInterval(pollInterval);
  // Leave the WebSocket channel to avoid memory leaks
  if (doctorId.value) {
    try { echo.leave(`queue.${doctorId.value}`); } catch { /* noop */ }
  }
});

async function reload() {
  if (!doctorId.value) return;

  // Auto-initialize queue from today's appointments (idempotent — safe to call every time).
  // This turns confirmed/pending appointments into waiting queue entries if not done yet.
  try {
    const { default: queueApi } = await import("../../../api/queueApi");
    await queueApi.init(doctorId.value, selectedDate.value);
  } catch {
    /* silent — init may fail if already done or no appointments; we still load below */
  }

  await store.fetchQueue(doctorId.value, selectedDate.value);
}

// ── Actions ───────────────────────────────────────────────────────────────
async function handleCallNext() {
  if (!doctorId.value) return;
  try {
    const result = await store.callNext(doctorId.value, selectedDate.value);
    if (result?.message && !result.current_patient) {
      store.error = result.message;
    }
  } catch {
    /* store.error set by store */
  }
}

async function handleComplete(entry) {
  await store.complete(entry.id, doctorId.value, selectedDate.value);
}

async function handleSkip(entry) {
  await store.skip(entry.id, doctorId.value, selectedDate.value);
}

async function handleRecall(entry) {
  await store.recall(entry.id, doctorId.value, selectedDate.value);
}

async function handleWalkIn() {
  walkInError.value = null;
  if (!doctorId.value || !hospitalId.value) {
    walkInError.value =
      "Doctor or hospital information is not available. Please refresh.";
    return;
  }
  try {
    await store.addWalkIn({
      doctor_id: doctorId.value,
      hospital_id: hospitalId.value,
      queue_date: selectedDate.value,
      walk_in_patient_name: walkInForm.value.name,
      walk_in_phone: walkInForm.value.phone || null,
    });
    walkInForm.value = { name: "", phone: "" };
    showWalkIn.value = false;
  } catch (err) {
    walkInError.value =
      err.response?.data?.message || "Failed to add walk-in patient.";
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────
function patientName(entry) {
  if (entry.walk_in_patient_name) return entry.walk_in_patient_name;
  const u = entry.appointment?.patient?.user;
  if (u) return `${u.first_name ?? ""} ${u.last_name ?? ""}`.trim();
  return `Patient #${entry.queue_number}`;
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-ET", {
    hour: "2-digit",
    minute: "2-digit",
  });
}
</script>
