<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import {
  Menu,
  LogOut,
  Bell,
  ChevronDown,
  Search,
  X,
  PanelLeft,
  CheckCheck,
} from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import { useNotificationStore } from "../../stores/notificationStore";
import { useSidebar } from "../../composables/useSidebar";
import doctorApi from "../../api/doctorApi";
import ThemeToggle from "../common/ThemeToggle.vue";
import LanguageSwitcher from "../common/LanguageSwitcher.vue";
const router = useRouter();
const route = useRoute();
const { t } = useI18n();
const authStore = useAuthStore();
const notifStore = useNotificationStore();
const { toggle } = useSidebar();

const showUserMenu = ref(false);
const showNotifPanel = ref(false);
const searchQuery = ref("");
const profilePicture = ref(null);

const user = computed(() => authStore.user);

const fullName = computed(() => {
  if (!user.value) return "User";
  return `${user.value.first_name ?? ""} ${user.value.last_name ?? ""}`.trim();
});

const initials = computed(() => {
  const f = user.value?.first_name?.[0] ?? "";
  const l = user.value?.last_name?.[0] ?? "";
  return (f + l).toUpperCase() || "U";
});

const roleName = computed(() => {
  const r = user.value?.roles?.[0]?.name ?? "";
  return r.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
});

const avatarUrl = computed(() => {
  const fromLogin =
    user.value?.healthcare_provider?.profile_picture_url ??
    user.value?.healthcareProvider?.profile_picture_url;
  return fromLogin || profilePicture.value || null;
});

const isDoctor = computed(() =>
  user.value?.roles?.some((r) => r.name === "doctor"),
);

// Notification route per role
const notifRoute = computed(() => {
  const role = user.value?.roles?.[0]?.name ?? "";
  if (role === "patient") return "/patient/notification";
  if (role === "hospital_admin") return "/hospital-admin/notifications";
  if (role === "platform_admin") return "/platform/notifications";
  if (role === "receptionist") return "/receptionist/notification";
  if (role === "doctor") return "/doctor/notifications";
  return null;
});

// Recent 5 notifications for the dropdown preview — always newest first
const recentNotifs = computed(() =>
  [...notifStore.notifications]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5),
);

async function fetchDoctorPhoto() {
  if (!isDoctor.value) return;
  if (avatarUrl.value) return;
  try {
    const res = await doctorApi.getMe();
    const data = res.data?.data ?? res.data;
    if (data?.profile_picture_url)
      profilePicture.value = data.profile_picture_url;
  } catch {
    /* silent */
  }
}

// Poll unread count every 60s; refresh full list every 2 min
let pollInterval = null;
let fullPollInterval = null;
async function startPolling() {
  await notifStore.fetchUnreadCount();
  pollInterval = setInterval(() => notifStore.fetchUnreadCount(), 60000);
  fullPollInterval = setInterval(() => notifStore.fetchAll(), 120000);
}

async function openNotifPanel() {
  showNotifPanel.value = !showNotifPanel.value;
  showUserMenu.value = false;
  if (showNotifPanel.value) {
    await notifStore.fetchAll();
  }
}

async function handleMarkAllRead() {
  await notifStore.markAllRead();
}

async function handleMarkRead(n) {
  if (n.status !== "read") await notifStore.markAsRead(n.id);
  // Remove from the in-memory list so it disappears from the dropdown
  notifStore.notifications = notifStore.notifications.filter(
    (x) => x.id !== n.id,
  );
  showNotifPanel.value = false;
  const target = getNotifRoute(n);
  if (target) router.push(target);
}

/**
 * Resolve the router path for a notification based on channel and user role.
 * Clicking a notification navigates to the relevant page.
 */
function getNotifRoute(n) {
  const role = user.value?.roles?.[0]?.name ?? "";

  // Map channel → route per role
  const map = {
    doctor: {
      appointment: { name: "doctor-appointments" },
      queue: { name: "Doctor_Queue" },
      telehealth: { name: "doctor-telehealth" },
      doctor_leave: { name: "doctor-schedule" },
      doctor_schedule: { name: "doctor-schedule" },
      medical_encounter: { name: "medicalencounter" },
      prescription: { name: "prescription" },
    },
    patient: {
      appointment: { name: "appointments" },
      queue: { name: "patient-queue-status" },
      telehealth: { name: "patient-telemedicine" },
      medical_encounter: { name: "medicalhistory" },
      prescription: { name: "patient-prescriptions" },
    },
    hospital_admin: {
      appointment: { name: "Appointments" },
      queue: { name: "Queue" },
      doctor_leave: { name: "Doctor_Leaves" },
      doctor_schedule: { name: "doctors" },
      telehealth: { name: "telemanagment" },
    },
    receptionist: {
      appointment: { name: "receptionist-appointments" },
      queue: { name: "receptionist-queue" },
    },
    platform_admin: {
      appointment: { path: "/platform/dashboard" },
      queue: { path: "/platform/dashboard" },
    },
  };

  return map[role]?.[n.channel] ?? null;
}

function goToAllNotifications() {
  showNotifPanel.value = false;
  if (notifRoute.value) router.push(notifRoute.value);
}

