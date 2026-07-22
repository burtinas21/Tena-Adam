<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 overflow-y-auto font-sans dark:text-slate-200">
    <div class="max-w-[1280px] mx-auto space-y-6">

      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200/60">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Medical History</h1>
          <p class="text-xs text-slate-500 font-medium mt-0.5">
            Your complete consultation and prescription records
          </p>
        </div>
      </div>

      <!-- KPI strip -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div
          v-for="stat in kpis"
          :key="stat.label"
          class="bg-white rounded-xl border border-slate-100 shadow-sm p-4"
        >
          <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2" :class="stat.bg">
            <component :is="stat.icon" class="w-4 h-4" :class="stat.color" />
          </div>
          <p class="text-xl font-black text-slate-900">{{ stat.value }}</p>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="encounterStore.error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ encounterStore.error }}
      </div>

      <!-- Loading -->
      <div v-if="encounterStore.loading" class="space-y-3">
        <div v-for="n in 4" :key="n" class="h-20 bg-white rounded-xl animate-pulse border border-slate-100" />
      </div>

      <!-- Empty state -->
      <div
        v-else-if="!encounterStore.loading && !encounterStore.encounters.length"
        class="bg-white border border-slate-200 rounded-xl p-12 text-center"
      >
        <ClipboardList class="w-10 h-10 mx-auto mb-3 text-slate-200" />
        <p class="text-sm font-semibold text-slate-400">No consultations recorded yet</p>
        <p class="text-xs text-slate-300 mt-1">Your medical history will appear here after your first consultation</p>
      </div>

      <!-- Two-column layout: list + detail -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Encounter list -->
        <div class="lg:col-span-4 space-y-3">
          <div class="flex items-center gap-2">
            <div class="relative flex-1">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
              <input
                v-model="search"
                type="text"
                placeholder="Search diagnosis..."
                class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-[#004795]"
              />
            </div>
            <select
              v-model="statusFilter"
              class="border border-slate-200 rounded-lg px-3 py-2 text-xs bg-white focus:outline-none focus:border-[#004795]"
            >
              <option value="">All</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div
            v-for="enc in filteredEncounters"
            :key="enc.id"
            @click="selectEncounter(enc)"
            class="bg-white border rounded-xl p-4 cursor-pointer transition hover:shadow-md"
            :class="selectedEncounter?.id === enc.id
              ? 'border-[#004795] ring-1 ring-[#004795]/20'
              : 'border-slate-200 hover:border-slate-300'"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <!-- Hospital & Doctor -->
                <p class="text-[10px] font-semibold text-slate-400 mb-0.5">
                  {{ enc.hospital?.name ?? '—' }}
                  <span v-if="enc.doctor?.first_name"> · Dr. {{ enc.doctor.first_name }} {{ enc.doctor.last_name }}</span>
                </p>
                <p class="text-sm font-bold text-slate-800 truncate">
                  {{ enc.diagnosis || enc.chief_complaint || 'Consultation' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                  {{ formatDate(enc.encounter_date) }}
                </p>
              </div>
              <span
                :class="statusClass(enc.status)"
                class="flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded border capitalize"
              >
                {{ enc.status?.replace('_', ' ') }}
              </span>
            </div>
            <div v-if="enc.diagnosis_icd10" class="mt-1.5">
              <span class="text-[9px] font-mono font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                ICD-10: {{ enc.diagnosis_icd10 }}
              </span>
            </div>
          </div>
        </div>

        <!-- Detail panel -->
        <div class="lg:col-span-8 space-y-4">

          <!-- No selection -->
          <div
            v-if="!selectedEncounter"
            class="bg-white border border-slate-200 rounded-xl p-12 text-center"
          >
            <FileText class="w-10 h-10 mx-auto mb-3 text-slate-200" />
            <p class="text-sm font-semibold text-slate-400">Select a consultation to view details</p>
          </div>

          <!-- Encounter detail -->
          <template v-if="selectedEncounter">

            <!-- Header card -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
              <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                  <div class="flex items-center gap-2 flex-wrap">
                    <span
                      :class="statusClass(selectedEncounter.status)"
                      class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider"
                    >
                      {{ selectedEncounter.status?.replace('_', ' ') }}
                    </span>
                    <span v-if="selectedEncounter.diagnosis_icd10" class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                      ICD-10: {{ selectedEncounter.diagnosis_icd10 }}
                    </span>
                  </div>
                  <h2 class="text-lg font-bold text-slate-900 mt-2">
                    {{ selectedEncounter.diagnosis || 'Diagnosis Pending' }}
                  </h2>
                  <p class="text-xs text-slate-500 mt-0.5">
                    {{ formatDate(selectedEncounter.encounter_date) }}
                    <span v-if="selectedEncounter.hospital?.name"> · {{ selectedEncounter.hospital.name }}</span>
                    <span v-if="selectedEncounter.doctor?.first_name"> · Dr. {{ selectedEncounter.doctor.first_name }} {{ selectedEncounter.doctor.last_name }}</span>
                  </p>
                </div>
                <div v-if="selectedEncounter.follow_up_date" class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                  <Calendar class="w-3.5 h-3.5" />
                  Follow-up: {{ selectedEncounter.follow_up_date }}
                </div>
              </div>
            </div>

            <!-- Clinical fields grid -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm divide-y divide-slate-100">
              <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                <div class="flex items-center gap-2">
                  <FileText class="w-4 h-4 text-blue-600" />
                  <h3 class="text-sm font-bold text-slate-900">Consultation Notes</h3>
                </div>
              </div>
              <ClinicalField label="Chief Complaint"    :value="selectedEncounter.chief_complaint" />
              <ClinicalField label="History"            :value="selectedEncounter.history" />
              <ClinicalField label="Physical Exam"      :value="selectedEncounter.physical_exam" />
              <ClinicalField label="Assessment"         :value="selectedEncounter.assessment" />
              <ClinicalField label="Treatment Plan"     :value="selectedEncounter.treatment_plan" />
              <ClinicalField label="Clinical Notes"     :value="selectedEncounter.clinical_notes" />
              <div v-if="!hasAnyField" class="px-5 py-8 text-center text-xs text-slate-400">
                Clinical notes are being documented.
              </div>
            </div>

            <!-- Vitals -->
            <div v-if="selectedEncounter.vitals" class="bg-white border border-slate-200 rounded-xl shadow-sm">
              <div class="flex items-center gap-2 px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                <Activity class="w-4 h-4 text-blue-600" />
                <h3 class="text-sm font-bold text-slate-900">Vital Signs</h3>
                <span v-if="selectedEncounter.vitals?.measured_at" class="text-[10px] text-slate-400">
                  — {{ formatDate(selectedEncounter.vitals.measured_at) }}
                </span>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-0 divide-x divide-y divide-slate-100">
                <VitalItem
                  v-for="v in vitalDisplay"
                  :key="v.label"
                  :label="v.label"
                  :value="v.value"
                  :unit="v.unit"
                  :flag="v.flag"
                  :flag-class="v.flagClass"
                />
              </div>
            </div>

            <!-- Prescriptions -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
              <div class="flex items-center gap-2 px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                <Pill class="w-4 h-4 text-violet-600" />
                <h3 class="text-sm font-bold text-slate-900">Prescriptions</h3>
                <span class="text-[10px] text-slate-400">({{ encounterPrescriptions.length }})</span>
              </div>

              <div v-if="prescriptionStore.loading" class="p-5 space-y-2">
                <div v-for="n in 2" :key="n" class="h-12 bg-slate-50 rounded-lg animate-pulse" />
              </div>
              <div v-else-if="!encounterPrescriptions.length" class="px-5 py-8 text-center">
                <Pill class="w-7 h-7 mx-auto mb-2 text-slate-200" />
                <p class="text-xs text-slate-400">No prescriptions for this consultation</p>
              </div>
              <div v-else class="divide-y divide-slate-100">
                <div
                  v-for="rx in encounterPrescriptions"
                  :key="rx.id"
                  class="px-5 py-4 flex items-start gap-4"
                >
                  <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <Pill class="w-4 h-4 text-violet-500" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <p class="text-sm font-bold text-slate-800">{{ rx.medication_name }}</p>
                      <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ rx.dosage }}</span>
                      <span :class="rxStatusClass(rx.status)" class="text-[9px] font-bold px-1.5 py-0.5 rounded border capitalize">
                        {{ rx.status }}
                      </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                      {{ rx.frequency }}
                      <span v-if="rx.route"> · {{ rx.route }}</span>
                      <span v-if="rx.duration_days"> · {{ rx.duration_days }} days</span>
                    </p>
                    <p v-if="rx.instructions" class="text-[11px] text-slate-400 mt-1 italic">
                      {{ rx.instructions }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  AlertCircle, Search, ClipboardList, FileText,
  Activity, Pill, Calendar, Heart, Wind, Thermometer,
  Droplet, Weight,
} from "lucide-vue-next";
import { useMedicalEncounterStore } from "../../stores/medicalEncounterStore";
import { usePrescriptionStore }     from "../../stores/prescriptionStore";

const encounterStore    = useMedicalEncounterStore();
const prescriptionStore = usePrescriptionStore();

const search             = ref("");
const statusFilter       = ref("");
const selectedEncounter  = ref(null);

// ── Inline helper components ──────────────────────────────────────────────────

const ClinicalField = {
  props: { label: String, value: String },
  template: `
    <div v-if="value" class="px-5 py-3">
      <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1">{{ label }}</p>
      <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-wrap">{{ value }}</p>
    </div>
  `,
};

const VitalItem = {
  props: { label: String, value: [String, Number], unit: String, flag: String, flagClass: String },
  template: `
    <div class="flex flex-col items-center justify-center p-4 text-center">
      <p class="text-lg font-black text-slate-900">{{ value || '—' }}</p>
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ label }}</p>
      <p v-if="unit && value" class="text-[9px] text-slate-400">{{ unit }}</p>
      <span v-if="flag" :class="flagClass" class="mt-1 text-[9px] font-bold px-1.5 py-0.5 rounded">{{ flag }}</span>
    </div>
  `,
};

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(async () => {
  await encounterStore.fetchAll();
});

// ── Computed ──────────────────────────────────────────────────────────────────

const filteredEncounters = computed(() => {
  let list = encounterStore.encounters;
  if (statusFilter.value) list = list.filter((e) => e.status === statusFilter.value);
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(
      (e) =>
        (e.diagnosis ?? "").toLowerCase().includes(q) ||
        (e.chief_complaint ?? "").toLowerCase().includes(q)
    );
  }
  return list;
});

