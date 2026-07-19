<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
  >
    <!-- Header -->
    <div
      class="flex items-center space-x-2.5 px-6 py-4 bg-slate-50/50 border-b border-slate-100"
    >
      <FileText class="w-4 h-4 text-blue-600" />
      <h3 class="text-sm font-bold text-slate-900">Clinical Assessment</h3>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-6">
      <div>
        <span
          class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider"
          >Primary Diagnosis</span
        >
        <h4 class="text-base font-bold text-slate-800 mt-1">
          Acute Upper Respiratory Infection (J06.9)
        </h4>
      </div>

      <div class="space-y-3">
        <span
          class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider"
          >Physician's Notes</span
        >
        <div
          class="text-sm text-slate-600 font-normal leading-relaxed space-y-3 pr-2"
        >
          <p>
            Patient reports onset of symptoms 3 days ago, including a dry cough,
            low-grade fever (100.2°F reported), and mild fatigue. No shortness
            of breath or chest pain. Throat appears slightly erythematous on
            video examination. Lungs sound clear based on patient's reported
            breathing patterns.
          </p>
          <p
            class="font-medium text-slate-700 bg-blue-50/40 p-3 rounded-lg border border-blue-100/30"
          >
            Advised rest, increased fluid intake, and over-the-counter
            antipyretics as needed. Prescribing a short course cough suppressant
            to aid with sleep.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { FileText } from "lucide-vue-next";
</script>
