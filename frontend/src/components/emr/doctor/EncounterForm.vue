<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col h-full overflow-hidden"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-6 py-4 bg-slate-50/50 border-b border-slate-100"
    >
      <div class="flex items-center space-x-2">
        <FileEdit class="w-4 h-4 text-blue-600" />
        <h3 class="text-sm font-bold text-slate-900">Encounter Documentation</h3>
      </div>
      <div class="flex items-center gap-2">
        <span
          v-if="encounter"
          :class="statusClass"
          class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider"
        >
          {{ encounter.status?.replace("_", " ") }}
        </span>
        <span v-if="lastSaved" class="text-[10px] font-semibold text-slate-400 bg-white border px-2 py-0.5 rounded shadow-sm">
          Saved {{ lastSaved }}
        </span>
      </div>
    </div>

    <!-- No encounter selected -->
    <div v-if="!encounter" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
      <FileEdit class="w-10 h-10 text-slate-200 mb-3" />
      <p class="text-sm font-semibold text-slate-400">Select an encounter from the queue</p>
      <p class="text-xs text-slate-300 mt-1">Start a consultation by clicking "Start Encounter" from an in-progress queue</p>
    </div>

    <!-- Completed state — read-only -->
    <div v-else-if="encounter.status === 'completed'" class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
      <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-lg px-4 py-3 text-xs font-semibold">
        <FileCheck class="w-4 h-4" />
        This encounter has been completed and signed off.
      </div>
      <ReadOnlyField label="Chief Complaint" :value="encounter.chief_complaint" />
      <ReadOnlyField label="History" :value="encounter.history" />
      <ReadOnlyField label="Physical Examination" :value="encounter.physical_exam" />
      <ReadOnlyField label="Assessment" :value="encounter.assessment" />
      <ReadOnlyField label="Diagnosis" :value="encounter.diagnosis" />
      <div v-if="encounter.diagnosis_icd10" class="space-y-1">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">ICD-10 Code</label>
        <span class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ encounter.diagnosis_icd10 }}</span>
      </div>
      <ReadOnlyField label="Treatment Plan" :value="encounter.treatment_plan" />
      <ReadOnlyField label="Clinical Notes" :value="encounter.clinical_notes" />
      <div v-if="encounter.follow_up_date" class="space-y-1">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Follow-up Date</label>
        <p class="text-sm font-semibold text-slate-700">{{ encounter.follow_up_date }}</p>
      </div>
    </div>

    <!-- Editable form -->
    <div v-else class="p-6 space-y-5 overflow-y-auto flex-1 custom-scroll">
      <!-- Chief Complaint -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
          Chief Complaint <span class="text-rose-400">*</span>
        </label>
        <textarea
          v-model="form.chief_complaint"
          rows="2"
          placeholder="What brings the patient in today?"
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- History -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">History of Presenting Illness</label>
        <textarea
          v-model="form.history"
          rows="3"
          placeholder="History, onset, duration, associated symptoms..."
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Physical Examination -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Physical Examination</label>
        <textarea
          v-model="form.physical_exam"
          rows="3"
          placeholder="General appearance, systems review findings..."
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Assessment -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
          Assessment <span class="text-rose-400">*</span>
        </label>
        <textarea
          v-model="form.assessment"
          rows="2"
          placeholder="Clinical assessment and differential diagnosis..."
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Diagnosis -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">
          Primary Diagnosis <span class="text-rose-400">*</span>
        </label>
        <input
          v-model="form.diagnosis"
          type="text"
          placeholder="e.g. Essential hypertension, Type 2 diabetes mellitus"
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- ICD-10 -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">ICD-10 Code (optional)</label>
        <input
          v-model="form.diagnosis_icd10"
          type="text"
          maxlength="20"
          placeholder="e.g. I10, E11"
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Treatment Plan -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Treatment Plan & Notes</label>
        <textarea
          v-model="form.treatment_plan"
          rows="3"
          placeholder="Medications, lifestyle changes, referrals, procedures..."
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Clinical Notes -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Additional Clinical Notes</label>
        <textarea
          v-model="form.clinical_notes"
          rows="2"
          placeholder="Any additional observations or notes..."
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 leading-relaxed focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Follow Up -->
      <div class="space-y-1.5">
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Follow-up Date</label>
        <input
          v-model="form.follow_up_date"
          type="date"
          :min="today"
          class="w-full bg-white border border-slate-200 rounded-lg p-3 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Validation errors -->
      <div v-if="encounterStore.error" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ encounterStore.error }}
      </div>
    </div>

    <!-- Footer actions -->
    <div
      v-if="encounter && encounter.status !== 'completed'"
      class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap"
    >
      <div class="flex items-center space-x-2">
        <button
          @click="saveDraft"
          :disabled="encounterStore.saving"
          class="flex items-center space-x-1.5 px-3.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 transition shadow-sm disabled:opacity-50"
        >
          <Save class="w-3.5 h-3.5" />
          <span>{{ encounterStore.saving ? "Saving…" : "Save Draft" }}</span>
        </button>
        <button
          v-if="form.follow_up_date"
          @click="form.follow_up_date = ''"
          class="flex items-center space-x-1.5 px-3.5 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 transition shadow-sm"
        >
          <Calendar class="w-3.5 h-3.5" />
          <span>Clear F/U</span>
        </button>
      </div>

      <button
        @click="signAndComplete"
        :disabled="encounterStore.saving"
        class="flex items-center space-x-2 px-5 py-2 bg-[#0252D7] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-md shadow-blue-600/10 active:scale-[0.98] transition disabled:opacity-50"
      >
        <FileCheck class="w-3.5 h-3.5" />
        <span>{{ encounterStore.saving ? "Completing…" : "Sign & Complete" }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import {
  FileEdit, FileCheck, Save, Calendar, AlertCircle,
} from "lucide-vue-next";
import { useMedicalEncounterStore } from "../../../stores/medicalEncounterStore";

// Tiny inline read-only field helper
const ReadOnlyField = {
  props: { label: String, value: String },
  template: `
    <div v-if="value" class="space-y-1">
      <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ label }}</label>
      <div class="w-full bg-slate-50 border border-slate-100 rounded-lg p-3 text-xs text-slate-700 leading-relaxed whitespace-pre-wrap">{{ value }}</div>
    </div>
  `,
};

const props = defineProps({
  encounter: { type: Object, default: null },
});
const emit = defineEmits(["saved", "completed"]);

const encounterStore = useMedicalEncounterStore();
const lastSaved = ref(null);

const form = ref({
  chief_complaint: "",
  history: "",
  physical_exam: "",
  assessment: "",
  diagnosis: "",
  diagnosis_icd10: "",
  treatment_plan: "",
  clinical_notes: "",
  follow_up_date: "",
});

const today = new Date().toISOString().split("T")[0];

// Sync form when encounter changes
watch(
  () => props.encounter,
  (enc) => {
    if (!enc) return;
    form.value = {
      chief_complaint: enc.chief_complaint ?? "",
      history: enc.history ?? "",
      physical_exam: enc.physical_exam ?? "",
      assessment: enc.assessment ?? "",
      diagnosis: enc.diagnosis ?? "",
      diagnosis_icd10: enc.diagnosis_icd10 ?? "",
      treatment_plan: enc.treatment_plan ?? "",
      clinical_notes: enc.clinical_notes ?? "",
      follow_up_date: enc.follow_up_date ?? "",
    };
  },
  { immediate: true }
);

const statusClass = computed(() => {
  const map = {
    in_progress: "text-blue-700 bg-blue-50 border-blue-100",
    completed:   "text-emerald-700 bg-emerald-50 border-emerald-100",
    cancelled:   "text-red-600 bg-red-50 border-red-100",
  };
  return map[props.encounter?.status] ?? "";
});

async function saveDraft() {
  if (!props.encounter?.id) return;
  encounterStore.clearError();
  try {
    const updated = await encounterStore.update(props.encounter.id, { ...form.value });
    lastSaved.value = new Date().toLocaleTimeString("en-ET", { hour: "2-digit", minute: "2-digit" });
    emit("saved", updated);
  } catch {
    // error shown via store.error
  }
}

async function signAndComplete() {
  if (!props.encounter?.id) return;
  encounterStore.clearError();
  // Save latest data first, then complete
  try {
    await encounterStore.update(props.encounter.id, { ...form.value });
    const completed = await encounterStore.complete(props.encounter.id);
    emit("completed", completed);
  } catch {
    // error shown via store.error
  }
}
</script>
