<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col h-full">
    <h3 class="text-sm font-bold text-gray-800 mb-4">Updates</h3>

    <div class="flex flex-col gap-y-4 flex-1">
      <!-- Telemedicine entry (only when appointment is telehealth) -->
      <div
        v-if="appointment?.is_telehealth"
        class="flex items-start gap-x-3 pb-3 border-b border-gray-50"
      >
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
          <Video class="w-4 h-4 text-blue-600" />
        </div>
        <div class="flex-1">
          <h4 class="text-xs font-bold text-gray-800">Telemedicine link is ready</h4>
          <p class="text-[11px] text-gray-500 leading-normal mt-0.5">
            Join your virtual consultation with Dr. {{ doctorFirstName }} now.
          </p>
          <router-link
            to="/patient/telemedicine"
            class="text-[11px] font-bold text-blue-600 hover:underline inline-block mt-1"
          >Join Now</router-link>
        </div>
      </div>

      <!-- Reminder for upcoming appointment -->
      <div v-if="appointment" class="flex items-start gap-x-3" :class="{ 'border-b border-gray-50 pb-3': appointment.is_telehealth }">
        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0 mt-0.5">
          <AlarmClock class="w-4 h-4 text-amber-600" />
        </div>
        <div class="flex-1">
          <h4 class="text-xs font-bold text-gray-800">
            {{ isTomorrow ? "Reminder: Tomorrow's appointment" : "Upcoming appointment" }}
          </h4>
          <p class="text-[11px] text-gray-500 leading-normal mt-0.5">
            {{ isTomorrow
              ? "Please arrive 15 minutes early to complete paperwork."
              : `Scheduled on ${apptDateLabel} at ${apptTimeLabel}.`
            }}
          </p>
        </div>
      </div>

      <!-- No updates -->
      <div v-if="!appointment" class="flex-1 flex flex-col items-center justify-center text-gray-400 py-4">
        <Bell class="w-7 h-7 mb-2 text-gray-200" />
        <p class="text-xs font-medium">No updates right now</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Video, AlarmClock, Bell } from "lucide-vue-next";

const props = defineProps({
  appointment: { type: Object, default: null },
});

const doctorFirstName = computed(() => {
  const u = props.appointment?.doctor?.user;
  return u?.first_name ?? "your doctor";
});

const isTomorrow = computed(() => {
  if (!props.appointment?.scheduled_time) return false;
  const apptDay = new Date(props.appointment.scheduled_time).toDateString();
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  return apptDay === tomorrow.toDateString();
});

const apptDateLabel = computed(() => {
  if (!props.appointment?.scheduled_time) return "—";
  return new Date(props.appointment.scheduled_time).toLocaleDateString("en-ET", {
    month: "short",
    day: "numeric",
  });
});

const apptTimeLabel = computed(() => {
  if (!props.appointment?.scheduled_time) return "—";
  return new Date(props.appointment.scheduled_time).toLocaleTimeString("en-ET", {
    hour: "2-digit",
    minute: "2-digit",
  });
});
</script>
