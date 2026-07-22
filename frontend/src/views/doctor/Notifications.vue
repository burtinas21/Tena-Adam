<!-- Doctor notifications — same rich UI as patient view -->
<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-3xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Notifications</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">Schedule, leave, encounter and queue updates</p>
        </div>
        <div class="flex items-center gap-2">
          <button v-if="store.unreadCount > 0" @click="store.markAllRead()"
            class="text-xs font-semibold text-[#004795] border border-[#004795]/30 px-3 py-2 rounded-lg hover:bg-[#004795]/5 transition flex items-center gap-1.5">
            <CheckCheck class="w-3.5 h-3.5" /> Mark all read
          </button>
          <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
            <button v-for="f in filters" :key="f.key" @click="activeFilter = f.key"
              :class="activeFilter === f.key ? 'bg-white text-[#004795] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
              class="px-3 py-1.5 text-xs font-bold rounded-md transition">
              {{ f.label }}
              <span v-if="f.key === 'unread' && store.unreadCount > 0"
                class="ml-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">
                {{ store.unreadCount }}
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Preferences -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5">
        <button @click="showPrefs = !showPrefs" class="flex items-center justify-between w-full">
          <span class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <Settings class="w-4 h-4 text-[#004795]" /> Notification Preferences
          </span>
          <ChevronDown class="w-4 h-4 text-gray-400 transition-transform" :class="showPrefs ? 'rotate-180' : ''" />
        </button>
        <div v-if="showPrefs" class="mt-4 border-t border-gray-50 pt-4 space-y-3">
          <div v-if="store.prefsLoading" class="text-xs text-gray-400">Loading...</div>
          <div v-else-if="store.preferences" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div v-for="pref in prefFields" :key="pref.key"
              class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2.5">
              <span class="text-xs font-medium text-gray-700">{{ pref.label }}</span>
              <button type="button" @click="store.preferences[pref.key] = !store.preferences[pref.key]"
                :class="store.preferences[pref.key] ? 'bg-[#004795]' : 'bg-gray-200'"
                class="relative w-9 h-5 rounded-full transition-colors duration-200">
                <span :class="store.preferences[pref.key] ? 'translate-x-4' : 'translate-x-0.5'"
                  class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" />
              </button>
            </div>
          </div>
          <div class="flex justify-end pt-1">
            <button @click="savePrefs" :disabled="prefsSaving"
              class="text-xs font-semibold text-white bg-[#004795] hover:bg-[#003670] px-4 py-2 rounded-lg transition disabled:opacity-50 flex items-center gap-2">
              <Loader2 v-if="prefsSaving" class="w-3 h-3 animate-spin" />
              Save
            </button>
          </div>
        </div>
      </div>

      <div v-if="store.error" class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ store.error }}
      </div>

      <div v-if="store.loading" class="space-y-2">
        <div v-for="n in 5" :key="n" class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse" />
      </div>

      <div v-else-if="!displayedNotifs.length"
        class="bg-white rounded-2xl border border-gray-100 py-16 flex flex-col items-center text-gray-400">
        <Bell class="w-10 h-10 mb-3 text-gray-300" />
        <p class="text-sm font-medium">{{ activeFilter === 'unread' ? 'No unread notifications' : 'No notifications yet' }}</p>
      </div>

      <div v-else class="space-y-2">
        <div v-for="n in displayedNotifs" :key="n.id"
          :class="n.status !== 'read' ? 'bg-blue-50/50 border-blue-100' : 'bg-white border-gray-100'"
          class="rounded-xl border shadow-sm p-4 flex items-start gap-3 hover:shadow-md transition-shadow">
          <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-base"
            :class="channelBg(n.channel)">
            {{ channelIcon(n.channel) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <p class="text-sm font-semibold text-gray-800">{{ n.subject || 'Notification' }}</p>
              <span v-if="n.status !== 'read'" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5" />
            </div>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ n.content }}</p>
            <div class="flex items-center gap-3 mt-2">
              <span class="text-[10px] text-gray-400">{{ formatTime(n.created_at) }}</span>
              <span :class="statusClass(n.status)" class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full capitalize">{{ n.status }}</span>
              <span class="text-[10px] text-gray-400 capitalize">{{ n.channel?.replace(/_/g, ' ') }}</span>
            </div>
          </div>
          <div class="flex items-center gap-1 flex-shrink-0">
            <button v-if="n.status !== 'read'" @click="store.markAsRead(n.id)"
              class="p-1.5 text-[#004795] hover:bg-[#004795]/10 rounded-lg transition" title="Mark read">
              <CheckCheck class="w-3.5 h-3.5" />
            </button>
            <button v-if="n.status === 'failed'" @click="store.retry(n.id)"
              class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Retry">
              <RefreshCw class="w-3.5 h-3.5" />
            </button>
            <button @click="store.destroy(n.id)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition" title="Delete">
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Bell, CheckCheck, RefreshCw, Trash2, AlertCircle, Settings, ChevronDown, Loader2 } from "lucide-vue-next";
import { useNotificationStore } from "../../stores/notificationStore";

const store = useNotificationStore();
const activeFilter = ref("all");
const showPrefs = ref(false);
const prefsSaving = ref(false);

const filters = [
  { key: "all", label: "All" },
  { key: "unread", label: "Unread" },
  { key: "doctor_schedule", label: "Schedule" },
  { key: "doctor_leave", label: "Leave" },
  { key: "medical_encounter", label: "Encounters" },
];

const displayedNotifs = computed(() => {
  if (activeFilter.value === "all") return store.notifications;
  if (activeFilter.value === "unread") return store.notifications.filter((n) => n.status !== "read");
  return store.notifications.filter((n) => n.channel === activeFilter.value);
});

const prefFields = [
  { key: "email_enabled", label: "Email" },
  { key: "sms_enabled", label: "SMS" },
  { key: "push_enabled", label: "Push" },
  { key: "appointment_reminders", label: "Reminders" },
  { key: "queue_updates", label: "Queue" },
];

async function savePrefs() {
  prefsSaving.value = true;
  try { await store.savePreferences(store.preferences); }
  finally { prefsSaving.value = false; }
}

function channelIcon(ch) {
  return { appointment: "📅", queue: "🔢", telehealth: "💻", doctor_leave: "📋", doctor_schedule: "🗓️", medical_encounter: "🏥", prescription: "💊" }[ch] ?? "🔔";
}
function channelBg(ch) {
  return { appointment: "bg-blue-100", queue: "bg-purple-100", telehealth: "bg-cyan-100", doctor_leave: "bg-orange-100", doctor_schedule: "bg-green-100", medical_encounter: "bg-red-100", prescription: "bg-yellow-100" }[ch] ?? "bg-gray-100";
}
function statusClass(s) {
  return { pending: "bg-amber-50 text-amber-600", sent: "bg-emerald-50 text-emerald-600", failed: "bg-red-50 text-red-600", read: "bg-gray-100 text-gray-500" }[s] ?? "bg-gray-100 text-gray-500";
}
function formatTime(dt) {
  if (!dt) return "";
  return new Date(dt).toLocaleString("en-ET", { day: "numeric", month: "short", hour: "2-digit", minute: "2-digit" });
}

onMounted(async () => {
  store.error = null;
  store.notifications = [];
  await store.fetchAll();
  await store.fetchPreferences();
});
</script>
