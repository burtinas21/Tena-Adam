<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-2xl mx-auto">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">My Profile</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your account information.</p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Avatar banner -->
        <div class="bg-gradient-to-r from-[#004795] to-[#0069d9] h-24 relative" />

        <div class="px-6 pb-6">
          <!-- Avatar -->
          <div class="relative -mt-10 mb-4 w-fit">
            <div class="w-20 h-20 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden">
              <span class="text-2xl font-bold text-[#004795]">
                {{ initials }}
              </span>
            </div>
          </div>

          <!-- Name + role -->
          <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">
              {{ user?.first_name }} {{ user?.last_name }}
            </h2>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-[#004795] border border-blue-100">
                Receptionist
              </span>
              <span v-if="user?.is_active"
                class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                Active
              </span>
            </div>
          </div>

          <!-- Info grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="field in fields" :key="field.label"
              class="bg-gray-50 rounded-xl p-4 border border-gray-100">
              <p class="text-xs font-semibold text-gray-500 mb-1">{{ field.label }}</p>
              <p class="text-sm font-medium text-gray-800">{{ field.value || '—' }}</p>
            </div>
          </div>

          <!-- Last login -->
          <div v-if="user?.last_login" class="mt-4 flex items-center gap-2 text-xs text-gray-400">
            <Clock class="w-3.5 h-3.5" />
            Last login: {{ formatDate(user.last_login) }}
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed } from "vue";
import { Clock } from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const initials = computed(() => {
  const f = user.value?.first_name?.[0] ?? "";
  const l = user.value?.last_name?.[0] ?? "";
  return (f + l).toUpperCase() || "R";
});

const fields = computed(() => [
  { label: "First Name", value: user.value?.first_name },
  { label: "Last Name",  value: user.value?.last_name  },
  { label: "Email",      value: user.value?.email      },
  { label: "Phone",      value: user.value?.phone      },
]);

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleString("en-GB", {
    day: "2-digit", month: "short", year: "numeric",
    hour: "2-digit", minute: "2-digit",
  });
}
</script>