const kpis = computed(() => [
  {
    label: "Total Visits",
    value: encounterStore.encounters.length,
    icon: ClipboardList,
    bg: "bg-blue-50",
    color: "text-blue-500",
  },
  {
    label: "Completed",
    value: encounterStore.encounters.filter((e) => e.status === "completed").length,
    icon: FileText,
    bg: "bg-emerald-50",
    color: "text-emerald-500",
  },
  {
    label: "In Progress",
    value: encounterStore.encounters.filter((e) => e.status === "in_progress").length,
    icon: Activity,
    bg: "bg-amber-50",
    color: "text-amber-500",
  },
  {
    label: "Prescriptions",
    value: prescriptionStore.prescriptions.length,
    icon: Pill,
    bg: "bg-violet-50",
    color: "text-violet-500",
  },
]);

const encounterPrescriptions = computed(() => prescriptionStore.prescriptions);

const hasAnyField = computed(() => {
  const e = selectedEncounter.value;
  if (!e) return false;
  return !!(e.chief_complaint || e.history || e.physical_exam || e.assessment || e.treatment_plan || e.clinical_notes);
});

const vitalDisplay = computed(() => {
  const v = selectedEncounter.value?.vitals;
  if (!v) return [];

  const bp  = v.blood_pressure ? `${v.blood_pressure.systolic}/${v.blood_pressure.diastolic}` : null;
  const bpF = flagBP(v.blood_pressure?.systolic, v.blood_pressure?.diastolic);

  return [
    { label: "Blood Pressure", value: bp, unit: "mmHg", flag: bpF?.label, flagClass: bpF?.cls },
    { label: "Pulse Rate",     value: v.pulse_rate,      unit: "bpm",      flag: flagPulse(v.pulse_rate)?.label, flagClass: flagPulse(v.pulse_rate)?.cls },
    { label: "Resp. Rate",     value: v.respiratory_rate, unit: "br/min",  flag: null },
    { label: "Temperature",    value: v.temperature ? `${v.temperature}°C` : null, unit: null, flag: flagTemp(v.temperature)?.label, flagClass: flagTemp(v.temperature)?.cls },
    { label: "SpO₂",           value: v.blood_oxygen ? `${v.blood_oxygen}%` : null, unit: null, flag: flagO2(v.blood_oxygen)?.label, flagClass: flagO2(v.blood_oxygen)?.cls },
    { label: "BMI",            value: v.bmi,             unit: "kg/m²",    flag: flagBMI(v.bmi)?.label, flagClass: flagBMI(v.bmi)?.cls },
    { label: "Weight",         value: v.weight ? `${v.weight} kg` : null, unit: null, flag: null },
    { label: "Height",         value: v.height ? `${v.height} cm` : null, unit: null, flag: null },
  ].filter((i) => i.value);
});

