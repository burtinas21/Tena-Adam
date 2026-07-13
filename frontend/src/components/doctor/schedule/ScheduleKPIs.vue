<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Working days -->
    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div class="flex items-start justify-between">
        <div>
          <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Working Days</span>
          <h3 class="text-3xl font-black text-gray-900 mt-2 tracking-tight">{{ workingDays }}</h3>
        </div>
        <div class="text-blue-500 bg-blue-50/50 p-2 rounded-xl">
          <Calendar class="w-5 h-5" />
        </div>
      </div>
      <p class="text-xs text-gray-400 font-medium mt-4">
        days per week scheduled
      </p>
    </div>

    <!-- Total available hours -->
    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div class="flex items-start justify-between">
        <div>
          <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Available Hours/Week</span>
          <h3 class="text-3xl font-black text-gray-900 mt-2 tracking-tight">{{ totalHours }}h</h3>
        </div>
        <div class="text-emerald-500 bg-emerald-50/50 p-2 rounded-xl">
          <Clock class="w-5 h-5" />
        </div>
      </div>
      <p class="text-xs text-gray-400 font-medium mt-4">
        {{ slotCount }} slots of {{ slotDuration }}m each
      </p>
    </div>

    <!-- Telemedicine -->
    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div class="flex items-start justify-between">
        <div>
          <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Telemedicine</span>
          <div class="flex items-baseline gap-2 mt-2">
            <h3 class="text-3xl font-black text-gray-900 tracking-tight">
              {{ telehealthEnabled ? 'On' : 'Off' }}
            </h3>
            <span
              :class="telehealthEnabled ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-200'"
              class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase border"
            >
              {{ telehealthEnabled ? 'Available' : 'Disabled' }}
            </span>
          </div>
        </div>
        <div class="text-indigo-500 bg-indigo-50/50 p-2 rounded-xl">
          <Video class="w-5 h-5" />
        </div>
      </div>
      <div class="w-full bg-slate-100 h-1.5 rounded-full mt-5 overflow-hidden">
        <div :class="telehealthEnabled ? 'bg-blue-600 w-full' : 'w-0'" class="h-full rounded-full transition-all" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Calendar, Clock, Video } from "lucide-vue-next";

const props = defineProps({
  schedules:         { type: Array,   default: () => [] },
  telehealthEnabled: { type: Boolean, default: false },
});

const workingDays = computed(() => props.schedules.length);

const totalHours = computed(() => {
  let total = 0;
  for (const s of props.schedules) {
    if (!s.is_available) continue;
    const [sh, sm] = s.start_time.split(":").map(Number);
    const [eh, em] = s.end_time.split(":").map(Number);
    let mins = (eh * 60 + em) - (sh * 60 + sm);
    // Subtract lunch
    if (s.lunch_start && s.lunch_end) {
      const [lsh, lsm] = s.lunch_start.split(":").map(Number);
      const [leh, lem] = s.lunch_end.split(":").map(Number);
      mins -= (leh * 60 + lem) - (lsh * 60 + lsm);
    }
    total += Math.max(0, mins);
  }
  return Math.round((total / 60) * 10) / 10;
});

const slotDuration = computed(() =>
  props.schedules[0]?.slot_duration_min ?? 30
);

const slotCount = computed(() => {
  if (!totalHours.value || !slotDuration.value) return 0;
  return Math.floor((totalHours.value * 60) / slotDuration.value);
});
</script>
