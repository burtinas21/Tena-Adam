<template>
  <div
    class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 dark:text-slate-300"
  >
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
            Telemedicine Management
          </h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">
            Sessions are created automatically when you confirm a telehealth appointment.
          </p>
        </div>
      </div>

      <!-- Error banner -->
      <div
        v-if="store.error"
        class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between"
      >
        <span>{{ store.error }}</span>
        <button @click="store.clearError()" class="ml-3 text-red-400 hover:text-red-600">✕</button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
            <CalendarClock class="w-5 h-5 text-blue-500" />
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ store.upcomingSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Upcoming Sessions</div>
          </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
            <div class="relative">
              <Radio class="w-5 h-5 text-emerald-500" />
              <span v-if="store.activeSessions.length" class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            </div>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ store.activeSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Active Now</div>
          </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center">
            <CheckCircle class="w-5 h-5 text-slate-400" />
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ store.completedSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Completed</div>
          </div>
        </div>
      </div>

      <!-- Sessions Table -->
      <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Telemedicine Schedule</h3>
          <select
            v-model="statusFilter"
            class="text-xs border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1.5 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Status</option>
            <option value="scheduled">Scheduled</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Loading -->
        <div v-if="store.loading" class="flex items-center justify-center py-16">
          <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
          <span class="ml-3 text-sm text-slate-400">Loading sessions...</span>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredSessions.length === 0" class="flex flex-col items-center justify-center py-16 text-slate-400">
          <Video class="w-10 h-10 mb-3 opacity-30" />
          <p class="text-sm font-medium">No telehealth sessions found</p>
          <p class="text-xs mt-1">Sessions are created automatically when you confirm a telehealth appointment.</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/60 dark:bg-slate-700/40 border-b border-slate-100 dark:border-slate-700 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-3 px-6">Patient</th>
                <th class="py-3 px-6">Scheduled Time</th>
                <th class="py-3 px-6">Meeting Link</th>
                <th class="py-3 px-6">Platform</th>
                <th class="py-3 px-6">Status</th>
                <th class="py-3 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300">
              <tr v-for="session in filteredSessions" :key="session.id" class="hover:bg-slate-50/40 dark:hover:bg-slate-700/30 transition">
                <!-- Patient -->
                <td class="py-4 px-6">
                  <div class="flex items-center space-x-3">
                    <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-500 text-[10px]">
                      {{ getInitials(session.patient?.name) }}
                    </div>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ session.patient?.name || 'Unknown' }}</span>
                  </div>
                </td>

                <!-- Scheduled Time -->
                <td class="py-4 px-6">
                  <div class="font-semibold text-slate-800 dark:text-slate-100">{{ formatTime(session.appointment?.scheduled_time) }}</div>
                  <div class="text-[10px] text-slate-400">{{ formatDate(session.appointment?.scheduled_time) }}</div>
                </td>

                <!-- Meeting Link -->
                <td class="py-4 px-6">
                  <a
                    v-if="session.session_url"
                    :href="session.session_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center space-x-1 text-[#0252D7] hover:underline font-semibold text-xs"
                  >
                    <ExternalLink class="w-3 h-3 flex-shrink-0" />
                    <span class="max-w-[160px] truncate">{{ session.session_url }}</span>
                  </a>
                  <span v-else class="text-slate-400">—</span>
                </td>

                <!-- Platform -->
                <td class="py-4 px-6">
                  <div class="flex items-center space-x-1.5 text-slate-500">
                    <Video class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ formatPlatform(session.platform) }}</span>
                  </div>
                </td>

                <!-- Status -->
                <td class="py-4 px-6">
                  <StatusBadge :status="session.status" />
                </td>

                <!-- Actions — three-dot dropdown -->
                <td class="py-4 px-6 text-right">
                  <div class="relative" :ref="el => dropdownRefs[session.id] = el">
                    <button
                      @click.stop="toggleDropdown(session.id)"
                      class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                      title="Actions"
                    >
                      <!-- Three vertical dots -->
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="5" r="1.5"/>
                        <circle cx="12" cy="12" r="1.5"/>
                        <circle cx="12" cy="19" r="1.5"/>
                      </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <Transition name="dropdown">
                      <div
                        v-if="openDropdown === session.id"
                        class="absolute right-0 top-full mt-1 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 z-50 py-1 overflow-hidden"
                      >
                        <!-- Start -->
                        <button
                          v-if="session.status === 'scheduled'"
                          @click.stop="handleStart(session); openDropdown = null"
                          :disabled="store.actionLoading"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition disabled:opacity-50"
                        >
                          <Play class="w-3.5 h-3.5 flex-shrink-0" />
                          Start Session
                        </button>

                        <!-- Join -->
                        <a
                          v-if="session.status === 'active' && session.session_url"
                          :href="session.session_url"
                          target="_blank"
                          rel="noopener noreferrer"
                          @click="openDropdown = null"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-[#0252D7] hover:bg-blue-50 dark:hover:bg-blue-900/20 transition"
                        >
                          <Video class="w-3.5 h-3.5 flex-shrink-0" />
                          Join Meeting
                        </a>

                        <!-- Complete -->
                        <button
                          v-if="session.status === 'active'"
                          @click.stop="handleComplete(session); openDropdown = null"
                          :disabled="store.actionLoading"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition disabled:opacity-50"
                        >
                          <CheckCircle class="w-3.5 h-3.5 flex-shrink-0" />
                          Complete
                        </button>

                        <!-- View Link (scheduled with a URL) -->
                        <a
                          v-if="session.status === 'scheduled' && session.session_url"
                          :href="session.session_url"
                          target="_blank"
                          rel="noopener noreferrer"
                          @click="openDropdown = null"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition"
                        >
                          <ExternalLink class="w-3.5 h-3.5 flex-shrink-0" />
                          View Link
                        </a>

                        <!-- Reschedule -->
                        <button
                          v-if="session.status === 'scheduled' || session.status === 'active'"
                          @click.stop="openRescheduleModal(session); openDropdown = null"
                          :disabled="store.actionLoading"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition disabled:opacity-50"
                        >
                          <Clock class="w-3.5 h-3.5 flex-shrink-0" />
                          Reschedule
                        </button>

                        <div
                          v-if="session.status === 'scheduled' || session.status === 'active'"
                          class="border-t border-slate-100 dark:border-slate-700 my-1"
                        />

                        <!-- Cancel -->
                        <button
                          v-if="session.status === 'scheduled' || session.status === 'active'"
                          @click.stop="handleCancel(session); openDropdown = null"
                          :disabled="store.actionLoading"
                          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition disabled:opacity-50"
                        >
                          <XCircle class="w-3.5 h-3.5 flex-shrink-0" />
                          Cancel Session
                        </button>

                        <!-- Nothing for completed/cancelled -->
                        <div
                          v-if="session.status === 'completed' || session.status === 'cancelled'"
                          class="px-4 py-2.5 text-xs text-slate-400 italic"
                        >
                          No actions available
                        </div>
                      </div>
                    </Transition>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ── Reschedule Modal ─────────────────────────────────────────────── -->
    <div
      v-if="showRescheduleModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
      @click.self="closeRescheduleModal"
    >
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-5">
        <div class="flex items-center justify-between">
          <h3 class="text-base font-bold text-slate-900 dark:text-white">Reschedule Session</h3>
          <button @click="closeRescheduleModal" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div v-if="rescheduleTarget" class="bg-slate-50 dark:bg-slate-700 rounded-xl px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
          <p class="font-semibold">{{ rescheduleTarget.patient?.name }}</p>
          <p class="text-slate-400 mt-0.5">Currently: {{ formatTime(rescheduleTarget.appointment?.scheduled_time) }}, {{ formatDate(rescheduleTarget.appointment?.scheduled_time) }}</p>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">
            Push forward by (minutes)
          </label>
          <div class="flex items-center gap-2">
            <!-- Quick picks -->
            <button
              v-for="m in [5, 10, 15, 30]" :key="m"
              @click="rescheduleMinutes = m"
              :class="rescheduleMinutes === m ? 'bg-[#004795] text-white border-[#004795]' : 'bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600'"
              class="px-3 py-1.5 text-xs font-bold rounded-lg border transition"
            >
              +{{ m }}
            </button>
          </div>
          <input
            v-model.number="rescheduleMinutes"
            type="number"
            min="1"
            max="120"
            placeholder="Custom minutes"
            class="mt-2 w-full border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition"
          />
          <p class="text-[10px] text-slate-400 mt-1">
            New time will be {{ previewNewTime }}
          </p>
        </div>

        <div v-if="rescheduleError" class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ rescheduleError }}</div>

        <div class="flex items-center justify-end gap-3 pt-1">
          <button @click="closeRescheduleModal" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            Cancel
          </button>
          <button
            @click="handleReschedule"
            :disabled="store.actionLoading || !rescheduleMinutes || rescheduleMinutes < 1"
            class="px-5 py-2 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition disabled:opacity-50 flex items-center gap-2"
          >
            <span v-if="store.actionLoading" class="animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></span>
            Confirm Reschedule
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { CalendarClock, Radio, CheckCircle, Video, Play, Clock, ExternalLink, XCircle } from "lucide-vue-next";
import { useTelehealthStore } from "../../stores/telehealthStore";
import StatusBadge from "../../components/telehealth/StatusBadge.vue";

