<template>
  <div class="flex flex-col justify-between h-full space-y-4">
    <!-- Area Content Block -->
    <div
      class="space-y-4 overflow-y-auto pr-1 flex-1 max-h-[calc(100vh-14rem)] custom-scroll"
    >
      <div
        class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] flex items-start justify-between gap-4"
      >
        <div class="flex items-center space-x-3">
          <div
            class="w-10 h-10 rounded-full bg-[#0252D7] text-white flex items-center justify-center font-bold text-xs tracking-wider shadow-inner"
          >
            SJ
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">
              Sarah Jenkins
            </h3>
            <span class="block text-[11px] font-medium text-slate-400 mt-0.5">
              Female, 34 yrs • DOB: 05/12/1989
            </span>
          </div>
        </div>

        <!-- Severe Allergy Flag Badge alert banner marker -->
        <div
          class="bg-rose-50 border border-rose-100 rounded-lg p-1.5 flex items-start space-x-1 max-w-[110px]"
        >
          <AlertTriangle class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" />
          <div
            class="text-[9px] leading-tight font-bold text-rose-800 uppercase tracking-wide"
          >
            Allergy:
            <span class="block font-semibold text-rose-600 normal-case mt-0.5"
              >Penicillin</span
            >
          </div>
        </div>
      </div>

      <!-- Profile Section Short Controls Buttons -->
      <div class="grid grid-cols-2 gap-3">
        <button
          class="py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-sm"
        >
          View Full Profile
        </button>
        <button
          class="py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-sm"
        >
          Past Encounters
        </button>
      </div>

      <!-- Box 2: Patient Active Prescription Lists Widget -->
      <ActiveMedications />

      <!-- Box 3: Live Notes entry field text form -->
      <ConsultationNotesForm />

      <!-- Box 4: Quick Action Operations Module panel items -->
      <div
        class="bg-white border border-slate-200 rounded-xl p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] space-y-3"
      >
        <span
          class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider"
          >Quick Actions</span
        >
        <div class="grid grid-cols-2 gap-3">
          <button
            class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition hover:border-slate-300"
          >
            <FilePlus class="w-4 h-4 text-slate-500 mb-1.5" />
            <span class="text-[10px] font-bold text-slate-700"
              >New Prescription</span
            >
          </button>
          <button
            class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition hover:border-slate-300"
          >
            <Beaker class="w-4 h-4 text-slate-500 mb-1.5" />
            <span class="text-[10px] font-bold text-slate-700">Order Labs</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Action Signatures operational panels footer blocks -->
    <div class="space-y-2.5 pt-2 border-t border-slate-100 bg-[#F8FAFC]">
      <button
        class="w-full flex items-center justify-center space-x-2 py-3 bg-[#0252D7] text-white text-xs font-bold rounded-xl hover:bg-blue-700 shadow-md shadow-blue-600/10 active:scale-[0.99] transition"
      >
        <ClipboardCheck class="w-4 h-4" />
        <span>End Session & Complete Summary</span>
      </button>
      <button
        class="w-full flex items-center justify-center space-x-2 py-2.5 bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-xl shadow-sm transition"
      >
        <CalendarPlus class="w-4 h-4 text-slate-400" />
        <span>Request Follow-up</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import ActiveMedications from "./ActiveMedications.vue";
import ConsultationNotesForm from "./ConsultationNotesForm.vue";
import {
  AlertTriangle,
  FilePlus,
  Beaker,
  ClipboardCheck,
  CalendarPlus,
} from "lucide-vue-next";
</script>
