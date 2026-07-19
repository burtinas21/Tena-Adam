<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white">
      <h3 class="text-sm font-bold text-slate-900 tracking-tight">Telemedicine Schedule</h3>
      <select v-model="statusFilter" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All</option>
        <option value="scheduled">Scheduled</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-500 border-t-transparent"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="rows.length === 0" class="flex flex-col items-center justify-center py-12 text-slate-400">
      <Video class="w-8 h-8 mb-2 opacity-30" />
      <p class="text-xs font-medium">No sessions found</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
            <th class="py-3 px-6 font-semibold">Patient Name</th>
            <th class="py-3 px-6 font-semibold">Time</th>
            <th class="py-3 px-6 font-semibold">Platform</th>
            <th class="py-3 px-6 font-semibold">Status</th>
            <th class="py-3 px-6 text-right pr-6 font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
          <tr v-for="session in rows" :key="session.id" class="hover:bg-slate-50/20 transition">
            <td class="py-4 px-6">
              <div class="flex items-center space-x-3">
                <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-400 text-[10px]">
                  {{ getInitials(session.patient?.name) }}
                </div>
                <span class="font-bold text-slate-800 tracking-tight">{{ session.patient?.name || 'Patient' }}</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <div class="space-y-0.5">
                <div class="font-bold text-slate-800">{{ formatTime(session.appointment?.scheduled_time) }}</div>
                <div v-if="session.status === 'active'" class="text-[10px] text-rose-500 font-bold tracking-tight">Live now</div>
                <div v-else class="text-[10px] text-slate-400">{{ formatDate(session.appointment?.scheduled_time) }}</div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center space-x-1.5 text-slate-500 font-normal">
                <Video class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ formatPlatform(session.platform) }}</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <StatusBadge :status="session.status" />
            </td>
            <td class="py-4 px-6 text-right pr-6">
              <div class="inline-flex items-center space-x-2">
                <!-- Start -->
                <button
                  v-if="session.status === 'scheduled'"
                  @click="$emit('start', session)"
                  class="flex items-center space-x-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 shadow-sm transition"
                >
                  <Play class="w-3.5 h-3.5" />
                  <span>Start</span>
                </button>

                <!-- Join -->
                <a
                  v-if="session.status === 'active' && session.session_url"
                  :href="session.session_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center space-x-1.5 px-4 py-1.5 bg-[#0252D7] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/10 transition"
                >
                  <Video class="w-3.5 h-3.5" />
                  <span>Join Session</span>
                </a>

                <!-- Disabled for scheduled without URL -->
                <button
                  v-if="session.status === 'scheduled' && !session.session_url"
                  disabled
                  class="flex items-center space-x-1.5 px-4 py-1.5 bg-slate-100 text-slate-300 text-xs font-bold rounded-lg border border-slate-200/60 cursor-not-allowed"
                >
                  <Video class="w-3.5 h-3.5" />
                  <span>No Link Yet</span>
                </button>

                <!-- View link -->
                <a
                  v-if="session.status === 'scheduled' && session.session_url"
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

    <!-- Footer -->
    <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-medium">
      <span>Showing {{ rows.length }} of {{ store.sessions.length }} sessions</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Video, Play, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { useTelehealthStore } from '../../../stores/telehealthStore';
import StatusBadge from '../StatusBadge.vue';

defineEmits(['start']);

const store = useTelehealthStore();
const statusFilter = ref('');

const rows = computed(() => {
  if (!statusFilter.value) return store.sessions;
  return store.sessions.filter((s) => s.status === statusFilter.value);
});

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
  return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function formatPlatform(p) {
  const map = { google_meet: 'Google Meet', zoom: 'Zoom', microsoft_teams: 'MS Teams', custom: 'Custom' };
  return map[p] || p;
}
</script>
