<template>
  <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative flex flex-col justify-between">
    <!-- Favorite -->
    <button
      @click="isFav = !isFav"
      class="absolute top-4 right-4 transition-colors"
      :class="isFav ? 'text-red-400' : 'text-gray-300 hover:text-red-400'"
      aria-label="Favourite"
    >
      <Heart class="w-4 h-4" :fill="isFav ? 'currentColor' : 'none'" />
    </button>

    <div>
      <!-- Top row: avatar + core info -->
      <div class="flex gap-4 items-start mb-4 pr-6">
        <!-- Avatar -->
        <div class="w-16 h-16 rounded-xl bg-blue-50 border border-blue-100 overflow-hidden shrink-0 flex items-center justify-center font-bold text-[#004bb5] text-lg relative">
          <img
            v-if="doctor.avatar && !imgError"
            :src="doctor.avatar"
            :alt="doctor.name"
            class="w-full h-full object-cover"
            @error="imgError = true"
          />
          <span v-else>{{ doctor.initials }}</span>
          <!-- Online dot -->
          <span class="absolute bottom-1 right-1 w-2.5 h-2.5 rounded-full border-2 border-white"
            :class="doctor.isTelemedicine ? 'bg-emerald-400' : 'bg-gray-300'" />
        </div>

        <!-- Info -->
        <div class="min-w-0">
          <h2 class="font-bold text-gray-900 text-base leading-snug">Dr. {{ doctor.name }}</h2>
          <p class="text-xs font-semibold text-[#004bb5] mt-0.5">{{ doctor.specialty }}</p>

          <div class="mt-2 space-y-1">
            <p class="text-xs text-gray-600 flex items-center gap-1.5">
              <Building2 class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
              <span class="truncate">{{ doctor.hospital }}</span>
            </p>
            <p class="text-xs text-gray-600 flex items-center gap-1.5">
              <Briefcase class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
              <span>{{ doctor.experience }} Years Experience</span>
            </p>
            <p class="text-xs text-gray-600 flex items-center gap-1.5">
              <Languages class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
              <span class="truncate">{{ doctor.languages.join(', ') }}</span>
            </p>
          </div>
        </div>
      </div>

      <!-- Divider + badges -->
      <div class="border-t border-gray-100 pt-3 mb-3 space-y-1.5">
        <div class="flex items-center gap-1.5 flex-wrap">
          <span
            v-if="doctor.isTelemedicine"
            class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded"
          >
            <Monitor class="w-3 h-3" /> Telemedicine
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-100 px-2 py-0.5 rounded"
          >
            <UserCheck class="w-3 h-3" /> In-person only
          </span>
          <span v-if="doctor.consultation_fee"
            class="inline-flex items-center gap-1 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded">
            {{ doctor.consultation_fee }} ETB
          </span>
        </div>

        <p class="flex items-center gap-1.5 text-xs text-gray-600">
          <CalendarDays class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
          Next: {{ doctor.nextAvailable }}
        </p>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
      <button
        @click="bookAppointment"
        class="w-full py-2.5 bg-[#004bb5] text-white text-xs font-bold rounded-lg hover:bg-[#003da1] transition-colors leading-tight text-center flex items-center justify-center"
      >
        Book<br>Appointment
      </button>
      <router-link
        :to="`/patient/doctors/${doctor.id}`"
        class="w-full py-2.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center flex items-center justify-center"
      >
        View Profile
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import {
  Heart, Building2, Briefcase, Languages,
  Monitor, UserCheck, CalendarDays,
} from "lucide-vue-next";

const props = defineProps({
  doctor: { type: Object, required: true },
});

const router   = useRouter();
const imgError = ref(false);
const isFav    = ref(false);

function bookAppointment() {
  router.push({
    name:  "appointments",
    query: {
      doctor_id:   props.doctor.id,
      hospital_id: props.doctor.hospitalId ?? "",
    },
  });
}
</script>