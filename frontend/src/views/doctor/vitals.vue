<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Vitals Records</h1>
          <p class="text-xs text-gray-500 font-medium mt-0.5">
            Patient vital signs recorded during consultations
          </p>
        </div>
      </div>

      <!-- Info note -->
      <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
        <Activity class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" />
        <p class="text-xs text-blue-700 font-medium">
          Vitals are recorded per encounter. Open a consultation from
          <router-link to="/doctor/medicalencounter" class="underline font-bold">Medical Encounters</router-link>
          to record or update vitals for an active consultation.
        </p>
      </div>

      <!-- Error -->
      <div
        v-if="encounterStore.error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ encounterStore.error }}
      </div>

      <!-- Loading skeleton -->
      <div v-if="encounterStore.loading" class="space-y-3">
        <div v-for="n in 4" :key="n" class="h-20 bg-white rounded-xl animate-pulse border border-slate-100" />
      </div>

      <!-- Empty state -->
      <div
        v-else-if="!encounterStore.loading && !vitalsWithEncounters.length"
        class="bg-white border border-slate-200 rounded-xl p-12 text-center"
      >
        <Activity class="w-10 h-10 mx-auto mb-3 text-slate-200" />
        <p class="text-sm font-semibold text-slate-400">No vitals recorded yet</p>
        <p class="text-xs text-slate-300 mt-1">Vitals will appear here once recorded during consultations</p>
      </div>

      <!-- Search bar -->
      <div v-if="vitalsWithEncounters.length" class="flex items-center gap-2">
        <div class="relative flex-1 max-w-sm">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <input
            v-model="search"
            type="text"
            placeholder="Search patient name..."
            class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>
      </div>

      <!-- Vitals cards -->
      <div v-if="!encounterStore.loading" class="space-y-4">
        <div
          v-for="item in filtered"
          :key="item.encounterId"
          class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
        >
          <!-- Patient + date header -->
          <div class="flex items-center justify-between px-5 py-3 bg-slate-50/60 border-b border-slate-100">
            <div>
              <p class="text-sm font-bold text-slate-800">{{ item.patientName }}</p>
              <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                {{ formatDate(item.measuredAt) }}
                <span v-if="item.diagnosis"> · {{ item.diagnosis }}</span>
              </p>
            </div>
          </div>

          <!-- Vitals grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 divide-x divide-y divide-slate-100">
            <div
              v-for="v in item.vitalItems"
              :key="v.label"
              class="flex flex-col items-center justify-center p-3 text-center"
            >
              <p class="text-base font-black text-slate-900">{{ v.value }}</p>
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ v.label }}</p>
              <span v-if="v.flag" :class="v.flagClass" class="mt-1 text-[9px] font-bold px-1 py-0.5 rounded">{{ v.flag }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { AlertCircle, Search, Activity } from "lucide-vue-next";
import { useMedicalEncounterStore } from "../../stores/medicalEncounterStore";

const encounterStore = useMedicalEncounterStore();
const search         = ref("");

onMounted(() => encounterStore.fetchAll());

// Build a flat list of encounters that have vitals
const vitalsWithEncounters = computed(() => {
  return encounterStore.encounters
    .filter((e) => e.vitals)
    .map((enc) => {
      const v = enc.vitals;
      const patientName = enc.patient
        ? `${enc.patient.first_name ?? ""} ${enc.patient.last_name ?? ""}`.trim()
        : "—";

      const bp  = v.blood_pressure
        ? `${v.blood_pressure.systolic}/${v.blood_pressure.diastolic}`
        : null;

      const vitalItems = [
        { label: "BP", value: bp ?? "—", unit: "mmHg", ...flagBP(v.blood_pressure?.systolic, v.blood_pressure?.diastolic) },
        { label: "Pulse",  value: v.pulse_rate ?? "—",      unit: "bpm",    ...flagPulse(v.pulse_rate) },
        { label: "RR",     value: v.respiratory_rate ?? "—", unit: "br/min", flag: null, flagClass: "" },
        { label: "Temp",   value: v.temperature ? `${v.temperature}°C` : "—", unit: null, ...flagTemp(v.temperature) },
        { label: "SpO₂",   value: v.blood_oxygen ? `${v.blood_oxygen}%` : "—", unit: null, ...flagO2(v.blood_oxygen) },
        { label: "BMI",    value: v.bmi ?? "—",             unit: "kg/m²",  ...flagBMI(v.bmi) },
        { label: "Weight", value: v.weight ? `${v.weight}kg` : "—", unit: null, flag: null, flagClass: "" },
        { label: "Height", value: v.height ? `${v.height}cm` : "—", unit: null, flag: null, flagClass: "" },
      ].filter((i) => i.value !== "—");

      return {
        encounterId: enc.id,
        patientName,
        measuredAt: v.measured_at ?? enc.encounter_date,
        diagnosis: enc.diagnosis,
        vitalItems,
      };
    });
});

const filtered = computed(() => {
  if (!search.value.trim()) return vitalsWithEncounters.value;
  const q = search.value.toLowerCase();
  return vitalsWithEncounters.value.filter((i) =>
    i.patientName.toLowerCase().includes(q)
  );
});

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
    hour: "2-digit", minute: "2-digit",
  });
}

// Clinical flag helpers
function flagBP(sys, dia) {
  if (!sys || !dia) return { flag: null, flagClass: "" };
  if (sys >= 180 || dia >= 120) return { flag: "Crisis",      flagClass: "bg-red-100 text-red-700" };
  if (sys >= 140 || dia >= 90)  return { flag: "High",        flagClass: "bg-orange-100 text-orange-700" };
  if (sys < 90  || dia < 60)   return { flag: "Low",         flagClass: "bg-blue-100 text-blue-700" };
  return { flag: null, flagClass: "" };
}
function flagPulse(p) {
  if (!p) return { flag: null, flagClass: "" };
  if (p > 100) return { flag: "Tachy", flagClass: "bg-orange-100 text-orange-700" };
  if (p < 60)  return { flag: "Brady", flagClass: "bg-blue-100 text-blue-700" };
  return { flag: null, flagClass: "" };
}
function flagTemp(t) {
  if (!t) return { flag: null, flagClass: "" };
  if (t > 38.3) return { flag: "Fever", flagClass: "bg-red-100 text-red-700" };
  if (t < 36.0) return { flag: "Low",   flagClass: "bg-blue-100 text-blue-700" };
  return { flag: null, flagClass: "" };
}
function flagO2(o) {
  if (!o) return { flag: null, flagClass: "" };
  if (o < 94) return { flag: "Low", flagClass: "bg-red-100 text-red-700" };
  return { flag: null, flagClass: "" };
}
function flagBMI(b) {
  if (!b) return { flag: null, flagClass: "" };
  if (b >= 30)  return { flag: "Obese",       flagClass: "bg-red-100 text-red-700" };
  if (b >= 25)  return { flag: "Overweight",  flagClass: "bg-orange-100 text-orange-700" };
  if (b < 18.5) return { flag: "Underweight", flagClass: "bg-blue-100 text-blue-700" };
  return { flag: null, flagClass: "" };
}
</script>
