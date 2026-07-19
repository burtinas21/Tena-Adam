<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <div class="flex items-center gap-2">
        <Activity class="w-4 h-4 text-blue-600" />
        <h3 class="text-sm font-bold text-slate-900">Vital Signs</h3>
        <span v-if="vital?.measured_at" class="text-[10px] text-slate-400 font-medium">
          — Measured {{ formatTime(vital.measured_at) }}
        </span>
      </div>
      <button
        v-if="!showForm && canEdit"
        @click="showForm = true"
        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#004795] bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition"
      >
        <Plus class="w-3.5 h-3.5" />
        {{ vital ? "Update Vitals" : "Record Vitals" }}
      </button>
    </div>

    <!-- Vitals Display Grid -->
    <div v-if="!showForm" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-0 divide-x divide-y divide-slate-100">
      <div
        v-for="item in vitalItems"
        :key="item.label"
        class="flex flex-col items-center justify-center p-4 text-center"
      >
        <component :is="item.icon" class="w-4 h-4 mb-1.5" :class="item.color" />
        <p class="text-lg font-black text-slate-900">
          {{ item.value || "—" }}
        </p>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
          {{ item.label }}
        </p>
        <p v-if="item.unit && item.value" class="text-[9px] text-slate-400">{{ item.unit }}</p>
        <span
          v-if="item.flag"
          :class="item.flagClass"
          class="mt-1 text-[9px] font-bold px-1.5 py-0.5 rounded"
        >
          {{ item.flag }}
        </span>
      </div>
    </div>

    <div v-if="!vital && !showForm" class="px-6 py-4 text-center text-xs text-slate-400">
      No vitals recorded yet for this encounter.
    </div>

    <!-- Vitals Input Form -->
    <div v-if="showForm" class="p-6 space-y-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <!-- Blood Pressure -->
        <div class="space-y-1 col-span-2 sm:col-span-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
            Blood Pressure (mmHg)
          </label>
          <div class="flex gap-2">
            <input
              v-model.number="form.blood_pressure_systolic"
              type="number" min="40" max="300" placeholder="Sys"
              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
            />
            <span class="flex items-center text-slate-400 font-bold">/</span>
            <input
              v-model.number="form.blood_pressure_diastolic"
              type="number" min="20" max="200" placeholder="Dia"
              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
            />
          </div>
        </div>

        <!-- Pulse Rate -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Pulse (bpm)</label>
          <input
            v-model.number="form.pulse_rate"
            type="number" min="20" max="250" placeholder="72"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>

        <!-- Respiratory Rate -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Resp. Rate (br/min)</label>
          <input
            v-model.number="form.respiratory_rate"
            type="number" min="5" max="80" placeholder="16"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>

        <!-- Temperature -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Temperature (°C)</label>
          <input
            v-model.number="form.temperature"
            type="number" step="0.1" min="30" max="45" placeholder="37.0"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>

        <!-- Weight -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Weight (kg)</label>
          <input
            v-model.number="form.weight"
            type="number" step="0.1" min="1" max="500" placeholder="70.0"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>

        <!-- Height -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Height (cm)</label>
          <input
            v-model.number="form.height"
            type="number" step="0.1" min="30" max="300" placeholder="170"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>

        <!-- Blood Oxygen -->
        <div class="space-y-1">
          <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">SpO₂ (%)</label>
          <input
            v-model.number="form.blood_oxygen"
            type="number" min="0" max="100" placeholder="98"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#004795]"
          />
        </div>
      </div>

      <!-- Error -->
      <div v-if="store.error" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ store.error }}
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3 pt-2">
        <button
          @click="saveVitals"
          :disabled="store.saving"
          class="flex items-center gap-2 px-5 py-2 bg-[#004795] text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        >
          <Save class="w-3.5 h-3.5" />
          {{ store.saving ? "Saving…" : "Save Vitals" }}
        </button>
        <button
          @click="showForm = false"
          class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import {
  Activity, Heart, Wind, Thermometer, Weight,
  Droplet, Plus, Save, AlertCircle,
} from "lucide-vue-next";
import { useVitalStore } from "../../../stores/vitalStore";

const props = defineProps({
  vital: { type: Object, default: null },
  encounterId: { type: String, required: true },
  patientId: { type: String, required: true },
  canEdit: { type: Boolean, default: true },
});

const emit = defineEmits(["saved"]);
const store = useVitalStore();
const showForm = ref(false);

const form = ref({
  blood_pressure_systolic: null,
  blood_pressure_diastolic: null,
  pulse_rate: null,
  respiratory_rate: null,
  temperature: null,
  weight: null,
  height: null,
  blood_oxygen: null,
});

