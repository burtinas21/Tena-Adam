<template>
  <div class="p-6 bg-white min-h-screen text-slate-800">
    <!-- Header Section -->
    <div class="flex justify-between items-start mb-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">
          Audit Logs
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Monitor system activities and security events for your hospital.
        </p>
      </div>
      <button
        @click="exportLogs"
        class="inline-flex items-center gap-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Export Logs
      </button>
    </div>

    <!-- Error state -->
    <div v-if="store.error" class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm">
      {{ store.error }}
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
      <!-- Search -->
      <div class="relative flex-1 max-w-md">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </span>
        <input
          v-model="searchInput"
          @input="onSearchInput"
          type="text"
          placeholder="Search user or action..."
          class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <div class="flex items-center gap-2">
        <!-- Action filter -->
        <select
          v-model="selectedAction"
          @change="applyFilters"
          class="bg-white border border-slate-300 rounded-lg text-sm px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Action Types</option>
          <option v-for="action in store.availableActions" :key="action" :value="action">
            {{ action }}
          </option>
        </select>

        <!-- Date range -->
        <input
          v-model="dateFrom"
          @change="applyFilters"
          type="date"
          class="bg-white border border-slate-300 rounded-lg text-sm px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          title="From date"
        />
        <input
          v-model="dateTo"
          @change="applyFilters"
          type="date"
          class="bg-white border border-slate-300 rounded-lg text-sm px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          title="To date"
        />

        <!-- Reset filters -->
        <button
          @click="resetFilters"
          class="p-2 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-500"
          title="Reset filters"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M6.75 12h10.5M11.25 19.5h1.5" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
      <!-- Loading skeleton -->
      <div v-if="store.loading" class="p-8 flex justify-center items-center gap-3 text-slate-500 text-sm">
        <svg class="w-5 h-5 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
        Loading audit logs…
      </div>

      <!-- Empty state -->
      <div v-else-if="!store.hasLogs" class="p-12 flex flex-col items-center justify-center text-slate-400 text-sm gap-2">
        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        No audit logs found.
      </div>

      <table v-else class="w-full text-left border-collapse bg-white">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            <th class="px-6 py-4">User</th>
            <th class="px-6 py-4">Action</th>
            <th class="px-6 py-4">Module</th>
            <th class="px-6 py-4">Timestamp</th>
            <th class="px-6 py-4">IP Address</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <tr
            v-for="log in store.logs"
            :key="log.id"
            class="hover:bg-slate-50/70 transition-colors"
          >
            <!-- User Column -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="avatarClass(log)" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-xs tracking-wider shadow-sm">
                  {{ initials(log.user?.name) }}
                </div>
                <div>
                  <div class="font-medium text-slate-900">{{ log.user?.name || 'Unknown' }}</div>
                  <div class="text-xs text-slate-400 mt-0.5">{{ log.user?.email || '—' }}</div>
                </div>
              </div>
            </td>

            <!-- Action Column -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-2 font-medium">
                <span :class="actionDotClass(log.action)" class="w-2 h-2 rounded-full inline-block"></span>
                <span :class="actionTextClass(log.action)">{{ log.action }}</span>
              </div>
            </td>

            <!-- Module Column -->
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded border border-blue-100 bg-blue-50/50 text-xs font-medium text-blue-600">
                {{ formatModule(log.target_table) }}
              </span>
            </td>

            <!-- Timestamp Column -->
            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
              {{ formatDate(log.created_at) }}
            </td>

            <!-- IP Address Column -->
            <td class="px-6 py-4 text-slate-500 font-mono tracking-tight">
              {{ log.ip_address || '—' }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Footer -->
      <div
        v-if="!store.loading && store.hasLogs"
        class="flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-white text-sm text-slate-500"
      >
        <div>
          Showing
          <span class="font-medium text-slate-800">{{ store.meta.from }}</span>
          to
          <span class="font-medium text-slate-800">{{ store.meta.to }}</span>
          of
          <span class="font-medium text-slate-800">{{ store.meta.total.toLocaleString() }}</span>
          entries
        </div>
        <div class="flex items-center gap-1">
          <!-- Prev -->
          <button
            @click="store.goToPage(store.currentPage - 1)"
            :disabled="store.currentPage === 1"
            class="p-2 text-slate-400 hover:text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
          </button>

          <!-- Page buttons -->
          <template v-for="page in visiblePages" :key="page">
            <span v-if="page === '...'" class="px-1 text-slate-400">…</span>
            <button
              v-else
              @click="store.goToPage(page)"
              :class="page === store.currentPage
                ? 'bg-blue-600 text-white'
                : 'text-slate-600 hover:bg-slate-50'"
              class="w-8 h-8 rounded-lg font-medium flex items-center justify-center"
            >
              {{ page }}
            </button>
          </template>

          <!-- Next -->
          <button
            @click="store.goToPage(store.currentPage + 1)"
            :disabled="store.currentPage === store.totalPages"
            class="p-2 text-slate-400 hover:text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuditLogStore } from "@/stores/auditLogStore";

const store = useAuditLogStore();

// Local filter state (synced to store on apply)
const searchInput   = ref("");
const selectedAction = ref("");
const dateFrom       = ref("");
const dateTo         = ref("");

// Debounce timer for search input
let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => applyFilters(), 400);
}

