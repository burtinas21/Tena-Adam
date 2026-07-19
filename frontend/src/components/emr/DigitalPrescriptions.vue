<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-6 py-4 bg-slate-50/50 border-b border-slate-100"
    >
      <div class="flex items-center space-x-2.5">
        <Pill class="w-4 h-4 text-blue-600" />
        <h3 class="text-sm font-bold text-slate-900">Digital Prescriptions</h3>
      </div>
      <span
        class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-100/50 uppercase tracking-wider"
      >
        Sent to Pharmacy
      </span>
    </div>

    <!-- Script Details Content Layout -->
    <div class="p-6">
      <div
        class="flex flex-col sm:flex-row sm:items-start sm:justify-between p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl gap-4"
      >
        <!-- Left Compound Matrix -->
        <div class="flex items-start space-x-3.5">
          <div
            class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg border border-blue-100 shadow-sm shrink-0"
          >
            <ReceiptText class="w-4 h-4" />
          </div>
          <div>
            <h4 class="text-sm font-bold text-slate-900">
              Robitussin DM (Dextromethorphan-Guaifenesin)
            </h4>
            <p class="text-xs text-slate-500 mt-1 font-medium">
              10ml orally every 4 hours as needed for cough
            </p>

            <div class="flex items-center space-x-3 mt-3">
              <span
                class="text-[11px] font-medium text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded"
                >Quantity: 120ml</span
              >
              <span
                class="text-[11px] font-medium text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded"
                >Refills: 0</span
              >
            </div>
          </div>
        </div>

        <!-- Right Pharmacy Delivery Frame -->
        <div
          class="text-left sm:text-right shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-200"
        >
          <span
            class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider"
            >Pharmacy</span
          >
          <span class="block text-xs font-bold text-slate-800 mt-0.5"
            >CVS Pharmacy #492</span
          >
          <p
            class="text-xs text-slate-500 font-normal leading-snug mt-1 max-w-[180px] sm:ml-auto"
          >
            123 Main St, Suite 100
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Pill, ReceiptText } from "lucide-vue-next";
</script>