// Pre-fill form when editing existing vitals
watch(
  () => props.vital,
  (v) => {
    if (v) {
      form.value = {
        blood_pressure_systolic: v.blood_pressure?.systolic ?? null,
        blood_pressure_diastolic: v.blood_pressure?.diastolic ?? null,
        pulse_rate: v.pulse_rate ?? null,
        respiratory_rate: v.respiratory_rate ?? null,
        temperature: v.temperature ?? null,
        weight: v.weight ?? null,
        height: v.height ?? null,
        blood_oxygen: v.blood_oxygen ?? null,
      };
    }
  },
  { immediate: true }
);

const vitalItems = computed(() => {
  const v = props.vital;
  if (!v) return [];

  const bp = v.blood_pressure ? `${v.blood_pressure.systolic}/${v.blood_pressure.diastolic}` : null;
  const bpFlag = flagBP(v.blood_pressure?.systolic, v.blood_pressure?.diastolic);

  return [
    {
      label: "Blood Pressure",
      value: bp,
      unit: "mmHg",
      icon: Heart,
      color: "text-rose-500",
      flag: bpFlag?.label,
      flagClass: bpFlag?.cls,
    },
    {
      label: "Pulse Rate",
      value: v.pulse_rate,
      unit: "bpm",
      icon: Activity,
      color: "text-pink-500",
      flag: flagPulse(v.pulse_rate)?.label,
      flagClass: flagPulse(v.pulse_rate)?.cls,
    },
    {
      label: "Resp. Rate",
      value: v.respiratory_rate,
      unit: "br/min",
      icon: Wind,
      color: "text-sky-500",
      flag: null,
    },
    {
      label: "Temperature",
      value: v.temperature ? `${v.temperature}°C` : null,
      unit: null,
      icon: Thermometer,
      color: "text-amber-500",
      flag: flagTemp(v.temperature)?.label,
      flagClass: flagTemp(v.temperature)?.cls,
    },
    {
      label: "SpO₂",
      value: v.blood_oxygen ? `${v.blood_oxygen}%` : null,
      unit: null,
      icon: Droplet,
      color: "text-blue-500",
      flag: flagO2(v.blood_oxygen)?.label,
      flagClass: flagO2(v.blood_oxygen)?.cls,
    },
    {
      label: "BMI",
      value: v.bmi,
      unit: "kg/m²",
      icon: Weight,
      color: "text-violet-500",
      flag: flagBMI(v.bmi)?.label,
      flagClass: flagBMI(v.bmi)?.cls,
    },
    {
      label: "Weight",
      value: v.weight ? `${v.weight} kg` : null,
      unit: null,
      icon: Weight,
      color: "text-slate-400",
      flag: null,
    },
    {
      label: "Height",
      value: v.height ? `${v.height} cm` : null,
      unit: null,
      icon: Activity,
      color: "text-slate-400",
      flag: null,
    },
  ].filter((i) => i.value);
});

async function saveVitals() {
  store.clearError();
  try {
    const payload = {
      encounter_id: props.encounterId,
      patient_id: props.patientId,
      ...form.value,
    };

    let result;
    if (props.vital?.id) {
      result = await store.update(props.vital.id, payload);
    } else {
      result = await store.create(payload);
    }

    showForm.value = false;
    emit("saved", result);
  } catch {
    // error shown via store.error
  }
}

function formatTime(dt) {
  if (!dt) return "";
  return new Date(dt).toLocaleString("en-ET", {
    day: "numeric", month: "short", hour: "2-digit", minute: "2-digit",
  });
}

// Clinical flag helpers
function flagBP(sys, dia) {
  if (!sys || !dia) return null;
  if (sys >= 180 || dia >= 120) return { label: "Crisis", cls: "bg-red-100 text-red-700" };
  if (sys >= 140 || dia >= 90)  return { label: "High", cls: "bg-orange-100 text-orange-700" };
  if (sys < 90  || dia < 60)   return { label: "Low", cls: "bg-blue-100 text-blue-700" };
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
  if (t < 36.0) return { label: "Low", cls: "bg-blue-100 text-blue-700" };
  return null;
}
function flagO2(o) {
  if (!o) return null;
  if (o < 94) return { label: "Low", cls: "bg-red-100 text-red-700" };
  return null;
}
function flagBMI(b) {
  if (!b) return null;
  if (b >= 30)   return { label: "Obese", cls: "bg-red-100 text-red-700" };
  if (b >= 25)   return { label: "Overweight", cls: "bg-orange-100 text-orange-700" };
  if (b < 18.5)  return { label: "Underweight", cls: "bg-blue-100 text-blue-700" };
  return null;
}
</script>
