<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Waiting -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between relative overflow-hidden">
      <div>
        <div class="flex items-center justify-between">
          <div class="p-2 bg-rose-50 rounded-xl text-rose-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <span v-if="waiting > 20"
            class="inline-flex items-center gap-1 text-[10px] font-bold bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full border border-rose-100">
            High Volume
          </span>
        </div>
        <p class="text-[11px] font-bold text-gray-400 tracking-wide mt-4 uppercase">Total Waiting Patients</p>
      </div>
      <h3 class="text-3xl font-black mt-2 tracking-tight" :class="waiting > 20 ? 'text-rose-600' : 'text-gray-900'">
        {{ loading ? '—' : waiting }}
      </h3>
    </div>

    <!-- In Consultation -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div>
        <div class="p-2 bg-teal-50 text-teal-600 w-fit rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4.8 16.2A8 8 0 1 1 19.2 7.8"/><path d="M16.5 10.5 12 15l-2.5-2.5"/>
          </svg>
        </div>
        <p class="text-[11px] font-bold text-gray-400 tracking-wide mt-4 uppercase">Patients In Consultation</p>
      </div>
      <h3 class="text-3xl font-black text-gray-900 mt-2 tracking-tight">{{ loading ? '—' : inConsultation }}</h3>
    </div>

    <!-- Completed -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div>
        <div class="p-2 bg-blue-50 text-blue-600 w-fit rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <p class="text-[11px] font-bold text-gray-400 tracking-wide mt-4 uppercase">Completed Today</p>
      </div>
      <h3 class="text-3xl font-black text-gray-900 mt-2 tracking-tight">{{ loading ? '—' : completed }}</h3>
    </div>

    <!-- Avg wait -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
      <div>
        <div class="p-2 bg-indigo-50 text-indigo-600 w-fit rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <p class="text-[11px] font-bold text-gray-400 tracking-wide mt-4 uppercase">Avg. Waiting Time</p>
      </div>
      <h3 class="text-3xl font-black text-gray-900 mt-2 tracking-tight">
        {{ loading ? '—' : avgWait }}
        <span class="text-xs font-medium text-gray-400">min</span>
      </h3>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  entries: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const waiting       = computed(() => props.entries.filter((e) => e.status === "waiting").length);
const inConsultation = computed(() => props.entries.filter((e) => e.status === "in_consultation").length);
const completed     = computed(() => props.entries.filter((e) => e.status === "completed").length);

const avgWait = computed(() => {
  const done = props.entries.filter((e) => e.started_at && e.ended_at);
  if (!done.length) return 0;
  const totalMs = done.reduce((acc, e) => {
    return acc + (new Date(e.ended_at) - new Date(e.started_at));
  }, 0);
  return Math.round(totalMs / done.length / 60000);
});
</script>