onMounted(async () => {
  fetchDoctorPhoto();
  if (authStore.user) startPolling();
});

// Also start polling if user becomes available after mount (e.g. after login redirect)
watch(
  () => authStore.user,
  (newUser) => {
    if (newUser && !pollInterval) startPolling();
  },
  { immediate: false },
);

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
  if (fullPollInterval) clearInterval(fullPollInterval);
});

const searchPlaceholder = computed(() => {
  const p = route.path;
  if (p.includes("/doctors")) return t("search.doctors");
  if (p.includes("/hospitals")) return t("search.hospitals");
  if (p.includes("/appointments")) return t("search.appointments");
  return t("search.placeholder");
});

function handleSearch() {
  const q = searchQuery.value.trim();
  if (route.path.includes("/patient/doctors")) {
    router.replace({ path: route.path, query: q ? { q } : {} });
  } else if (q) {
    const role = user.value?.roles?.[0]?.name ?? "";
    if (role === "patient")
      router.push({ path: "/patient/doctors", query: { q } });
  }
}

async function logout() {
  showUserMenu.value = false;
  await authStore.logout();
  router.replace("/login");
}

function channelIcon(channel) {
  const map = {
    appointment: "📅",
    queue: "🔢",
    telehealth: "💻",
    doctor_leave: "📋",
    doctor_schedule: "🗓️",
    medical_encounter: "🏥",
    prescription: "💊",
  };
  return map[channel] ?? "🔔";
}

/** For telehealth reminder notifications, highlight the join link in the content. */
function notifContent(n) {
  if (n.channel !== "telehealth") return n.content;
  // Truncate long content for the dropdown but keep the meeting link visible
  const lines = (n.content ?? "").split("\n").filter(Boolean);
  return lines.slice(0, 2).join(" — ");
}

/**
 * Extract a https:// meeting link from notification content, if present.
 * Returns the URL string or null.
 */
function extractMeetingLink(content) {
  if (!content) return null;
  const match = content.match(/https?:\/\/[^\s\n]+/);
  return match ? match[0] : null;
}

function timeAgo(dateStr) {
  if (!dateStr) return "";
  const diff = (Date.now() - new Date(dateStr)) / 1000;
  if (diff < 60) return t("notification.just_now");
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}
</script>

<template>
  <header
    class="h-16 w-full bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 flex items-center px-4 gap-3 flex-shrink-0 z-20 transition-colors duration-300"
  >
    <!-- Mobile-only hamburger (sidebar is hidden on mobile so we need this) -->
    <button
  @click="toggle"
  class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg
         hover:bg-gray-100 dark:hover:bg-slate-700 transition"
>
  <PanelLeft class="w-5 h-5 text-gray-600 dark:text-slate-300" />