const store = useTelehealthStore();

const statusFilter = ref("");

// ── Dropdown state ────────────────────────────────────────────────────────
const openDropdown = ref(null);
const dropdownRefs = ref({});

function toggleDropdown(sessionId) {
  openDropdown.value = openDropdown.value === sessionId ? null : sessionId;
}

function handleClickOutside(e) {
  if (openDropdown.value === null) return;
  const ref = dropdownRefs.value[openDropdown.value];
  if (ref && !ref.contains(e.target)) {
    openDropdown.value = null;
  }
}

// ── Reschedule modal state ────────────────────────────────────────────────
const showRescheduleModal = ref(false);
const rescheduleTarget = ref(null);
const rescheduleMinutes = ref(10);
const rescheduleError = ref("");

const filteredSessions = computed(() => {
  if (!statusFilter.value) return store.sessions;
  return store.sessions.filter((s) => s.status === statusFilter.value);
});

const previewNewTime = computed(() => {
  if (!rescheduleTarget.value?.appointment?.scheduled_time || !rescheduleMinutes.value) return "—";
  const base = new Date(rescheduleTarget.value.appointment.scheduled_time);
  base.setMinutes(base.getMinutes() + Number(rescheduleMinutes.value));
  return base.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" }) +
    ", " + base.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
});

