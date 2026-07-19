<template>
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-full overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 bg-slate-50/50 border-b border-slate-100">
      <div class="flex items-center gap-2">
        <ClipboardList class="w-4 h-4 text-indigo-600" />
        <h3 class="text-sm font-bold text-slate-900">Medical History</h3>
      </div>
      <span class="text-[10px] font-semibold text-slate-400">
        {{ encounters.length }} encounter{{ encounters.length !== 1 ? "s" : "" }}
      </span>
    </div>

    <!-- Patient quick summary -->
    <div v-if="patient" class="px-5 py-3 border-b border-slate-100 space-y-2">
      <!-- Allergies -->
      <div v-if="patient.allergies" class="flex items-start gap-2">
        <AlertTriangle class="w-3.5 h-3.5 text-rose-500 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Allergies</p>
          <p class="text-xs text-slate-700 mt-0.5">{{ patient.allergies }}</p>
        </div>
      </div>
      <!-- Medical History -->
      <div v-if="patient.medical_history" class="flex items-start gap-2">
        <History class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Past History</p>
          <p class="text-xs text-slate-600 mt-0.5 line-clamp-3">{{ patient.medical_history }}</p>
        </div>
      </div>
    </div>

    <!-- Timeline -->
    <div class="flex-1 overflow-y-auto custom-scroll divide-y divide-slate-100">
      <!-- Empty state -->
      <div v-if="!encounters.length" class="flex flex-col items-center justify-center py-12 text-center px-6">
        <ClipboardList class="w-8 h-8 text-slate-200 mb-2" />
        <p class="text-xs text-slate-400 font-medium">No past encounters recorded</p>
      </div>

      <!-- Encounter items -->
      <div
        v-for="enc in encounters"
        :key="enc.id"
        @click="$emit('select', enc)"
        class="px-5 py-4 hover:bg-slate-50/70 transition cursor-pointer"
        :class="{ 'bg-blue-50/50': selectedId === enc.id }"
      >
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-slate-800 truncate">
              {{ enc.diagnosis || "Diagnosis pending" }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5">
              {{ formatDate(enc.encounter_date) }}
            </p>
            <p v-if="enc.chief_complaint" class="text-[11px] text-slate-600 mt-1 line-clamp-2">
              {{ enc.chief_complaint }}
            </p>
          </div>
          <span
            :class="statusClass(enc.status)"
            class="flex-shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded border capitalize"
          >
            {{ enc.status?.replace("_", " ") }}
          </span>
        </div>
        <div v-if="enc.diagnosis_icd10" class="mt-1.5">
          <span class="text-[9px] font-mono font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
            ICD-10: {{ enc.diagnosis_icd10 }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { AlertTriangle, ClipboardList, History } from "lucide-vue-next";

defineProps({
  encounters: { type: Array, default: () => [] },
  patient: { type: Object, default: null },
  selectedId: { type: String, default: null },
});

defineEmits(["select"]);

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
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
