<template>
  <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
    <div>
      <!-- Header -->
      <div class="flex items-start gap-3 mb-4">
        <div class="w-11 h-11 bg-slate-50 border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
          <Building2 class="w-5 h-5 text-[#004bb5]" />
        </div>
        <div class="min-w-0">
          <h3 class="font-bold text-gray-800 text-sm leading-snug truncate">
            {{ hospital.name }}
          </h3>
          <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 truncate">
            <MapPin class="w-3 h-3 flex-shrink-0 text-gray-400" />
            <span class="truncate">{{ hospital.location }}</span>
          </p>
          <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
            <Phone class="w-3 h-3 flex-shrink-0 text-gray-400" />
            {{ hospital.phone }}
          </p>
        </div>
      </div>

      <!-- Departments -->
      <div class="mb-4">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Departments</p>
        <div class="flex flex-wrap gap-1.5">
          <span
            v-for="dept in hospital.departments"
            :key="dept"
            class="px-2 py-0.5 bg-blue-50 text-[#004bb5] border border-blue-100 rounded text-xs font-medium"
          >
            {{ dept }}
          </span>
          <span
            v-if="hospital.moreDeptsCount > 0"
            class="px-2 py-0.5 bg-gray-50 text-gray-500 border border-gray-200 rounded text-xs font-medium"
          >
            +{{ hospital.moreDeptsCount }} more
          </span>
        </div>
      </div>
    </div>

    <!-- Stats row -->
    <div>
      <div class="grid grid-cols-2 gap-2 mb-4">
        <!-- Total doctors -->
        <div class="bg-slate-50 border border-gray-100 p-2.5 rounded-lg flex items-center gap-2">
          <Users class="w-4 h-4 text-slate-500 flex-shrink-0" />
          <div>
            <p class="text-base font-bold text-slate-800 leading-none">{{ hospital.totalDoctors }}</p>
            <p class="text-[10px] font-medium text-gray-400 uppercase mt-0.5">Total Doctors</p>
          </div>
        </div>
        <!-- Telemed badge -->
        <div
          :class="hospital.telemedAvailable
            ? 'bg-emerald-50 border-emerald-100'
            : 'bg-gray-50 border-gray-100'"
          class="border p-2.5 rounded-lg flex items-center gap-2"
        >
          <Video
            :class="hospital.telemedAvailable ? 'text-emerald-500' : 'text-gray-300'"
            class="w-4 h-4 flex-shrink-0"
          />
          <div>
            <p
              :class="hospital.telemedAvailable ? 'text-emerald-700' : 'text-gray-400'"
              class="text-[10px] font-bold uppercase leading-tight"
            >
              {{ hospital.telemedAvailable ? 'Telemed Available' : 'Telemed Unavailable' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="grid grid-cols-2 gap-2">
        <router-link
          :to="`/patient/hospitals/${hospital.id}`"
          class="w-full py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors text-center"
        >
          View Details
        </router-link>
        <router-link
          to="/patient/appointments"
          class="w-full py-2 bg-[#004bb5] text-white text-xs font-semibold rounded-lg hover:bg-[#003da1] transition-colors text-center"
        >
          Book Appt
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Building2, MapPin, Phone, Users, Video } from "lucide-vue-next";

defineProps({
  hospital: {
    type: Object,
    required: true,
  },
});
</script>