// ── Actions ───────────────────────────────────────────────────────────────────

async function selectEncounter(enc) {
  selectedEncounter.value = enc;
  encounterStore.clearError();
  prescriptionStore.clearError();

  // Load full encounter with vitals
  const full = await encounterStore.fetchById(enc.id);
  selectedEncounter.value = full;

  // Load prescriptions for this encounter
  await prescriptionStore.fetchByEncounter(enc.id);
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function formatDate(dt) {
  if (!dt) return "—";
  return new Date(dt).toLocaleDateString("en-ET", {
    day: "numeric", month: "short", year: "numeric",
  });
}

function statusClass(status) {
  return {
    in_progress: "bg-blue-50 text-blue-700 border-blue-100",
    completed:   "bg-emerald-50 text-emerald-700 border-emerald-100",
    cancelled:   "bg-red-50 text-red-500 border-red-100",
  }[status] ?? "bg-slate-50 text-slate-500 border-slate-100";
}

function rxStatusClass(status) {
  return {
    active:    "bg-blue-50 text-blue-700 border-blue-100",
    completed: "bg-emerald-50 text-emerald-700 border-emerald-100",
    cancelled: "bg-red-50 text-red-500 border-red-100",
  }[status] ?? "bg-slate-50 text-slate-500 border-slate-100";
}

function flagBP(sys, dia) {
  if (!sys || !dia) return null;
  if (sys >= 180 || dia >= 120) return { label: "Crisis",      cls: "bg-red-100 text-red-700" };
  if (sys >= 140 || dia >= 90)  return { label: "High",        cls: "bg-orange-100 text-orange-700" };
  if (sys < 90  || dia < 60)   return { label: "Low",         cls: "bg-blue-100 text-blue-700" };
  return null;
}
function flagPulse(p) {
  if (!p) return null;
  if (p > 100) return { label: "Tachy", cls: "bg-orange-100 text-orange-700" };
  if (p < 60)  return { label: "Brady", cls: "bg-blue-100 text-blue-700" };
  return null;
}
function flagTemp(t) {
  if (!t) return null;
  if (t > 38.3) return { label: "Fever", cls: "bg-red-100 text-red-700" };
  if (t < 36.0) return { label: "Low",   cls: "bg-blue-100 text-blue-700" };
  return null;
}
function flagO2(o) {
  if (!o) return null;
  if (o < 94) return { label: "Low", cls: "bg-red-100 text-red-700" };
  return null;
}
function flagBMI(b) {
  if (!b) return null;
  if (b >= 30)  return { label: "Obese",       cls: "bg-red-100 text-red-700" };
  if (b >= 25)  return { label: "Overweight",  cls: "bg-orange-100 text-orange-700" };
  if (b < 18.5) return { label: "Underweight", cls: "bg-blue-100 text-blue-700" };
  return null;
}
</script>

<style scoped>
.custom-scroll-area::-webkit-scrollbar { width: 5px; }
.custom-scroll-area::-webkit-scrollbar-track { background: transparent; }
.custom-scroll-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
.custom-scroll-area::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
