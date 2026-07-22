<template>
  <div class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 dark:text-slate-300 selection:bg-blue-500/10">
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Telemedicine Management</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">
            Manage your upcoming and active virtual consultations.
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="flex items-center space-x-1.5 px-4 py-2 bg-[#0252D7] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/10 transition self-start sm:self-center"
        >
          <span class="text-sm font-light mt-[-2px]">+</span>
          <span>New Session</span>
        </button>
      </div>

      <!-- Error banner -->
      <div v-if="store.error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between">
        <span>{{ store.error }}</span>
        <button @click="store.clearError()" class="ml-3 text-red-400 hover:text-red-600">✕</button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
            <CalendarClock class="w-5 h-5 text-blue-500" />
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900">{{ store.upcomingSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Upcoming Sessions</div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
            <div class="relative">
              <Radio class="w-5 h-5 text-emerald-500" />
              <span v-if="store.activeSessions.length" class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            </div>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900">{{ store.activeSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Active Now</div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center space-x-4">
          <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center">
            <CheckCircle class="w-5 h-5 text-slate-400" />
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900">{{ store.completedSessions.length }}</div>
            <div class="text-xs text-slate-400 font-medium">Completed</div>
          </div>
        </div>
      </div>

      <!-- Sessions Table -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <h3 class="text-sm font-bold text-slate-900 tracking-tight">Telemedicine Schedule</h3>
          <div class="flex items-center space-x-2">
            <select v-model="statusFilter" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">All Status</option>
              <option value="scheduled">Scheduled</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="store.loading" class="flex items-center justify-center py-16">
          <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
          <span class="ml-3 text-sm text-slate-400">Loading sessions...</span>
        </div>

        <!-- Empty state -->
        <div v-else-if="filteredSessions.length === 0" class="flex flex-col items-center justify-center py-16 text-slate-400">
          <Video class="w-10 h-10 mb-3 opacity-30" />
          <p class="text-sm font-medium">No telehealth sessions found</p>
          <p class="text-xs mt-1">Sessions linked to telehealth appointments will appear here.</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/60 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-3 px-6">Patient</th>
                <th class="py-3 px-6">Scheduled Time</th>
                <th class="py-3 px-6">Platform</th>
                <th class="py-3 px-6">Status</th>
                <th class="py-3 px-6 text-right pr-6">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <tr v-for="session in filteredSessions" :key="session.id" class="hover:bg-slate-50/40 transition">
                <td class="py-4 px-6">
                  <div class="flex items-center space-x-3">
                    <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-500 text-[10px]">
                      {{ getInitials(session.patient?.name) }}
                    </div>
                    <span class="font-semibold text-slate-800">{{ session.patient?.name || 'Unknown' }}</span>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <div class="font-semibold text-slate-800">{{ formatTime(session.appointment?.scheduled_time) }}</div>
                  <div class="text-[10px] text-slate-400">{{ formatDate(session.appointment?.scheduled_time) }}</div>
                </td>
                <td class="py-4 px-6">
                  <div class="flex items-center space-x-1.5 text-slate-500">
                    <Video class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ formatPlatform(session.platform) }}</span>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <StatusBadge :status="session.status" />
                </td>
                <td class="py-4 px-6 text-right pr-6">
                  <div class="inline-flex items-center space-x-2">
                    <!-- Start button for scheduled sessions -->
                    <button
                      v-if="session.status === 'scheduled'"
                      @click="handleStart(session)"
                      :disabled="store.actionLoading"
                      class="flex items-center space-x-1.5 px-3.5 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 shadow-sm transition disabled:opacity-50"
                    >
                      <Play class="w-3.5 h-3.5" />
                      <span>Start</span>
                    </button>

                    <!-- Join button for active sessions -->
                    <a
                      v-if="session.status === 'active' && session.session_url"
                      :href="session.session_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="flex items-center space-x-1.5 px-4 py-1.5 bg-[#0252D7] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/10 transition"
                    >
                      <Video class="w-3.5 h-3.5" />
                      <span>Join</span>
                    </a>

                    <!-- Complete button for active sessions -->
                    <button
                      v-if="session.status === 'active'"
                      @click="handleComplete(session)"
                      :disabled="store.actionLoading"
                      class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm disabled:opacity-50"
                    >
                      Complete
                    </button>

                    <!-- Cancel button -->
                    <button
                      v-if="session.status === 'scheduled' || session.status === 'active'"
                      @click="handleCancel(session)"
                      :disabled="store.actionLoading"
                      class="px-3.5 py-1.5 bg-white border border-red-200 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 transition shadow-sm disabled:opacity-50"
                    >
                      Cancel
                    </button>

                    <!-- View link for any session -->
                    <a
                      v-if="session.session_url && (session.status === 'scheduled')"
                      :href="session.session_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm"
                    >
                      View Link
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Session Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Create Telehealth Session</h3>
            <button @click="closeModal" class="text-slate-400 hover:text-slate-600">✕</button>
          </div>

          <div v-if="createError" :class="[
            'border rounded-lg px-3 py-2 text-xs space-y-2',
            googleConnected
              ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
              : 'bg-red-50 border-red-200 text-red-700'
          ]">
            <p>{{ createError }}</p>
            <button
              v-if="needsGoogleAuth"
              @click="openGoogleAuthPopup"
              class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-red-300 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50 transition"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.545 10.239v3.821h5.445c-.712 2.315-2.647 3.972-5.445 3.972a6.033 6.033 0 110-12.064c1.498 0 2.866.549 3.921 1.453l2.814-2.814A9.969 9.969 0 0012.545 2C7.021 2 2.543 6.477 2.543 12s4.478 10 10.002 10c8.396 0 10.249-7.85 9.426-11.748l-9.426-.013z"/></svg>
              <span>Connect Google Account</span>
            </button>
          </div>

          <div class="space-y-3">
            <!-- Appointment selector -->
            <div>
              <label class="text-xs font-semibold text-slate-600 mb-1 block">Appointment</label>
              <div v-if="appointmentsLoading" class="flex items-center space-x-2 border border-slate-200 rounded-lg px-3 py-2">
                <div class="animate-spin rounded-full h-3 w-3 border-2 border-blue-500 border-t-transparent"></div>
                <span class="text-xs text-slate-400">Loading appointments…</span>
              </div>
              <select
                v-else
                v-model="form.appointment_id"
                @change="onAppointmentSelect"
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">— Select a confirmed telehealth appointment —</option>
                <option
                  v-for="apt in availableTelehealthAppointments"
                  :key="apt.id"
                  :value="apt.id"
                >
                  {{ apt.patient?.first_name }} {{ apt.patient?.last_name }} — {{ formatDateShort(apt.scheduled_time) }}
                </option>
              </select>
              <p v-if="!appointmentsLoading && availableTelehealthAppointments.length === 0" class="text-[10px] text-slate-400 mt-1">
                No confirmed telehealth appointments without an active session found.
              </p>
            </div>

            <!-- Platform -->
            <div>
              <label class="text-xs font-semibold text-slate-600 mb-1 block">Platform</label>
              <select v-model="form.platform" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="google_meet">Google Meet (auto-create)</option>
                <option value="zoom">Zoom (auto-create)</option>
                <option value="microsoft_teams">Microsoft Teams (auto-create)</option>
                <option value="custom">Custom URL</option>
              </select>
              <p v-if="form.platform !== 'custom'" class="text-[10px] text-emerald-600 mt-1 flex items-center space-x-1">
                <span>✓</span><span>Meeting link will be generated automatically.</span>
              </p>
            </div>

            <!-- Session URL — only for Custom platform -->
            <div v-if="form.platform === 'custom'">
              <label class="text-xs font-semibold text-slate-600 mb-1 block">Session URL</label>
              <input
                v-model="form.session_url"
                type="url"
                placeholder="https://meet.example.com/your-room"
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Times — auto-filled from appointment, still editable -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-semibold text-slate-600 mb-1 block">Start Time</label>
                <input
                  v-model="form.start_time"
                  type="datetime-local"
                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="text-xs font-semibold text-slate-600 mb-1 block">End Time</label>
                <input
                  v-model="form.end_time"
                  type="datetime-local"
                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <input v-model="form.recording_consent" type="checkbox" id="consent" class="rounded border-slate-300 text-blue-600" />
              <label for="consent" class="text-xs text-slate-600">Recording consent granted</label>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-2">
            <button @click="closeModal" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 rounded-lg border border-slate-200 transition">
              Cancel
            </button>
            <button
              @click="handleCreate"
              :disabled="store.actionLoading || !form.appointment_id"
              class="px-4 py-2 text-xs font-bold text-white bg-[#0252D7] rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/10 transition disabled:opacity-50 flex items-center space-x-2"
            >
              <span v-if="store.actionLoading" class="animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></span>
              <span>Create Session</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { CalendarClock, Radio, CheckCircle, Video, Play } from 'lucide-vue-next';
