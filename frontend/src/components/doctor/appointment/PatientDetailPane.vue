<template>
  <div
    class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex flex-col justify-between h-full"
  >
    <div>
      <!-- Header Label Info -->
      <div class="flex justify-between items-center mb-5">
        <h3 class="text-sm font-bold text-gray-800">Patient Details</h3>
        <button class="text-gray-400 hover:text-gray-600">
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            />
          </svg>
        </button>
      </div>

      <!-- Center Profile Focus Meta Data -->
      <div
        class="flex flex-col items-center text-center pb-5 border-b border-gray-50 mb-5"
      >
        <div
          class="w-16 h-16 rounded-full overflow-hidden border border-gray-100 shadow-sm bg-slate-50 mb-3 flex items-center justify-center font-bold text-gray-400 text-lg"
        >
          <img
            v-if="patient.avatar"
            :src="patient.avatar"
            :alt="patient.patientName"
            class="w-full h-full object-cover"
          />
          <span v-else>{{ patient.initials }}</span>
        </div>
        <h4 class="font-bold text-gray-800 text-base">
          {{ patient.patientName }}
        </h4>
        <p class="text-xs text-gray-400 mt-1 font-medium">
          {{ patient.age }} yrs • {{ patient.gender }} • ID:
          {{ patient.patientId }}
        </p>
      </div>

      <!-- Segment: Medical History Summary Box -->
      <div class="mb-5">
        <h5
          class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2"
        >
          Medical History
        </h5>
        <div class="space-y-2 text-xs">
          <div
            class="flex items-start gap-2 bg-red-50/40 border border-red-100/50 p-2 rounded-lg text-red-800"
          >
            <svg
              class="w-4 h-4 shrink-0 text-red-500 mt-0.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
            <div>
              <span class="font-bold">Allergies:</span>
              {{ patient.allergies || "None" }}
            </div>
          </div>
          <div
            class="flex items-start gap-2 bg-slate-50 p-2 rounded-lg text-gray-700"
          >
            <svg
              class="w-4 h-4 shrink-0 text-blue-500 mt-0.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
            <div>
              <span class="font-semibold text-gray-800">Medications:</span>
              {{ patient.medications || "None" }}
            </div>
          </div>
        </div>
      </div>

      <!-- Segment: Dynamic Clinician Interactive Notes -->
      <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <h5
            class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"
          >
            Latest Notes
          </h5>
          <button class="text-blue-500 hover:text-blue-600">
            <svg
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
              />
            </svg>
          </button>
        </div>
        <div
          class="bg-blue-50/30 border border-blue-100/50 p-3 rounded-xl text-xs text-gray-600 italic leading-relaxed"
        >
          "{{ patient.latestNotes }}"
        </div>
      </div>
    </div>

    <!-- Base Container Trigger Link Button -->
    <button
      class="w-full py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg transition-colors"
    >
      View Full Profile
    </button>
  </div>
</template>

<script setup>
defineProps({
  patient: {
    type: Object,
    required: true,
  },
});
</script>