function applyFilters() {
  store.applyFilters({
    search:    searchInput.value,
    action:    selectedAction.value,
    date_from: dateFrom.value,
    date_to:   dateTo.value,
  });
}

function resetFilters() {
  searchInput.value    = "";
  selectedAction.value = "";
  dateFrom.value       = "";
  dateTo.value         = "";
  store.resetFilters();
}

// Helpers ----------------------------------------------------------------

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((w) => w[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

const avatarPalette = [
  ["bg-blue-100",   "text-blue-600"],
  ["bg-emerald-100","text-emerald-600"],
  ["bg-purple-100", "text-purple-600"],
  ["bg-amber-100",  "text-amber-600"],
  ["bg-rose-100",   "text-rose-600"],
  ["bg-slate-200",  "text-slate-600"],
];

function avatarClass(log) {
  const idx = log.user?.name
    ? log.user.name.charCodeAt(0) % avatarPalette.length
    : avatarPalette.length - 1;
  const [bg, text] = avatarPalette[idx];
  return `${bg} ${text}`;
}

const dangerActions = /delete|fail|reject|cancel|denied|unauthori/i;
const warningActions = /update|edit|change|modif/i;

function actionDotClass(action) {
  if (dangerActions.test(action))  return "bg-rose-500";
  if (warningActions.test(action)) return "bg-amber-400";
  return "bg-emerald-500";
}

function actionTextClass(action) {
  if (dangerActions.test(action)) return "text-rose-600 font-semibold";
  return "text-slate-700";
}

function formatModule(table) {
  if (!table) return "—";
  return table
    .replace(/_/g, " ")
    .replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatDate(iso) {
  if (!iso) return "—";
  return new Date(iso).toLocaleString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
}

// Smart pagination: show at most 5 page buttons + ellipsis
const visiblePages = computed(() => {
  const total   = store.totalPages;
  const current = store.currentPage;
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

  const pages = [];
  pages.push(1);

  if (current > 3)      pages.push("...");

  const start = Math.max(2, current - 1);
  const end   = Math.min(total - 1, current + 1);
  for (let p = start; p <= end; p++) pages.push(p);

  if (current < total - 2) pages.push("...");
  pages.push(total);

  return pages;
});

// Export (CSV-style download using current filtered data)
function exportLogs() {
  const rows = [["User", "Email", "Action", "Module", "Timestamp", "IP Address"]];
  for (const log of store.logs) {
    rows.push([
      log.user?.name ?? "",
      log.user?.email ?? "",
      log.action,
      formatModule(log.target_table),
      formatDate(log.created_at),
      log.ip_address ?? "",
    ]);
  }
  const csv     = rows.map((r) => r.map((c) => `"${c}"`).join(",")).join("\n");
  const blob    = new Blob([csv], { type: "text/csv" });
  const url     = URL.createObjectURL(blob);
  const a       = document.createElement("a");
  a.href        = url;
  a.download    = `audit-logs-${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
  URL.revokeObjectURL(url);
}

// Load data on mount
onMounted(async () => {
  await Promise.all([store.fetchLogs(1), store.fetchActions()]);
});
</script>
