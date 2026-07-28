<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
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
const { isOpen, isMobileOpen, closeMenu } = useSidebar();

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
  <Transition name="slide">
    <aside
      v-show="isMobileOpen || true"
      :class="[
        sidebarConfig.theme.background,
        'fixed lg:relative inset-y-0 left-0 z-40 lg:z-auto',
        'flex flex-col h-screen border-r border-gray-100 dark:border-slate-700 transition-all duration-300 ease-in-out flex-shrink-0 dark:bg-slate-800',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isOpen ? 'w-60' : 'lg:w-16',
        'w-60',
      ]"
    >
      <!-- Brand header -->
      <div class="px-3 pt-4 pb-3 border-b border-gray-100 dark:border-slate-700 flex items-center gap-3 flex-shrink-0 overflow-hidden">
        <div class="w-8 h-8 rounded-lg bg-[#004795] flex items-center justify-center flex-shrink-0">
          <span class="text-xs font-bold text-white">SC</span>
        </div>
        <Transition name="fade-text">
          <div v-if="isOpen" class="overflow-hidden">
            <h1 class="text-sm font-bold text-[#0A3D80] dark:text-blue-400 whitespace-nowrap">
              {{ sidebarConfig.theme.title }}
            </h1>
            <p class="text-[10px] text-gray-400 dark:text-slate-500 whitespace-nowrap">
              {{ sidebarConfig.theme.subtitle }}
            </p>
          </div>
        </Transition>
      </div>

      <!-- Scrollable nav -->
      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        <div
          v-for="item in menuItems"
          :key="item.titleKey || item.title"
          @click="handleMenuClick(item)"
          :title="!isOpen ? itemLabel(item) : ''"
          class="flex items-center gap-3 rounded-lg cursor-pointer transition-colors group relative"
          :class="[
            route.path === item.route
              ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400'
              : 'hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-100',
            isOpen ? 'px-3 py-2.5' : 'lg:px-2 lg:py-2.5 lg:justify-center px-3 py-2.5',
          ]"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span
            class="text-sm font-medium whitespace-nowrap overflow-hidden transition-all duration-300"
            :class="isOpen ? 'opacity-100 max-w-full' : 'lg:opacity-0 lg:max-w-0 opacity-100 max-w-full'"
          >
            {{ itemLabel(item) }}
          </span>

          <!-- Tooltip on collapsed desktop -->
          <div
            v-if="!isOpen"
            class="hidden lg:block absolute left-full ml-2 px-2 py-1 bg-gray-800 dark:bg-slate-700 text-white text-xs rounded-md whitespace-nowrap
                   opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50"
          >
            {{ itemLabel(item) }}
          </div>
        </div>
      </nav>
    </aside>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.fade-text-enter-active, .fade-text-leave-active { transition: opacity 0.15s; }
.fade-text-enter-from, .fade-text-leave-to { opacity: 0; }
</style>
