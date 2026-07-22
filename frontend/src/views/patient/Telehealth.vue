<template>
  <div class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 dark:text-slate-300 selection:bg-blue-600/10">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-8 border-b border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Telemedicine Overview</h1>
        <p class="text-sm text-gray-500 mt-1">Your virtual consultations with doctors.</p>
      </div>
    </div>

    <!-- Error -->
    <div v-if="store.error" class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between">
      <span>{{ store.error }}</span>
      <button @click="store.clearError()" class="ml-3 text-red-400 hover:text-red-600">✕</button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8">
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center space-x-4">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
          <Video class="w-5 h-5 text-[#0252D7]" />
        </div>
        <div>
          <div class="text-2xl font-bold text-gray-900">{{ store.upcomingSessions.length }}</div>
          <div class="text-xs text-gray-400 font-medium">Active & Upcoming</div>
          <div class="text-[10px] text-gray-400">Today</div>
        </div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center space-x-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
          <CheckCircle class="w-5 h-5 text-emerald-500" />
        </div>
        <div>
          <div class="text-2xl font-bold text-gray-900">{{ store.completedSessions.length }}</div>
          <div class="text-xs text-gray-400 font-medium">Completed Consultations</div>
          <div class="text-[10px] text-gray-400">All time</div>
        </div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center space-x-4">
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
          <XCircle class="w-5 h-5 text-red-400" />
        </div>
        <div>
          <div class="text-2xl font-bold text-gray-900">{{ store.cancelledSessions.length }}</div>
          <div class="text-xs text-gray-400 font-medium">Cancelled/Rescheduled</div>
          <div class="text-[10px] text-gray-400">All time</div>
        </div>
      </div>
    </div>

    <!-- Sessions List -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
      <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div class="flex items-center space-x-2">
          <Video class="w-4 h-4 text-gray-400" />
          <h2 class="text-sm font-bold text-gray-900">Your Telemedicine Sessions</h2>
        </div>
        <select v-model="statusFilter" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All</option>
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

      <!-- Empty state -->
      <div v-else-if="filteredSessions.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
        <Video class="w-10 h-10 mb-3 opacity-30" />
        <p class="text-sm font-medium">No sessions found</p>
        <p class="text-xs mt-1">Book a telehealth appointment to get started.</p>
      </div>

      <!-- Session rows -->
      <div v-else class="p-6 space-y-4">
        <div
          v-for="session in filteredSessions"
          :key="session.id"
          class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition"
        >
          <div class="flex items-center space-x-4">
            <!-- Doctor avatar -->
            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-500 text-xs flex-shrink-0">
              {{ getInitials(session.doctor?.name) }}
            </div>
            <div>
              <div class="font-semibold text-gray-900 text-sm">{{ session.doctor?.name || 'Doctor' }}</div>
              <div class="text-xs text-gray-400 mt-0.5">
                {{ formatPlatform(session.platform) }} •
                {{ formatDateTime(session.appointment?.scheduled_time) }}
              </div>
              <div class="mt-1">
                <StatusBadge :status="session.status" />
              </div>
            </div>
          </div>

          <div class="mt-3 sm:mt-0 flex items-center space-x-2 self-start sm:self-center">
            <!-- Active — join button -->
            <a
              v-if="session.status === 'active' && session.session_url"
              :href="session.session_url"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center space-x-1.5 px-4 py-2 bg-[#0252D7] text-white text-xs font-semibold rounded-lg hover:bg-blue-700 shadow-sm transition"
            >
              <Video class="w-3.5 h-3.5" />
              <span>Join Now</span>
            </a>

            <!-- Scheduled — meeting link preview -->
            <a
              v-else-if="session.status === 'scheduled' && session.session_url"
              :href="session.session_url"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center space-x-1.5 px-4 py-2 bg-white border border-gray-200 text-xs font-semibold text-gray-600 rounded-lg hover:bg-gray-50 shadow-sm transition"
            >
              <ExternalLink class="w-3.5 h-3.5" />
              <span>View Link</span>
            </a>

            <!-- Completed — duration chip -->
            <span
              v-if="session.status === 'completed' && session.duration_min"
              class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-xs font-semibold rounded-lg border border-emerald-100"
            >
              {{ session.duration_min }} min
            </span>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Video, CheckCircle, XCircle, ExternalLink } from 'lucide-vue-next';
import { useTelehealthStore } from '../../stores/telehealthStore';
import StatusBadge from '../../components/telehealth/StatusBadge.vue';

const store = useTelehealthStore();
const statusFilter = ref('');

const filteredSessions = computed(() => {
  if (!statusFilter.value) return store.sessions;
  return store.sessions.filter((s) => s.status === statusFilter.value);
});

onMounted(async () => {
  await store.fetchMySessions();
});

function getInitials(name) {
  if (!name) return '?';
  return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
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

function formatDateTime(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleString('en-US', {
    month: 'short', day: 'numeric', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}
</script>
