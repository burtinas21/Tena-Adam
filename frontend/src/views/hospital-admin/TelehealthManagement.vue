<template>
  <div class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-3 sm:p-5 font-sans antialiased text-slate-600 dark:text-slate-300">
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Telehealth Management</h1>
          <p class="text-xs text-slate-400 font-medium mt-0.5">
            Monitor all virtual consultations in your hospital.
          </p>
        </div>
      </div>

      <!-- Error -->
      <div v-if="store.error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between">
        <span>{{ store.error }}</span>
        <button @click="store.clearError()" class="ml-3 text-red-400 hover:text-red-600">✕</button>
      </div>

      <!-- Stats row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
            <CalendarClock class="w-4 h-4 text-blue-500" />
          </div>
          <div>
            <div class="text-xl font-bold text-slate-900">{{ store.sessions.length }}</div>
            <div class="text-[10px] text-slate-400 font-medium">Total</div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
            <Clock class="w-4 h-4 text-sky-500" />
          </div>
          <div>
            <div class="text-xl font-bold text-slate-900">{{ store.scheduledSessions.length }}</div>
            <div class="text-[10px] text-slate-400 font-medium">Scheduled</div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
            <Radio class="w-4 h-4 text-emerald-500" />
          </div>
          <div>
            <div class="text-xl font-bold text-slate-900">{{ store.activeSessions.length }}</div>
            <div class="text-[10px] text-slate-400 font-medium">Active</div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center">
            <CheckCircle class="w-4 h-4 text-slate-400" />
          </div>
          <div>
            <div class="text-xl font-bold text-slate-900">{{ store.completedSessions.length }}</div>
            <div class="text-[10px] text-slate-400 font-medium">Completed</div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex items-center space-x-3">
        <select v-model="statusFilter" class="text-xs border border-slate-200 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Status</option>
          <option value="scheduled">Scheduled</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input
          v-model="search"
          type="text"
          placeholder="Search by patient or doctor…"
          class="text-xs border border-slate-200 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
        />
      </div>

      <!-- Table -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center space-x-2">
          <Video class="w-4 h-4 text-slate-400" />
          <h3 class="text-sm font-bold text-slate-900">All Telehealth Sessions</h3>
        </div>

        <!-- Loading -->
        <div v-if="store.loading" class="flex items-center justify-center py-16">
          <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
          <span class="ml-3 text-sm text-slate-400">Loading…</span>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredSessions.length === 0" class="flex flex-col items-center justify-center py-16 text-slate-400">
          <Video class="w-10 h-10 mb-3 opacity-30" />
          <p class="text-sm font-medium">No sessions found</p>
        </div>

        <!-- Data -->
        <div v-else>
          <table class="w-full text-left table-fixed">
            <colgroup>
              <col style="width:20%" />
              <col style="width:18%" class="hidden sm:table-column" />
              <col style="width:16%" />
              <col style="width:12%" class="hidden md:table-column" />
              <col style="width:12%" class="hidden md:table-column" />
              <col style="width:11%" class="hidden lg:table-column" />
              <col style="width:11%" />
            </colgroup>
            <thead>
              <tr class="bg-slate-50/60 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="py-2.5 px-2 sm:px-3">Patient</th>
                <th class="py-2.5 px-2 sm:px-3 hidden sm:table-cell">Doctor</th>
                <th class="py-2.5 px-2 sm:px-3">Scheduled</th>
                <th class="py-2.5 px-2 sm:px-3 hidden md:table-cell">Start</th>
                <th class="py-2.5 px-2 sm:px-3 hidden md:table-cell">End</th>
                <th class="py-2.5 px-2 sm:px-3 hidden lg:table-cell">Platform</th>
                <th class="py-2.5 px-2 sm:px-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
              <tr v-for="session in paginatedSessions" :key="session.id" class="hover:bg-slate-50/40 transition">
                <td class="py-2.5 px-2 sm:px-3">
                  <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-400 text-[9px] flex-shrink-0">
                      {{ getInitials(session.patient?.name) }}
                    </div>
                    <span class="font-medium text-slate-800 truncate">{{ session.patient?.name || '—' }}</span>
                  </div>
                </td>
                <td class="py-2.5 px-2 sm:px-3 text-slate-600 truncate hidden sm:table-cell">{{ session.doctor?.name || '—' }}</td>
                <td class="py-2.5 px-2 sm:px-3">
                  <div class="font-medium text-slate-800 whitespace-nowrap">{{ formatTime(session.appointment?.scheduled_time) }}</div>
                  <div class="text-[10px] text-slate-400 whitespace-nowrap">{{ formatDate(session.appointment?.scheduled_time) }}</div>
                </td>
                <td class="py-2.5 px-2 sm:px-3 hidden md:table-cell">
                  <template v-if="session.started_at">
                    <div class="font-medium text-slate-800 whitespace-nowrap">{{ formatTime(session.started_at) }}</div>
                    <div class="text-[10px] text-slate-400">{{ formatDate(session.started_at) }}</div>
                  </template>
                  <span v-else class="text-slate-400">—</span>
                </td>
                <td class="py-2.5 px-2 sm:px-3 hidden md:table-cell">
                  <template v-if="session.ended_at">
                    <div class="font-medium text-slate-800 whitespace-nowrap">{{ formatTime(session.ended_at) }}</div>
                    <div class="text-[10px] text-slate-400">{{ formatDate(session.ended_at) }}</div>
                  </template>
                  <span v-else class="text-slate-400">—</span>
                </td>
                <td class="py-2.5 px-2 sm:px-3 text-slate-500 truncate hidden lg:table-cell">{{ formatPlatform(session.platform) }}</td>
                <td class="py-2.5 px-2 sm:px-3">
                  <StatusBadge :status="session.status" />
                </td>
              </tr>
            </tbody>
          </table>
          <TablePagination
            :page="tlPage" :total-pages="tlTotalPages" :total="tlTotal" :per-page="tlPerPage"
            @prev="tlPrev" @next="tlNext" @go-to="tlGoTo"
          />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { CalendarClock, Clock, Radio, CheckCircle, Video } from 'lucide-vue-next';
import { useTelehealthStore } from '../../stores/telehealthStore';
import StatusBadge from '../../components/telehealth/StatusBadge.vue';
import { usePagination } from '../../composables/usePagination';
import TablePagination from '../../components/common/TablePagination.vue';

const store = useTelehealthStore();
const statusFilter = ref('');
const search = ref('');

const filteredSessions = computed(() => {
  let list = store.sessions;
  if (statusFilter.value) list = list.filter((s) => s.status === statusFilter.value);
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(
      (s) =>
        s.patient?.name?.toLowerCase().includes(q) ||
        s.doctor?.name?.toLowerCase().includes(q)
    );
  }
  return list;
});

const {
  page: tlPage, perPage: tlPerPage, total: tlTotal, totalPages: tlTotalPages,
  paginated: paginatedSessions, reset: tlReset, prev: tlPrev, next: tlNext, goTo: tlGoTo,
} = usePagination(filteredSessions, 10);
watch(filteredSessions, tlReset);

onMounted(async () => {
  await store.fetchMySessions();
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
  return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatPlatform(p) {
  const map = { google_meet: 'Google Meet', zoom: 'Zoom', microsoft_teams: 'MS Teams', custom: 'Custom' };
  return map[p] || p;
}
</script>
