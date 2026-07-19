<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-5xl mx-auto">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Notifications</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">Manage and send notifications</p>
        </div>
        <button @click="showSendModal = true"
          class="bg-[#004795] hover:bg-[#003670] text-white text-xs font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition shadow-sm">
          <Send class="w-3.5 h-3.5" /> Send Notification
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit mb-6">
        <button v-for="tab in visibleTabs" :key="tab.key" @click="activeTab = tab.key"
          :class="activeTab === tab.key ? 'bg-white text-[#004795] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
          class="px-4 py-2 text-xs font-bold rounded-lg transition">
          {{ tab.label }}
        </button>
      </div>

      <!-- Error -->
      <div v-if="store.error" class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3">
        <AlertCircle class="w-4 h-4 flex-shrink-0" /> {{ store.error }}
      </div>

      <!-- TAB: My Notifications -->
      <div v-if="activeTab === 'mine'">
        <div class="flex items-center justify-between mb-3">
          <p class="text-sm font-semibold text-gray-700">Your notifications</p>
          <button v-if="store.unreadCount > 0" @click="store.markAllRead()"
            class="text-xs text-[#004795] font-semibold hover:underline flex items-center gap-1">
            <CheckCheck class="w-3 h-3" /> Mark all read
          </button>
        </div>
        <div v-if="store.loading" class="space-y-2">
          <div v-for="n in 4" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>
        <div v-else-if="!store.notifications.length"
          class="bg-white rounded-2xl border border-gray-100 py-12 flex flex-col items-center text-gray-400">
          <Bell class="w-8 h-8 mb-2 text-gray-300" />
          <p class="text-sm font-medium">No notifications</p>
        </div>
        <div v-else class="space-y-2">
          <div v-for="n in store.notifications" :key="n.id"
            :class="n.status !== 'read' ? 'bg-blue-50/50 border-blue-100' : 'bg-white border-gray-100'"
            class="rounded-xl border shadow-sm p-4 flex items-start gap-3 hover:shadow-md transition-shadow">
            <div class="text-lg flex-shrink-0 mt-0.5">{{ channelIcon(n.channel) }}</div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800">{{ n.subject || 'Notification' }}</p>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ n.content }}</p>
              <div class="flex items-center gap-3 mt-2">
                <span class="text-[10px] text-gray-400">{{ formatTime(n.created_at) }}</span>
                <span :class="statusClass(n.status)" class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full capitalize">{{ n.status }}</span>
                <span class="text-[10px] text-gray-400 capitalize">{{ n.type }} · {{ n.channel?.replace(/_/g, ' ') }}</span>
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
              <button @click="store.destroy(n.id)"
                class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition" title="Delete">
                <Trash2 class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: Preferences -->
      <div v-if="activeTab === 'prefs'">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h2 class="text-sm font-bold text-gray-700 mb-4">Notification Preferences</h2>
          <div v-if="store.prefsLoading" class="text-xs text-gray-400">Loading...</div>
          <div v-else-if="store.preferences" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-for="pref in prefFields" :key="pref.key"
                class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ pref.label }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ pref.desc }}</p>
                </div>
                <button type="button" @click="store.preferences[pref.key] = !store.preferences[pref.key]"
                  :class="store.preferences[pref.key] ? 'bg-[#004795]' : 'bg-gray-200'"
                  class="relative w-10 h-5 rounded-full transition-colors duration-200 flex-shrink-0">
                  <span :class="store.preferences[pref.key] ? 'translate-x-5' : 'translate-x-0.5'"
                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200" />
                </button>
              </div>
            </div>
            <div class="flex justify-end pt-2">
              <button @click="savePrefs" :disabled="prefsSaving"
                class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2">
                <Loader2 v-if="prefsSaving" class="w-3.5 h-3.5 animate-spin" />
                Save Preferences
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB: Templates (platform_admin only) -->
      <div v-if="activeTab === 'templates'">
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-semibold text-gray-700">Notification Templates</p>
          <button @click="openCreateTemplate"
            class="text-xs font-semibold text-white bg-[#004795] hover:bg-[#003670] px-3 py-2 rounded-lg transition flex items-center gap-1.5">
            <Plus class="w-3.5 h-3.5" /> New Template
          </button>
        </div>
        <div v-if="templatesLoading" class="space-y-2">
          <div v-for="n in 3" :key="n" class="h-14 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>
        <div v-else-if="!templates.length"
          class="bg-white rounded-2xl border border-gray-100 py-12 text-center text-gray-400 text-sm">
          No templates yet.
        </div>
        <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Subject</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in templates" :key="t.id" class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-5 py-3 font-semibold text-gray-800">{{ t.name }}</td>
                <td class="px-5 py-3 text-gray-500 hidden sm:table-cell">{{ t.subject || '—' }}</td>
                <td class="px-5 py-3">
                  <span :class="t.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200'"
                    class="text-[10px] font-semibold px-2 py-0.5 rounded-full border">
                    {{ t.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-5 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button @click="openEditTemplate(t)" class="text-xs font-semibold text-amber-600 hover:underline">Edit</button>
                    <button @click="deleteTemplate(t.id)" class="text-xs font-semibold text-red-500 hover:underline">Delete</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ── Send Notification Modal ────────────────────────────────── -->
    <div v-if="showSendModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="showSendModal = false">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <h3 class="text-sm font-bold text-gray-800">Send Notification</h3>
          <button @click="showSendModal = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div v-if="sendError" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ sendError }}
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Recipient User ID <span class="text-red-500">*</span></label>
            <input v-model="sendForm.user_id" type="text" placeholder="UUID of the user"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
              <select v-model="sendForm.type"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition">
                <option value="email">Email</option>
                <option value="in_app">In-App</option>
                <option value="sms">SMS</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Channel <span class="text-red-500">*</span></label>
              <input v-model="sendForm.channel" type="text" placeholder="e.g. appointment"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Subject</label>
            <input v-model="sendForm.subject" type="text" placeholder="Notification subject"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
            <textarea v-model="sendForm.content" rows="3" placeholder="Notification content..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
          <button @click="showSendModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleSend" :disabled="!canSend || sendSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2">
            <Loader2 v-if="sendSaving" class="w-3.5 h-3.5 animate-spin" />
            Send
          </button>
        </div>
      </div>
    </div>

    <!-- ── Template Create/Edit Modal ─────────────────────────────── -->
    <div v-if="showTemplateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
      @click.self="showTemplateModal = false">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
          <h3 class="text-sm font-bold text-gray-800">{{ editingTemplate ? 'Edit Template' : 'New Template' }}</h3>
          <button @click="showTemplateModal = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition">
            <X class="w-4 h-4" />
          </button>
        </div>
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
          <div v-if="templateError" class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2.5">
            <AlertCircle class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />{{ templateError }}
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Template Name <span class="text-red-500">*</span></label>
              <input v-model="templateForm.name" type="text" placeholder="e.g. appointment_reminder"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Subject</label>
              <input v-model="templateForm.subject" type="text" placeholder="Email subject line"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Body</label>
            <textarea v-model="templateForm.email_body" rows="3" placeholder="Email content — use {{variable}} for dynamic values"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">SMS Body</label>
            <textarea v-model="templateForm.sms_body" rows="2" placeholder="SMS content"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Push Body</label>
            <textarea v-model="templateForm.push_body" rows="2" placeholder="Push notification content"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#004795]/30 focus:border-[#004795] transition resize-none" />
          </div>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="templateForm.is_active" type="checkbox" class="rounded" />
            <span class="text-xs font-semibold text-gray-700">Active</span>
          </label>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
          <button @click="showTemplateModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Cancel</button>
          <button @click="handleSaveTemplate" :disabled="!templateForm.name.trim() || templateSaving"
            class="px-5 py-2 text-sm font-semibold text-white bg-[#004795] hover:bg-[#003670] rounded-lg transition disabled:opacity-50 flex items-center gap-2">
            <Loader2 v-if="templateSaving" class="w-3.5 h-3.5 animate-spin" />
            {{ editingTemplate ? 'Save' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Bell, CheckCheck, RefreshCw, Trash2, AlertCircle, Send, X, Loader2, Plus } from "lucide-vue-next";
import { useNotificationStore } from "../../stores/notificationStore";
import { useAuthStore } from "../../stores/authStore";
import notificationApi from "../../api/notificationApi";

const store = useNotificationStore();
const authStore = useAuthStore();
const activeTab = ref("mine");

const isPlatformAdmin = computed(() =>
  authStore.user?.roles?.some((r) => r.name === "platform_admin")
);

const tabs = [
  { key: "mine", label: "My Notifications" },
  { key: "prefs", label: "Preferences" },
  { key: "templates", label: "Templates", adminOnly: true },
];
const visibleTabs = computed(() =>
  tabs.filter((t) => !t.adminOnly || isPlatformAdmin.value)
);

// ── Send Modal ────────────────────────────────────────────────────────────
const showSendModal = ref(false);
const sendSaving = ref(false);
const sendError = ref(null);
const sendForm = ref({ user_id: "", type: "in_app", channel: "general", subject: "", content: "" });
const canSend = computed(() => sendForm.value.user_id.trim() && sendForm.value.content.trim() && sendForm.value.channel.trim());

async function handleSend() {
  sendError.value = null;
  sendSaving.value = true;
  try {
    await store.send(sendForm.value);
    showSendModal.value = false;
    sendForm.value = { user_id: "", type: "in_app", channel: "general", subject: "", content: "" };
    await store.fetchAll();
  } catch (err) {
    const errors = err.response?.data?.errors;
    sendError.value = errors ? Object.values(errors).flat().join(" ") : err.response?.data?.message || "Failed to send.";
  } finally {
    sendSaving.value = false;
  }
}

// ── Preferences ───────────────────────────────────────────────────────────
const prefsSaving = ref(false);
const prefFields = [
  { key: "email_enabled",         label: "Email Notifications",    desc: "Receive notifications via email" },
  { key: "sms_enabled",           label: "SMS Notifications",      desc: "Receive notifications via SMS" },
  { key: "push_enabled",          label: "Push Notifications",     desc: "Receive browser push notifications" },
  { key: "appointment_reminders", label: "Appointment Reminders",  desc: "Reminders for upcoming appointments" },
  { key: "queue_updates",         label: "Queue Updates",          desc: "Updates when your queue status changes" },
  { key: "promotional",           label: "Promotional",            desc: "Health tips and promotional updates" },
];

async function savePrefs() {
  prefsSaving.value = true;
  try { await store.savePreferences(store.preferences); }
  finally { prefsSaving.value = false; }
}

// ── Templates ─────────────────────────────────────────────────────────────
const templates = ref([]);
const templatesLoading = ref(false);
const showTemplateModal = ref(false);
const editingTemplate = ref(null);
const templateSaving = ref(false);
const templateError = ref(null);
const templateForm = ref({ name: "", subject: "", email_body: "", sms_body: "", push_body: "", is_active: true });

async function loadTemplates() {
  templatesLoading.value = true;
  try {
    const res = await notificationApi.getTemplates();
    templates.value = res.data?.data ?? res.data ?? [];
  } catch { /* silent — will show empty state */ }
  finally { templatesLoading.value = false; }
}

function openCreateTemplate() {
  editingTemplate.value = null;
  templateForm.value = { name: "", subject: "", email_body: "", sms_body: "", push_body: "", is_active: true };
  templateError.value = null;
  showTemplateModal.value = true;
}

function openEditTemplate(t) {
  editingTemplate.value = t;
  templateForm.value = { name: t.name, subject: t.subject ?? "", email_body: t.email_body ?? "", sms_body: t.sms_body ?? "", push_body: t.push_body ?? "", is_active: t.is_active };
  templateError.value = null;
  showTemplateModal.value = true;
}

async function handleSaveTemplate() {
  templateError.value = null;
  templateSaving.value = true;
  try {
    if (editingTemplate.value) {
      await notificationApi.updateTemplate(editingTemplate.value.id, templateForm.value);
    } else {
      await notificationApi.createTemplate(templateForm.value);
    }
    showTemplateModal.value = false;
    await loadTemplates();
  } catch (err) {
    const errors = err.response?.data?.errors;
    templateError.value = errors ? Object.values(errors).flat().join(" ") : err.response?.data?.message || "Failed to save template.";
  } finally {
    templateSaving.value = false;
  }
}

async function deleteTemplate(id) {
  try {
    await notificationApi.destroyTemplate(id);
    templates.value = templates.value.filter((t) => t.id !== id);
  } catch { /* silent */ }
}

// ── Helpers ───────────────────────────────────────────────────────────────
function channelIcon(ch) {
  return { appointment: "📅", queue: "🔢", telehealth: "💻", doctor_leave: "📋", doctor_schedule: "🗓️", medical_encounter: "🏥", prescription: "💊" }[ch] ?? "🔔";
}

function statusClass(s) {
  return { pending: "bg-amber-50 text-amber-600", sent: "bg-emerald-50 text-emerald-600", failed: "bg-red-50 text-red-600", read: "bg-gray-100 text-gray-500" }[s] ?? "bg-gray-100 text-gray-500";
}

function formatTime(dt) {
  if (!dt) return "";
  return new Date(dt).toLocaleString("en-ET", { day: "numeric", month: "short", hour: "2-digit", minute: "2-digit" });
}

onMounted(async () => {
  await store.fetchAll();
  await store.fetchPreferences();
  if (isPlatformAdmin.value) await loadTemplates();
});
</script>
