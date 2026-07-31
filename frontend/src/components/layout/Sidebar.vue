<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { Menu } from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import { useSidebar } from "../../composables/useSidebar";
import platformAdmin from "../../config/sidebar/platformAdmin";
import hospitalAdmin from "../../config/sidebar/hospitalAdmin";
import doctor from "../../config/sidebar/doctor";
import patient from "../../config/sidebar/patient";
import receptionist from "../../config/sidebar/receptionist";

const router = useRouter();
const route  = useRoute();
const { t }  = useI18n();
const authStore = useAuthStore();
const { isOpen, isMobileOpen, toggle, closeMenu } = useSidebar();

const defaultSidebar = {
  theme: { title: "", subtitle: "", background: "bg-white" },
  menu: [],
};

const sidebarConfig = computed(() => {
  const role = authStore.user?.roles?.[0]?.name;
  switch (role) {
    case "platform_admin": return platformAdmin;
    case "hospital_admin": return hospitalAdmin;
    case "doctor":         return doctor;
    case "patient":        return patient;
    case "receptionist":   return receptionist;
    default:               return defaultSidebar;
  }
});

const menuItems = computed(() => {
  const role = authStore.user?.roles?.[0]?.name;
  if (!role) return [];
  return sidebarConfig.value.menu.filter(
    (item) => item.roles?.includes(role) && item.action !== "logout"
  );
});

/** Translate nav item title — fall back to the hardcoded English title */
function itemLabel(item) {
  if (!item.titleKey) return item.title;
  const translated = t(item.titleKey);
  // vue-i18n returns the key itself when not found, so check for that
  return translated === item.titleKey ? item.title : translated;
}

function handleMenuClick(item) {
  router.push(item.route);
  closeMenu();
}
</script>

<template>
  <!-- Mobile overlay backdrop -->
  <Transition name="fade">
    <div
      v-if="isMobileOpen"
      class="fixed inset-0 z-30 bg-black/40 lg:hidden"
      @click="closeMenu"
    />
  </Transition>

  <!-- Sidebar panel -->
  <!-- expanded: w-48 (192px) | collapsed: w-[52px] -->
  <aside
    :class="[
      sidebarConfig.theme.background,
      'fixed lg:relative inset-y-0 left-0 z-40 lg:z-auto',
      'flex flex-col h-screen border-r border-gray-100 dark:border-slate-700',
      'transition-[width] duration-300 ease-in-out flex-shrink-0 dark:bg-slate-800',
      isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      isOpen ? 'w-48' : 'lg:w-[52px] w-48',
    ]"
  >
    <!-- Brand header -->
    <div class="h-16 border-b border-gray-100 dark:border-slate-700 flex items-center flex-shrink-0 px-2 gap-1">

      <!-- SC logo — smaller when collapsed -->
      <div
        :class="isOpen ? 'w-7 h-7 rounded-md' : 'w-6 h-6 rounded'"
        class="bg-[#004795] flex items-center justify-center flex-shrink-0 transition-all duration-300"
      >
        <span :class="isOpen ? 'text-[10px]' : 'text-[9px]'" class="font-bold text-white leading-none">SC</span>
      </div>

      <!-- Expanded: title + hamburger -->
      <template v-if="isOpen">
        <div class="flex-1 min-w-0 pl-1.5 overflow-hidden">
          <p class="text-[11px] font-bold text-[#0A3D80] dark:text-blue-400 truncate leading-tight">
            {{ sidebarConfig.theme.title }}
          </p>
          <p class="text-[9px] text-gray-400 dark:text-slate-500 truncate leading-tight">
            {{ sidebarConfig.theme.subtitle }}
          </p>
        </div>
        <button
          @click="toggle"
          class="w-6 h-6 flex items-center justify-center rounded text-gray-400 dark:text-slate-500
                 hover:bg-gray-100 dark:hover:bg-slate-700 transition flex-shrink-0"
          aria-label="Collapse sidebar"
        >
          <Menu class="w-3.5 h-3.5" />
        </button>
      </template>

      <!-- Collapsed (desktop): small hamburger next to SC -->
      <button
        v-else
        @click="toggle"
        class="hidden lg:flex w-6 h-6 items-center justify-center rounded text-gray-400
               dark:text-slate-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition flex-shrink-0"
        aria-label="Expand sidebar"
      >
        <Menu class="w-3.5 h-3.5" />
      </button>
    </div>

    <!-- Scrollable nav -->
    <nav class="flex-1 overflow-y-auto py-2 px-1.5 space-y-0.5">
      <div
        v-for="item in menuItems"
        :key="item.titleKey || item.title"
        @click="handleMenuClick(item)"
        :title="!isOpen ? itemLabel(item) : ''"
        class="flex items-center rounded-lg cursor-pointer transition-colors group relative"
        :class="[
          route.path === item.route
            ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400'
            : 'hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-100',
          isOpen ? 'gap-2.5 px-2.5 py-2' : 'lg:justify-center lg:px-0 lg:py-2 gap-2.5 px-2.5 py-2',
        ]"
      >
        <component :is="item.icon" class="w-[18px] h-[18px] flex-shrink-0" />
        <span
          class="text-xs font-medium whitespace-nowrap transition-all duration-300 overflow-hidden"
          :class="isOpen ? 'opacity-100 max-w-[120px]' : 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none opacity-100 max-w-[120px]'"
        >
          {{ itemLabel(item) }}
        </span>

        <!-- Tooltip when collapsed (desktop) -->
        <div
          v-if="!isOpen"
          class="hidden lg:block absolute left-full ml-2 px-2 py-1 bg-gray-800 dark:bg-slate-700
                 text-white text-xs rounded-md whitespace-nowrap
                 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50"
        >
          {{ itemLabel(item) }}
        </div>
      </div>
    </nav>
  </aside>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.fade-text-enter-active, .fade-text-leave-active { transition: opacity 0.15s; }
.fade-text-enter-from, .fade-text-leave-to { opacity: 0; }
</style>