</button>

    <!-- Search bar -->
    <div class="flex-1 max-w-md relative">
      <Search
        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500 pointer-events-none"
      />
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="searchPlaceholder"
        @keyup.enter="handleSearch"
        @input="handleSearch"
        class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#004795]/20 dark:focus:ring-blue-500/30 focus:border-[#004795] dark:focus:border-blue-500 transition"
      />
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-1 ml-auto">
      <!-- Theme toggle -->
      <ThemeToggle />
      <LanguageSwitcher />

      <!-- Notification bell -->
      <div class="relative">
        <button
          @click="openNotifPanel"
          class="relative p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition"
          aria-label="Notifications"
        >
          <Bell class="w-5 h-5" />
          <span
            v-if="notifStore.unreadCount > 0"
            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"
          >
            {{ notifStore.unreadCount > 99 ? "99+" : notifStore.unreadCount }}
          </span>
        </button>

        <Transition name="dropdown">
          <div
            v-if="showNotifPanel"
            class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 flex flex-col max-h-[420px]"
          >
            <!-- Header -->
            <div
              class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex-shrink-0"
            >
              <span class="text-sm font-bold text-gray-800 dark:text-slate-100">
                {{ $t("notification.title") }}
              </span>
              <div class="flex items-center gap-2">
                <button
                  v-if="notifStore.unreadCount > 0"
                  @click="handleMarkAllRead"
                  class="text-xs text-[#004795] dark:text-blue-400 hover:underline font-semibold flex items-center gap-1"
                >
                  <CheckCheck class="w-3 h-3" />
                  {{ $t("button.mark_all_read") }}
                </button>
                <button
                  @click="showNotifPanel = false"
                  class="p-1 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            <!-- Notification list -->
            <div class="overflow-y-auto flex-1">
              <div v-if="notifStore.loading" class="p-4 space-y-2">
                <div
                  v-for="n in 3"
                  :key="n"
                  class="h-12 bg-gray-100 dark:bg-slate-700 rounded-lg animate-pulse"
                />
              </div>
              <div
                v-else-if="!recentNotifs.length"
                class="py-10 text-center text-xs text-gray-400 dark:text-slate-500"
              >
                <Bell
                  class="w-6 h-6 mx-auto mb-2 text-gray-300 dark:text-slate-600"
                />
                {{ $t("notification.empty") }}
              </div>
              <div v-else>
                <div
                  v-for="n in recentNotifs"
                  :key="n.id"
                  :class="[
                    n.status !== 'read'
                      ? 'bg-blue-50/60 dark:bg-blue-900/20'
                      : 'bg-white dark:bg-slate-800',
                    n.channel === 'telehealth' &&
                    n.subject?.includes('Reminder')
                      ? 'border-l-2 border-l-blue-400'
                      : '',
                  ]"
                  class="w-full text-left px-4 py-3 border-b border-gray-50 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-start gap-3 group cursor-pointer"
                  @click="handleMarkRead(n)"
                >
                  <span class="text-lg flex-shrink-0 mt-0.5">{{
                    channelIcon(n.channel)
                  }}</span>
                  <div class="min-w-0 flex-1">
                    <p
                      class="text-xs font-semibold text-gray-800 dark:text-slate-100 truncate"
                    >
                      {{ n.subject || "Notification" }}
                    </p>
                    <p
                      class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 line-clamp-2"
                    >
                      {{ notifContent(n) }}
                    </p>
                    <!-- Inline "Join Meeting" button for telehealth reminder notifications -->
                    <a
                      v-if="
                        n.channel === 'telehealth' &&
                        extractMeetingLink(n.content)
                      "
                      :href="extractMeetingLink(n.content)"
                      target="_blank"
                      rel="noopener noreferrer"
                      @click.stop
                      class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold text-white bg-[#004795] hover:bg-[#003670] px-2 py-0.5 rounded transition"
                    >
                      Join Meeting →
                    </a>
                    <p
                      class="text-[10px] text-gray-400 dark:text-slate-500 mt-1"
                    >
                      {{ timeAgo(n.created_at) }}
                    </p>
                  </div>
                  <div class="flex items-center gap-1 flex-shrink-0 mt-1">
                    <span
                      v-if="n.status !== 'read'"
                      class="w-2 h-2 bg-blue-500 rounded-full"
                    />
                    <svg
                      v-if="getNotifRoute(n)"
                      class="w-3 h-3 text-gray-300 dark:text-slate-600 group-hover:text-[#004795] dark:group-hover:text-blue-400 transition"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2.5"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div
              class="border-t border-gray-100 dark:border-slate-700 px-4 py-2.5 flex-shrink-0"
              v-if="notifRoute"
            >
              <button
                @click="goToAllNotifications"
                class="text-xs font-semibold text-[#004795] dark:text-blue-400 hover:underline w-full text-center"
              >
                {{ $t("notification.view_all") }}
              </button>
            </div>
          </div>
        </Transition>

        <!-- Click-outside -->
        <div
          v-if="showNotifPanel"
          class="fixed inset-0 z-40"
          @click="showNotifPanel = false"
        />
      </div>

      <!-- User menu -->
      <div class="relative">
        <button
          @click="showUserMenu = !showUserMenu"
          class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition"
        >
          <div
            class="w-8 h-8 rounded-full bg-[#004795] flex items-center justify-center flex-shrink-0 overflow-hidden"
          >
            <img
              v-if="avatarUrl"
              :src="avatarUrl"
              :alt="fullName"
              class="w-full h-full object-cover"
              @error="profilePicture = null"
            />
            <span v-else class="text-xs font-bold text-white">{{
              initials
            }}</span>
          </div>
          <div class="hidden sm:block text-left">
            <p
              class="text-xs font-semibold text-gray-800 dark:text-slate-100 leading-tight"
            >
              {{ fullName }}
            </p>
            <p
              class="text-[10px] text-gray-400 dark:text-slate-500 leading-tight"
            >
              {{ roleName }}
            </p>
          </div>
          <ChevronDown
            class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 transition-transform duration-200"
            :class="{ 'rotate-180': showUserMenu }"
          />
        </button>

        <Transition name="dropdown">
          <div
            v-if="showUserMenu"
            class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-1 z-50"
          >
            <div
              class="px-4 py-3 border-b border-gray-50 dark:border-slate-700"
            >
              <div class="flex items-center gap-3 mb-2">
                <div
                  class="w-10 h-10 rounded-full bg-[#004795] flex items-center justify-center flex-shrink-0 overflow-hidden"
                >
                  <img
                    v-if="avatarUrl"
                    :src="avatarUrl"
                    :alt="fullName"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-sm font-bold text-white">{{
                    initials
                  }}</span>
                </div>
                <div class="min-w-0">
                  <p
                    class="text-xs font-semibold text-gray-800 dark:text-slate-100 truncate"
                  >
                    {{ fullName }}
                  </p>
                  <p
                    class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 truncate"
                  >
                    {{ user?.email }}
                  </p>
                </div>
              </div>
            </div>
            <button
              @click="logout"
              class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
            >
              <LogOut class="w-4 h-4" />
              {{ $t("logout") }}
            </button>
          </div>
        </Transition>

        <div
          v-if="showUserMenu"
          class="fixed inset-0 z-40"
          @click="showUserMenu = false"
        />
      </div>
    </div>
  </header>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition:
    opacity 0.15s,
    transform 0.15s;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