import { useTelehealthStore } from '../../stores/telehealthStore';
import { useAuthStore } from '../../stores/authStore';
import StatusBadge from '../../components/telehealth/StatusBadge.vue';
import appointmentApi from '../../api/appointmentApi';
import telehealthApi from '../../api/telehealthApi';

const store = useTelehealthStore();
const authStore = useAuthStore();

const statusFilter = ref('');
const showCreateModal = ref(false);
const createError = ref('');
const needsGoogleAuth = ref(false);
const googleConnected = ref(false);
const googleAuthUrl = telehealthApi.getGoogleAuthUrl();

// Appointments that are eligible for a telehealth session
const telehealthAppointments = ref([]);
const appointmentsLoading = ref(false);

const defaultForm = () => ({
  appointment_id: '',
  platform: 'google_meet',
  session_url: '',
  start_time: '',
  end_time: '',
  recording_consent: false,
});

const form = ref(defaultForm());

const filteredSessions = computed(() => {
  if (!statusFilter.value) return store.sessions;
  return store.sessions.filter((s) => s.status === statusFilter.value);
});

/**
 * Filter to only approved telehealth appointments that don't already have a session.
 * The existing sessions list tells us which appointment_ids already have sessions.
 */
const availableTelehealthAppointments = computed(() => {
  const usedIds = new Set(store.sessions.map((s) => s.appointment_id).filter(Boolean));
  return telehealthAppointments.value.filter(
    (apt) =>
      apt.is_telehealth &&
      (apt.status === 'confirmed' || apt.status === 'pending') &&
      !usedIds.has(apt.id)
  );
});

