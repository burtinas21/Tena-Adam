<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { Menu, LogOut, Bell, ChevronDown, Search } from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import { useSidebar } from "../../composables/useSidebar";
import doctorApi from "../../api/doctorApi";

const router = useRouter();
const route  = useRoute();
const authStore = useAuthStore();
const { toggle } = useSidebar();

const showUserMenu   = ref(false);
const searchQuery    = ref("");
const profilePicture = ref(null);  // fetched from backend for doctor role

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
  const fromLogin = user.value?.healthcare_provider?.profile_picture_url
    ?? user.value?.healthcareProvider?.profile_picture_url;
  return fromLogin || profilePicture.value || null;
});

const isDoctor = computed(() =>
  user.value?.roles?.some((r) => r.name === "doctor")
);

// Fetch doctor profile picture if not already in stored user object
async function fetchDoctorPhoto() {
  if (!isDoctor.value) return;
  if (avatarUrl.value) return;   // already have it from login response
  try {
    const res = await doctorApi.getMe();
    const data = res.data?.data ?? res.data;
    if (data?.profile_picture_url) {
      profilePicture.value = data.profile_picture_url;
    }
  } catch {
    /* silent — will fall back to initials */
  }
}

onMounted(fetchDoctorPhoto);

// Placeholder adapts to current page
const searchPlaceholder = computed(() => {
  const p = route.path;
  if (p.includes("/doctors")) return "Search Doctor Name, Specialization...";
  if (p.includes("/hospitals")) return "Search hospitals...";
  if (p.includes("/appointments")) return "Search appointments...";
  return "Search...";
});

function handleSearch() {
  const q = searchQuery.value.trim();
  if (route.path.includes("/patient/doctors")) {
    router.replace({ path: route.path, query: q ? { q } : {} });
  } else if (q) {
    const role = user.value?.roles?.[0]?.name ?? "";
    if (role === "patient") {
      router.push({ path: "/patient/doctors", query: { q } });
    }
  }
}

async function logout() {
  showUserMenu.value = false;
  await authStore.logout();
  router.replace("/login");
}
</script>

<template>
  <header class="h-16 w-full bg-white border-b border-gray-100 flex items-center px-4 gap-3 flex-shrink-0 z-20">

    <!-- Hamburger / toggle button -->
    <button
      @click="toggle"
      class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition flex-shrink-0"
      aria-label="Toggle sidebar"
    >
      <Menu class="w-5 h-5" />
    </button>

    <!-- Search bar -->
    <div class="flex-1 max-w-md relative">
      <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="searchPlaceholder"
        @keyup.enter="handleSearch"
        @input="handleSearch"
        class="w-full bg-gray-50 border border-gray-200 text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#004795]/20 focus:border-[#004795] transition"
      />
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-2 ml-auto">
      <!-- Notification bell -->
      <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
        <Bell class="w-5 h-5" />
        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full" />
      </button>

      <!-- User menu -->
      <div class="relative">
        <button
          @click="showUserMenu = !showUserMenu"
          class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100 transition"
        >
          <!-- Avatar: real photo when available, initials fallback -->
          <div class="w-8 h-8 rounded-full bg-[#004795] flex items-center justify-center flex-shrink-0 overflow-hidden">
            <img
              v-if="avatarUrl"
              :src="avatarUrl"
              :alt="fullName"
              class="w-full h-full object-cover"
              @error="profilePicture = null"
            />
            <span v-else class="text-xs font-bold text-white">{{ initials }}</span>
          </div>
          <div class="hidden sm:block text-left">
            <p class="text-xs font-semibold text-gray-800 leading-tight">{{ fullName }}</p>
            <p class="text-[10px] text-gray-400 leading-tight">{{ roleName }}</p>
          </div>
          <ChevronDown
            class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
            :class="{ 'rotate-180': showUserMenu }"
          />
        </button>

        <!-- Dropdown -->
        <Transition name="dropdown">
          <div
            v-if="showUserMenu"
            class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
          >
            <div class="px-4 py-3 border-b border-gray-50">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-[#004795] flex items-center justify-center flex-shrink-0 overflow-hidden">
                  <img v-if="avatarUrl" :src="avatarUrl" :alt="fullName"
                    class="w-full h-full object-cover" />
                  <span v-else class="text-sm font-bold text-white">{{ initials }}</span>
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-semibold text-gray-800 truncate">{{ fullName }}</p>
                  <p class="text-xs text-gray-400 mt-0.5 truncate">{{ user?.email }}</p>
                </div>
              </div>
            </div>
            <button
              @click="logout"
              class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition"
            >
              <LogOut class="w-4 h-4" />
              Sign out
            </button>
          </div>
        </Transition>

        <!-- Click-outside backdrop -->
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
.dropdown-enter-active, .dropdown-leave-active {
  transition: opacity 0.15s, transform 0.15s;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
