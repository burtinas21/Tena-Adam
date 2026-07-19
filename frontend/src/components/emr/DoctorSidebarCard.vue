<template>
  <div class="space-y-5">
    <div
      class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm text-center"
    >
      <div class="relative w-16 h-16 mx-auto">
        <img
          src="https://unsplash.com"
          alt="Dr. Sarah Jenkins Avatar"
          class="w-full h-full object-cover rounded-full ring-4 ring-blue-50 shadow-inner"
        />
        <span
          class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white shadow-sm"
        ></span>
      </div>

      <h3 class="text-base font-bold text-slate-900 tracking-tight mt-3">
        Dr. Sarah Jenkins
      </h3>
      <span class="block text-xs font-medium text-slate-500 mt-0.5"
        >Internal Medicine & General Practice</span
      >

      <div
        class="flex items-center justify-center space-x-1 mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"
      >
        <Award class="w-3.5 h-3.5 text-blue-500" />
        <span>Board Certified</span>
      </div>

      <button
        class="w-full mt-5 py-2 bg-white border border-slate-200 text-xs font-bold text-blue-600 rounded-lg hover:bg-blue-50/50 shadow-sm active:scale-[0.98] transition"
      >
        Message Provider
      </button>
    </div>

    <!-- Part 2: Warning Alerts Context Container Box -->
    <div
      class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4"
    >
      <div
        class="flex items-center space-x-2 text-rose-600 border-b border-rose-50 pb-3"
      >
        <AlertCircle class="w-4 h-4 shrink-0" />
        <h4 class="text-xs font-bold uppercase tracking-wider">
          Important Instructions
        </h4>
      </div>

      <p class="text-xs text-slate-600 leading-relaxed font-normal">
        If you experience any of the following symptoms, seek immediate
        emergency medical care:
      </p>

      <ul class="space-y-2.5 text-xs text-slate-600 font-medium pl-1">
        <li class="flex items-start space-x-2">
          <span
            class="w-1.5 h-1.5 rounded-full bg-slate-400 mt-1.5 shrink-0"
          ></span>
          <span>Difficulty breathing or severe shortness of breath</span>
        </li>
        <li class="flex items-start space-x-2">
          <span
            class="w-1.5 h-1.5 rounded-full bg-slate-400 mt-1.5 shrink-0"
          ></span>
          <span>Persistent chest pain or pressure</span>
        </li>
        <li class="flex items-start space-x-2">
          <span
            class="w-1.5 h-1.5 rounded-full bg-slate-400 mt-1.5 shrink-0"
          ></span>
          <span>Fever exceeding 103°F (39.4°C)</span>
        </li>
        <li class="flex items-start space-x-2">
          <span
            class="w-1.5 h-1.5 rounded-full bg-slate-400 mt-1.5 shrink-0"
          ></span>
          <span>Confusion or inability to wake up</span>
        </li>
      </ul>

      <div class="border-t border-slate-100 pt-3.5">
        <span
          class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider"
          >Recommended Follow-up</span
        >
        <p class="text-xs text-slate-600 font-normal leading-relaxed mt-1">
          In 5-7 days if symptoms persist or worsen.
        </p>
      </div>
    </div>

    <!-- Part 3: Help Desk CTA Support Banner -->
    <div
      class="bg-blue-50/40 border border-blue-100 rounded-xl p-5 shadow-sm text-center space-y-2"
    >
      <HelpCircle class="w-5 h-5 text-blue-600 mx-auto" />
      <h4 class="text-xs font-bold text-slate-900 tracking-tight">
        Need technical help?
      </h4>
      <p
        class="text-[11px] text-slate-500 font-normal leading-normal max-w-[200px] mx-auto"
      >
        If you had issues with video quality or accessing your prescription.
      </p>
      <button
        class="inline-block text-[11px] font-bold text-blue-600 hover:text-blue-700 pt-1 tracking-tight hover:underline"
      >
        Contact Support
      </button>
    </div>
  </div>
</template>

<script setup>
import { Award, AlertCircle, HelpCircle } from "lucide-vue-next";
</script>
