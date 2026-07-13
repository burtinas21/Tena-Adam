<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Telemedicine</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Your virtual consultations and online doctor sessions
        </p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div v-for="n in 3" :key="n" class="h-24 bg-white rounded-xl border border-gray-100 animate-pulse" />
      </div>

      <!-- No telehealth appointments -->
      <div v-else-if="!telehealthAppts.length"
        class="bg-white rounded-xl border border-gray-100 py-20 flex flex-col items-center text-gray-400">
        <Video class="w-12 h-12 mb-3 text-gray-200" />
        <p class="text-sm font-semibold text-gray-500">No telemedicine sessions</p>
        <p class="text-xs mt-1 text-gray-400">Book a telemedicine appointment to get started.</p>
        <router-link to="/patient/appointments"
          class="mt-4 bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2 px-4 rounded-lg transition">
          Book Appointment
        </router-link>
      </div>

      <!-- Telehealth appointment list -->
      <div v-else class="space-y-4">
        <!-- Active / joinable sessions first -->
        <div v-for="appt in telehealthAppts" :key="appt.id"
          class="bg-white rounded-xl border shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:shadow-md transition-shadow"
          :class="isJoinable(appt) ? 'border-blue-200' : 'border-gray-100'">
          <div class="flex items-start gap-4">
            <!-- Doctor avatar -->
            <div class="w-12 h-12 rounded-full overflow-hidden bg-[#004795]/10 flex items-center justify-center flex-shrink-0 border border-gray-100">
              <img v-if="appt.doctor?.profile_picture_url"
                :src="appt.doctor.profile_picture_url"
                class="w-full h-full object-cover" />
              <span v-else class="text-sm font-bold text-[#004795]">
                {{ initials(appt.doctor) }}
              </span>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-gray-800 text-sm">
                  Dr. {{ appt.doctor?.user?.first_name }} {{ appt.doctor?.user?.last_name }}
                </p>
                <span v-if="isJoinable(appt)"
                  class="text-[10px] font-bold bg-blue-600 text-white px-2 py-0.5 rounded-full">
                  LIVE
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ appt.department?.name ?? appt.doctor?.department?.name ?? "—" }}
              </p>
              <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                  <CalendarDays class="w-3.5 h-3.5 text-gray-400" />
                  {{ formatDate(appt.scheduled_time) }}
                </span>
                <span class="flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-gray-400" />
                  {{ formatTime(appt.scheduled_time) }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3 flex-shrink-0">
            <span :class="statusClass(appt.status)"
              class="text-xs font-semibold px-2.5 py-0.5 rounded-full border capitalize">
              {{ appt.status }}
            </span>
            <a v-if="appt.telehealth_link && isJoinable(appt)"
              :href="appt.telehealth_link"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 px-4 rounded-lg transition shadow-sm">
              <Video class="w-3.5 h-3.5" /> Join Now
            </a>
            <span v-else-if="isJoinable(appt)"
              class="flex items-center gap-1.5 text-xs text-gray-400 font-medium">
              <Clock class="w-3.5 h-3.5" /> Link pending
            </span>
          </div>
        </div>
      </div>

      <!-- Info box -->
      <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 flex items-start gap-3">
        <Info class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-semibold text-blue-800">How telemedicine works</p>
          <p class="text-xs text-blue-600 mt-1 leading-relaxed">
            When your doctor starts the session, a join link will appear here. Make sure your
            camera and microphone are enabled before joining. Sessions typically last 15–30 minutes.
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Video, CalendarDays, Clock, Info } from "lucide-vue-next";
import { useAppointmentStore } from "../../stores/appointmentStore";

const apptStore = useAppointmentStore();

const loading = computed(() => apptStore.loading && !apptStore.appointments.length);

// Only show telehealth appointments
const telehealthAppts = computed(() =>
  apptStore.appointments
    .filter((a) => a.is_telehealth)
    .sort((a, b) => new Date(b.scheduled_time) - new Date(a.scheduled_time))
);

// An appointment is "joinable" if it's confirmed and within 30 min before/after scheduled time
function isJoinable(appt) {
  if (appt.status !== "confirmed") return false;
  const now = Date.now();
  const t = new Date(appt.scheduled_time).getTime();
  return now >= t - 30 * 60 * 1000 && now <= t + 60 * 60 * 1000;
}

onMounted(async () => {
  if (!apptStore.appointments.length) {
    await apptStore.fetchAll();
  }
});

function initials(doctor) {
  const u = doctor?.user;
  return u ? ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase() : "?";
}

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
  });
}

function formatTime(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
}

function statusClass(status) {
  return ({
    pending:   "bg-amber-50 text-amber-700 border-amber-200",
    confirmed: "bg-blue-50 text-blue-700 border-blue-200",
    completed: "bg-emerald-50 text-emerald-700 border-emerald-200",
    cancelled: "bg-red-50 text-red-600 border-red-200",
  }[status] ?? "bg-gray-50 text-gray-500 border-gray-200");
}
</script>
