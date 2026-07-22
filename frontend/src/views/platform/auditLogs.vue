<template>
  <div
    class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 dark:text-slate-300 selection:bg-blue-600/10"
  >
    <div class="max-w-[1440px] mx-auto space-y-6">
      <!-- ── Page Header ── -->
      <div
        class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-2"
      >
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Audit Logs
          </h1>
          <p class="text-xs text-slate-400 font-medium tracking-wide mt-0.5">
            Monitor system activities and security events across the platform.
          </p>
          <!-- Live total count -->
          <p
            v-if="!store.loading && store.meta.total > 0"
            class="text-[11px] text-slate-400 mt-1"
          >
            <span class="font-bold text-slate-600">
              {{ store.meta.total.toLocaleString() }}
            </span>
            total events recorded
          </p>
        </div>

        <button
          class="flex items-center space-x-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 shadow-sm active:scale-[0.98] transition self-start"
          title="Export feature coming soon"
          @click="onExport"
        >
          <Download class="w-4 h-4 text-slate-400" />
          <span>Export Logs</span>
        </button>
      </div>

      <!-- ── Audit Logs Table ── -->
      <AuditLogsTable />
    </div>
  </div>
</template>

<script setup>
import { Download } from "lucide-vue-next";
import AuditLogsTable from "../../components/AuditLogsTable.vue";
import { useAuditLogStore } from "../../stores/auditLogStore";

const store = useAuditLogStore();

function onExport() {
  // Simple CSV export of the current page
  if (!store.logs.length) return;

  const headers = ["User", "Email", "Action", "Module", "Timestamp", "IP Address"];
  const rows = store.logs.map((log) => [
    log.user?.name    ?? "System",
    log.user?.email   ?? "",
    log.action        ?? "",
    log.target_table  ?? "",
    log.created_at    ?? "",
    log.ip_address    ?? "",
  ]);

  const csv =
    [headers, ...rows]
      .map((row) =>
        row
          .map((cell) => `"${String(cell).replace(/"/g, '""')}"`)
          .join(",")
      )
      .join("\n");

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement("a");
  a.href     = url;
  a.download = `audit-logs-page-${store.currentPage}.csv`;
  a.click();
  URL.revokeObjectURL(url);
}
</script>
