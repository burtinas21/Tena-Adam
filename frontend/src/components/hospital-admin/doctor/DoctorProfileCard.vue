<template>
  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-fit">
    <!-- Cover banner -->
    <div class="h-20 bg-gradient-to-r from-blue-600 to-indigo-600 p-3 flex justify-end items-start">
      <span
        :class="doctor.user?.is_active !== false
          ? 'bg-emerald-500 text-white'
          : 'bg-gray-400 text-white'"
        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
      >
        <span class="w-1.5 h-1.5 rounded-full bg-white/80" />
        {{ doctor.user?.is_active !== false ? 'Active' : 'Inactive' }}
      </span>
    </div>

    <!-- Profile -->
    <div class="px-5 pb-5 flex-1 flex flex-col items-center text-center -mt-10 relative z-10">
      <div class="w-20 h-20 rounded-full border-4 border-white shadow-sm bg-gray-100 flex items-center justify-center overflow-hidden">
        <img
          v-if="doctor.profile_picture_url"
          :src="doctor.profile_picture_url"
          :alt="fullName"
          class="w-full h-full object-cover"
        />
        <span v-else class="text-2xl font-bold text-gray-400">
          {{ initials }}
        </span>
      </div>

      <h2 class="text-base font-bold text-gray-900 mt-3">{{ fullName }}</h2>
      <p class="text-xs font-semibold text-blue-600 mt-0.5">
        {{ doctor.department?.name ?? '—' }}
      </p>

      <div class="grid grid-cols-2 gap-2 w-full mt-4">
        <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-xl text-left">
          <span class="text-[10px] font-medium text-gray-400 block">Experience</span>
          <span class="text-xs font-bold text-gray-800 block mt-0.5">
            {{ doctor.years_experience ?? 0 }} yrs
          </span>
        </div>
        <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-xl text-left">
          <span class="text-[10px] font-medium text-gray-400 block">Fee</span>
          <span class="text-xs font-bold text-gray-800 block mt-0.5">
            ETB {{ Number(doctor.consultation_fee ?? 0).toLocaleString() }}
          </span>
        </div>
      </div>

      <div class="w-full mt-3 text-left bg-gray-50 border border-gray-100 p-2.5 rounded-xl">
        <span class="text-[10px] font-medium text-gray-400 block">License</span>
        <span class="text-xs font-bold text-gray-700 block mt-0.5">{{ doctor.license_number ?? '—' }}</span>
      </div>

      <div
        v-if="doctor.is_telehealth_available"
        class="w-full mt-2 bg-blue-50 border border-blue-100 p-2.5 rounded-xl flex items-center gap-2"
      >
        <span class="text-xs text-blue-600 font-semibold">✓ Telemedicine Available</span>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 w-full mt-4">
        <button
          @click="$emit('edit', doctor)"
          class="flex-1 bg-[#004795] hover:bg-[#003670] text-white text-xs font-semibold py-2 rounded-xl transition"
        >
          Edit Profile
        </button>
        <button
          @click="$emit('delete', doctor)"
          class="px-3 border border-red-200 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition"
        >
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Trash2 } from "lucide-vue-next";

const props = defineProps({
  doctor: { type: Object, required: true },
});
defineEmits(["edit", "delete"]);

const fullName = computed(() =>
  props.doctor.user
    ? `Dr. ${props.doctor.user.first_name} ${props.doctor.user.last_name}`
    : "—"
);

const initials = computed(() => {
  const u = props.doctor.user;
  if (!u) return "?";
  return ((u.first_name?.[0] ?? "") + (u.last_name?.[0] ?? "")).toUpperCase();
});
</script>