onMounted(async () => {
  await store.fetchMySessions();

  // Listen for Google OAuth popup callback
  window.addEventListener('message', (event) => {
    if (event.data?.google_auth === 'success') {
      needsGoogleAuth.value = false;
      createError.value = 'Google account connected! You can now create a Google Meet session.';
      // Change error styling to success
      googleConnected.value = true;
    } else if (event.data?.google_auth === 'error') {
      createError.value = `Google authorization failed: ${event.data.message}`;
    }
  });
});

/** Load telehealth appointments when the modal opens */
async function openCreateModal() {
  showCreateModal.value = true;
  appointmentsLoading.value = true;
  try {
    const res = await appointmentApi.getAll();
    telehealthAppointments.value = res.data?.data ?? res.data ?? [];
  } catch {
    telehealthAppointments.value = [];
  } finally {
    appointmentsLoading.value = false;
  }
}

/** Auto-fill start/end times from the chosen appointment */
function onAppointmentSelect() {
  const apt = telehealthAppointments.value.find((a) => a.id === form.value.appointment_id);
  if (!apt) return;

  // scheduled_time → start_time; end_time is start + 30 min by default
  if (apt.scheduled_time) {
    const start = new Date(apt.scheduled_time);
    const end = new Date(start.getTime() + 30 * 60 * 1000);
    form.value.start_time = toLocalDatetimeInput(start);
    form.value.end_time = toLocalDatetimeInput(end);
  }
}

/** Convert a Date to the value format expected by <input type="datetime-local"> */
function toLocalDatetimeInput(date) {
  const pad = (n) => String(n).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function getInitials(name) {
  if (!name) return '?';
  return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
}

function formatTime(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(dt) {
  if (!dt) return '';
  return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatDateShort(dt) {
  if (!dt) return '';
  return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatPlatform(p) {
  const map = {
    google_meet: 'Google Meet',
    zoom: 'Zoom',
    microsoft_teams: 'MS Teams',
    custom: 'Custom',
  };
  return map[p] || p;
}

async function handleStart(session) {
  if (!confirm(`Start telehealth session for ${session.patient?.name}?`)) return;
  try {
    await store.startSession(session.id);
  } catch (e) {
    // error shown in banner
  }
}

async function handleComplete(session) {
  if (!confirm('Mark this session as completed?')) return;
  try {
    await store.completeSession(session.id);
  } catch (e) {
    // error shown in banner
  }
}

async function handleCancel(session) {
  if (!confirm('Cancel this telehealth session?')) return;
  try {
    await store.cancelSession(session.id);
  } catch (e) {
    // error shown in banner
  }
}

function closeModal() {
  showCreateModal.value = false;
  createError.value = '';
  needsGoogleAuth.value = false;
  googleConnected.value = false;
  form.value = defaultForm();
}

function openGoogleAuthPopup() {
  const width = 500;
  const height = 640;
  const left = window.screenX + (window.outerWidth - width) / 2;
  const top = window.screenY + (window.outerHeight - height) / 2;
  window.open(
    googleAuthUrl,
    'google_oauth',
    `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no`
  );
}

async function handleCreate() {
  createError.value = '';
  needsGoogleAuth.value = false;

  if (!form.value.appointment_id) {
    createError.value = 'Please select an appointment.';
    return;
  }
  if (!form.value.start_time || !form.value.end_time) {
    createError.value = 'Start and end times are required.';
    return;
  }
  if (form.value.platform === 'custom' && !form.value.session_url) {
    createError.value = 'Session URL is required for a Custom platform.';
    return;
  }

  try {
    const payload = {
      appointment_id: form.value.appointment_id,
      platform: form.value.platform,
      start_time: form.value.start_time,
      end_time: form.value.end_time,
      recording_consent: form.value.recording_consent,
    };

    // Only include session_url for custom platform
    if (form.value.platform === 'custom') {
      payload.session_url = form.value.session_url;
    }

    await store.createSession(payload);
    closeModal();
  } catch (err) {
    const errs = err.response?.data?.errors;
    if (errs) {
      if (errs.google_oauth_required) {
        needsGoogleAuth.value = true;
        createError.value = errs.google_oauth_required[0];
      } else if (errs.zoom) {
        createError.value = errs.zoom[0];
      } else {
        createError.value = Object.values(errs).flat().join(' ');
      }
    } else {
      createError.value = err.response?.data?.message || 'Failed to create session.';
    }
  }
}
</script>
