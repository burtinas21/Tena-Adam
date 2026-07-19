<template>
  <div
    class="min-h-screen bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 selection:bg-blue-600/10"
  >
    <div class="max-w-[1440px] mx-auto space-y-6">
      <!-- Section 1: Page Headline Banner row with Exports CTA -->
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2"
      >
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Audit Logs
          </h1>
          <p class="text-xs text-slate-400 font-medium tracking-wide mt-0.5">
            Monitor system activities and security events across the platform.
          </p>
        </div>

        <button
          class="flex items-center space-x-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 shadow-sm active:scale-[0.98] transition self-start sm:self-center"
        >
          <Download class="w-4 h-4 text-slate-400" />
          <span>Export Logs</span>
        </button>
      </div>

      <!-- Section 2: Audit Logs Master Listing Module Table Component -->
      <AuditLogsTable />
    </div>
  </div>
</template>

<script setup>
import AuditLogsTable from "../../components/AuditLogsTable.vue";
import { Download } from "lucide-vue-next";
</script>
