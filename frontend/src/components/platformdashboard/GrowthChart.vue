<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-base font-bold text-gray-800">Hospital &amp; Patient Growth</h2>
      <div class="flex items-center gap-x-4">
        <div class="flex items-center gap-x-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-[#6594C0]"></span>
          <span class="text-xs text-gray-500 font-medium">Hospitals</span>
        </div>
        <div class="flex items-center gap-x-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-[#B2D6D4]"></span>
          <span class="text-xs text-gray-500 font-medium">Patients</span>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="h-48 flex items-end gap-2 px-2">
      <div v-for="i in 6" :key="i" class="flex-1 rounded-t animate-pulse bg-gray-100" :style="{ height: (30 + i * 12) + '%' }"></div>
    </div>

    <!-- No data -->
    <div v-else-if="!displayLabels.length" class="h-48 flex items-center justify-center">
      <span class="text-xs text-gray-400 font-medium">No trend data available yet.</span>
    </div>

    <!-- Stacked bar chart -->
    <div v-else>
      <div class="flex items-end justify-between gap-1.5 h-48 pb-2 border-b border-gray-100">
        <div
          v-for="(label, i) in displayLabels"
          :key="label"
          class="flex flex-col items-center justify-end flex-1 min-w-0 h-full gap-y-0.5"
        >
          <!-- patient segment (top) -->
          <div
            v-if="displayPatients[i] > 0"
            class="w-full max-w-[36px] bg-[#B2D6D4] rounded-t-sm transition-all duration-700"
            :style="{ height: barPct(displayPatients[i]) + '%' }"
          ></div>
          <!-- hospital segment (bottom) -->
          <div
            v-if="displayHospitals[i] > 0"
            class="w-full max-w-[36px] bg-[#6594C0] transition-all duration-700"
            :class="{ 'rounded-t-sm': displayPatients[i] === 0 }"
            :style="{ height: barPct(displayHospitals[i]) + '%' }"
          ></div>
          <!-- zero marker -->
          <div v-if="!displayPatients[i] && !displayHospitals[i]" class="w-full max-w-[36px] h-1 bg-gray-200 rounded-sm"></div>
        </div>
      </div>

      <!-- Y-axis labels -->
      <div class="flex justify-between mt-2 px-0.5">
        <span
          v-for="label in displayLabels"
          :key="label"
          class="flex-1 text-center text-[10px] text-gray-400 font-medium"
        >{{ label }}</span>
      </div>
    </div>

    <!-- Appointments bar chart section -->
    <div class="mt-6 pt-5 border-t border-gray-100">
      <h3 class="text-sm font-bold text-gray-800 mb-4">Platform Activity (Appointments)</h3>

      <div v-if="loading" class="h-32 flex items-end gap-2 px-2">
        <div v-for="i in 6" :key="i" class="flex-1 rounded-t animate-pulse bg-gray-100" :style="{ height: (20 + i * 10) + '%' }"></div>
      </div>

      <div v-else-if="!displayLabels.length" class="h-32 flex items-center justify-center">
        <span class="text-xs text-gray-400 font-medium">No appointment data yet.</span>
      </div>

      <div v-else>
        <div class="flex items-end justify-between gap-1.5 h-32 pb-2 border-b border-gray-100">
          <div
            v-for="(label, i) in displayLabels"
            :key="label"
            class="flex flex-col items-center justify-end flex-1 min-w-0 h-full"
          >
            <div
              class="w-full max-w-[36px] bg-[#1E4DB4] rounded-t-sm transition-all duration-700"
              :style="{ height: apptBarPct(displayAppointments[i]) + '%' }"
            ></div>
          </div>
        </div>
        <div class="flex justify-between mt-2 px-0.5">
          <span
            v-for="label in displayLabels"
            :key="label"
            class="flex-1 text-center text-[10px] text-gray-400 font-medium"
          >{{ label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  labels:           { type: Array, default: () => [] },
  hospitalData:     { type: Array, default: () => [] },
  patientData:      { type: Array, default: () => [] },
  appointmentData:  { type: Array, default: () => [] },
  loading:          { type: Boolean, default: false },
});

const displayLabels       = computed(() => props.labels.slice(-6));
const displayHospitals    = computed(() => props.hospitalData.slice(-6));
const displayPatients     = computed(() => props.patientData.slice(-6));
const displayAppointments = computed(() => props.appointmentData.slice(-6));

// max combined value across all months (for scaling)
const maxGrowth = computed(() => {
  const vals = displayLabels.value.map((_, i) =>
    (displayHospitals.value[i] || 0) + (displayPatients.value[i] || 0)
  );
  return Math.max(1, ...vals);
});

const maxAppts = computed(() =>
  Math.max(1, ...displayAppointments.value.map((v) => v || 0))
);

function barPct(value) {
  return Math.max(Math.round((value / maxGrowth.value) * 90), value > 0 ? 3 : 0);
}

function apptBarPct(value) {
  return Math.max(Math.round(((value || 0) / maxAppts.value) * 90), value > 0 ? 3 : 0);
}
</script>
