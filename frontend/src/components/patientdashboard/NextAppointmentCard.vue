<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-full min-h-[200px]">
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 border-b border-gray-50">
      <h3 class="text-sm font-bold text-gray-800">Next Appointment</h3>
      <span
        v-if="appointment"
        :class="statusBadge"
        class="font-bold px-2 py-0.5 rounded-full text-[10px] border"
      >
        ● {{ appointment.status }}
      </span>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="flex-1 flex items-center justify-center py-6">
      <div class="w-5 h-5 border-2 border-[#004795] border-t-transparent rounded-full animate-spin" />
    </div>

    <!-- No upcoming appointment -->
    <div v-else-if="!appointment" class="flex-1 flex flex-col items-center justify-center py-6 text-gray-400">
      <CalendarDays class="w-9 h-9 mb-2 text-gray-200" />
      <p class="text-sm font-medium text-gray-500">No upcoming appointments</p>
      <router-link to="/patient/appointments"
        class="mt-2 text-xs font-semibold text-[#004795] hover:underline">
        Book an appointment
      </router-link>
    </div>

    <!-- Appointment details -->
    <template v-else>
      <div class="flex items-center justify-between gap-x-4 my-4">
        <!-- Doctor info -->
        <div class="flex items-center gap-x-3.5">
          <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-100 bg-[#004795]/10 flex items-center justify-center flex-shrink-0">
            <img
              v-if="appointment.doctor?.profile_picture_url"
              :src="appointment.doctor.profile_picture_url"
              :alt="doctorName"
              class="w-full h-full object-cover"
            />
            <span v-else class="text-sm font-bold text-[#004795]">{{ doctorInitials }}</span>
          </div>
          <div>
            <h4 class="text-sm font-bold text-gray-900">Dr. {{ doctorName }}</h4>
            <p class="text-xs text-gray-500 font-medium mt-0.5">
              {{ appointment.department?.name ?? appointment.doctor?.department?.name ?? "—" }}
            </p>
            <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
              <MapPin class="w-3 h-3" />
              {{ hospitalInfo }}
            </p>
          </div>
        </div>

        <!-- Date/time block -->
        <div class="bg-[#F0F4FA] p-2.5 rounded-xl flex flex-col items-center justify-center text-center min-w-[75px] flex-shrink-0">
          <span class="text-[9px] font-bold text-blue-600 tracking-wider uppercase">
            {{ apptDate }}
          </span>
          <span class="text-base font-extrabold text-[#004795] leading-none mt-1">
            {{ apptTime }}
          </span>
          <span class="text-[9px] font-bold text-gray-400 uppercase mt-0.5">
            {{ apptAmPm }}
          </span>
        </div>
      </div>

      <!-- Action buttons — only pending can be rescheduled -->
      <div class="grid grid-cols-2 gap-3 mt-2">
        <button
          v-if="appointment.status === 'pending'"
          @click="$emit('reschedule', appointment)"
          class="w-full bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg transition shadow-sm"
        >
          Reschedule
        </button>
        <div v-else class="w-full" />
        <router-link
          to="/patient/appointments"
          class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-lg transition text-center"
        >
          View Details
        </router-link>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { CalendarDays, MapPin } from "lucide-vue-next";

const props = defineProps({
  appointment: { type: Object, default: null },
  loading: { type: Boolean, default: false },
});

defineEmits(["reschedule"]);

const doctorName = computed(() => {
  const u = props.appointment?.doctor?.user;
  if (!u) return "—";
  return `${u.first_name ?? ""} ${u.last_name ?? ""}`.trim();
});

const doctorInitials = computed(() => {
  const u = props.appointment?.doctor?.user;
  if (!u) return "?";
  return ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase();
});

const hospitalInfo = computed(() => {
  const a = props.appointment;
  const hospital = a?.doctor?.hospital?.name ?? a?.hospital?.name ?? "—";
  const room = a?.room ?? a?.notes?.match(/Room\s+\w+/i)?.[0] ?? "";
  return room ? `${hospital}, ${room}` : hospital;
});

const apptDate = computed(() => {
  if (!props.appointment?.scheduled_time) return "—";
  return new Date(props.appointment.scheduled_time).toLocaleDateString("en-ET", {
    month: "short",
    day: "numeric",
  });
});

const apptTime = computed(() => {
  if (!props.appointment?.scheduled_time) return "—";
  const d = new Date(props.appointment.scheduled_time);
  const h = d.getHours() % 12 || 12;
  const m = String(d.getMinutes()).padStart(2, "0");
  return `${h}:${m}`;
});

const apptAmPm = computed(() => {
  if (!props.appointment?.scheduled_time) return "";
  return new Date(props.appointment.scheduled_time).getHours() < 12 ? "AM" : "PM";
});

const statusBadge = computed(() => ({
  pending:   "bg-amber-50 text-amber-600 border-amber-200",
  confirmed: "bg-emerald-50 text-emerald-600 border-emerald-100",
  completed: "bg-blue-50 text-blue-600 border-blue-100",
  cancelled: "bg-red-50 text-red-500 border-red-200",
}[props.appointment?.status] ?? "bg-gray-50 text-gray-500 border-gray-200"));
</script>
