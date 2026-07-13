<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Good {{ greeting }}, {{ authStore.user?.first_name ?? 'Receptionist' }}
        </h1>
        <p class="text-xs text-gray-500 mt-0.5">
          {{ todayStr }} · Reception Portal
        </p>
      </div>

      <!-- KPI cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div v-for="card in kpiCards" :key="card.label"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
            :class="card.bg">
            <component :is="card.icon" class="w-5 h-5" :class="card.color" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">{{ card.label }}</p>
            <p v-if="store.loading" class="h-5 w-12 bg-gray-100 animate-pulse rounded mt-0.5" />
            <p v-else class="text-xl font-bold text-gray-800">{{ card.value }}</p>
          </div>
        </div>
      </div>

      <!-- Two columns -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's appointments -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-bold text-gray-800">Today's Appointments</span>
            <router-link to="/receptionist/appointments"
              class="text-xs font-semibold text-[#004795] hover:underline">View all</router-link>
          </div>
          <div v-if="store.loading" class="divide-y divide-gray-50">
            <div v-for="n in 3" :key="n" class="px-5 py-3 flex items-center gap-3">
              <div class="h-8 w-8 rounded-full bg-gray-100 animate-pulse flex-shrink-0" />
              <div class="flex-1 space-y-2">
                <div class="h-3 bg-gray-100 animate-pulse rounded w-3/4" />
                <div class="h-2.5 bg-gray-100 animate-pulse rounded w-1/2" />
              </div>
            </div>
          </div>
          <div v-else-if="!store.todayAppointments.length"
            class="py-12 text-center text-gray-400">
            <CalendarDays class="w-8 h-8 mx-auto mb-2 text-gray-300" />
            <p class="text-sm font-medium">No appointments today</p>
          </div>
          <div v-else class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
            <div v-for="appt in store.todayAppointments.slice(0, 8)" :key="appt.id"
              class="px-5 py-3 flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-xs font-bold text-[#004795] flex-shrink-0">
                  {{ initials(appt.patient?.user) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-800">
                    {{ appt.patient?.user?.first_name }} {{ appt.patient?.user?.last_name }}
                  </p>
                  <p class="text-xs text-gray-400">
                    Dr. {{ appt.doctor?.user?.first_name }} · {{ formatTime(appt.scheduled_time) }}
                  </p>
                </div>
              </div>
              <span :class="statusClass(appt.status)"
                class="text-[11px] font-semibold px-2 py-0.5 rounded-full border capitalize">
                {{ appt.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Quick actions -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <p class="text-sm font-bold text-gray-800 mb-4">Quick Actions</p>
          <div class="grid grid-cols-2 gap-3">
            <router-link to="/receptionist/registration"
              class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-[#004795]/30 hover:bg-blue-50/50 transition group cursor-pointer">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-[#004795] transition">
                <UserPlus class="w-5 h-5 text-[#004795] group-hover:text-white transition" />
              </div>
              <span class="text-xs font-semibold text-gray-700">Register Patient</span>
            </router-link>

            <router-link to="/receptionist/queue"
              class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 transition group cursor-pointer">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-500 transition">
                <ListOrdered class="w-5 h-5 text-emerald-600 group-hover:text-white transition" />
              </div>
              <span class="text-xs font-semibold text-gray-700">Manage Queue</span>
            </router-link>

            <router-link to="/receptionist/appointments"
              class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-amber-300 hover:bg-amber-50/50 transition group cursor-pointer">
              <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-500 transition">
                <CalendarDays class="w-5 h-5 text-amber-600 group-hover:text-white transition" />
              </div>
              <span class="text-xs font-semibold text-gray-700">Appointments</span>
            </router-link>

            <router-link to="/receptionist/profile"
              class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition group cursor-pointer">
              <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-500 transition">
                <UserCircle class="w-5 h-5 text-purple-600 group-hover:text-white transition" />
              </div>
              <span class="text-xs font-semibold text-gray-700">My Profile</span>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { CalendarDays, UserPlus, ListOrdered, UserCircle, Users, ClipboardList } from "lucide-vue-next";
import { useAuthStore } from "../../stores/authStore";
import { useReceptionistStore } from "../../stores/receptionistStore";

const authStore = useAuthStore();
const store     = useReceptionistStore();

const hour = new Date().getHours();
const greeting = hour < 12 ? "Morning" : hour < 17 ? "Afternoon" : "Evening";

const todayStr = new Date().toLocaleDateString("en-US", {
  weekday: "long", year: "numeric", month: "long", day: "numeric",
});

const kpiCards = computed(() => [
  {
    label: "Total Patients",
    value: store.patients.length,
    icon: Users,
    bg: "bg-blue-50",
    color: "text-[#004795]",
  },
  {
    label: "Today's Appointments",
    value: store.todayAppointments.length,
    icon: CalendarDays,
    bg: "bg-amber-50",
    color: "text-amber-600",
  },
  {
    label: "Confirmed",
    value: store.appointments.filter((a) => a.status === "confirmed").length,
    icon: ClipboardList,
    bg: "bg-emerald-50",
    color: "text-emerald-600",
  },
  {
    label: "Pending",
    value: store.appointments.filter((a) => a.status === "pending").length,
    icon: ClipboardList,
    bg: "bg-orange-50",
    color: "text-orange-500",
  },
]);

function initials(user) {
  return ((user?.first_name?.[0] ?? "") + (user?.last_name?.[0] ?? "")).toUpperCase() || "?";
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
}

function statusClass(status) {
  return {
    pending:   "bg-amber-50 text-amber-700 border-amber-200",
    confirmed: "bg-emerald-50 text-emerald-700 border-emerald-200",
    cancelled: "bg-red-50 text-red-600 border-red-200",
    completed: "bg-blue-50 text-blue-600 border-blue-200",
    no_show:   "bg-gray-50 text-gray-500 border-gray-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200";
}

onMounted(async () => {
  await Promise.all([
    store.fetchPatients(),
    store.fetchAppointments(),
  ]);
});
</script>