onMounted(async () => {
  await store.fetchMySessions();
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

function getInitials(name) {
  if (!name) return "?";
  return name.split(" ").map((n) => n[0]).join("").toUpperCase().slice(0, 2);
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
}

function formatDate(dt) {
  if (!dt) return "";
  return new Date(dt).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
}

function formatPlatform(p) {
  return { google_meet: "Google Meet", zoom: "Zoom", microsoft_teams: "MS Teams", custom: "Jitsi / Custom" }[p] || p;
}

// ── Actions ───────────────────────────────────────────────────────────────

async function handleStart(session) {
  if (!confirm(`Start session for ${session.patient?.name}?`)) return;
  try { await store.startSession(session.id); } catch { /* shown in banner */ }
}

async function handleComplete(session) {
  if (!confirm("Mark this session as completed?")) return;
  try { await store.completeSession(session.id); } catch { /* shown in banner */ }
}

async function handleCancel(session) {
  if (!confirm("Cancel this telehealth session?")) return;
  try { await store.cancelSession(session.id); } catch { /* shown in banner */ }
}

function openRescheduleModal(session) {
  rescheduleTarget.value = session;
  rescheduleMinutes.value = 10;
  rescheduleError.value = "";
  showRescheduleModal.value = true;
}

function closeRescheduleModal() {
  showRescheduleModal.value = false;
  rescheduleTarget.value = null;
  rescheduleError.value = "";
}

async function handleReschedule() {
  rescheduleError.value = "";
  if (!rescheduleMinutes.value || rescheduleMinutes.value < 1) {
    rescheduleError.value = "Please enter a valid number of minutes.";
    return;
  }
  try {
    await store.rescheduleSession(rescheduleTarget.value.id, rescheduleMinutes.value);
    closeRescheduleModal();
  } catch (err) {
    const errs = err.response?.data?.errors;
    rescheduleError.value = errs
      ? Object.values(errs).flat().join(" ")
      : err.response?.data?.message || "Failed to reschedule.";
  }
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.12s ease, transform 0.12s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.97);
}
</style>
