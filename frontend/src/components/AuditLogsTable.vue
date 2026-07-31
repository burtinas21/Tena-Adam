<template>
  <div
    class="bg-white border border-slate-200 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] overflow-hidden"
  >
    <!-- ── Filter Controls Bar ── -->
    <div
      class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white"
    >
      <!-- Search -->
      <div class="relative w-full max-w-sm">
        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input
          v-model="searchInput"
          type="text"
          placeholder="Search user or action..."
          class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
          @input="onSearchInput"
        />
      </div>

      <!-- Right-side filters -->
      <div class="flex flex-wrap items-center gap-2 shrink-0">
        <!-- Action filter -->
        <div class="relative">
          <select
            v-model="selectedAction"
            class="appearance-none pl-3 pr-7 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition cursor-pointer"
            @change="onActionChange"
          >
            <option value="">All Action Types</option>
            <option v-for="act in store.availableActions" :key="act" :value="act">
              {{ act }}
            </option>
          </select>
          <ChevronDown class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
        </div>

        <!-- Date preset filter -->
        <div class="relative">
          <select
            v-model="datePreset"
            class="appearance-none pl-3 pr-7 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition cursor-pointer"
            @change="onDatePresetChange"
          >
            <option value="">All Time</option>
            <option value="24h">Last 24 Hours</option>
            <option value="7d">Last 7 Days</option>
            <option value="30d">Last 30 Days</option>
          </select>
          <ChevronDown class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
        </div>

        <!-- Clear filters -->
        <button
          v-if="hasActiveFilters"
          class="flex items-center gap-1.5 px-3 py-2 bg-rose-50 border border-rose-100 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-100 transition shadow-sm"
          @click="resetFilters"
        >
          <X class="w-3.5 h-3.5" />
          Clear
        </button>
      </div>
    </div>

    <!-- ── Loading State ── -->
    <div v-if="store.loading" class="py-20 flex flex-col justify-center items-center gap-3">
      <div class="w-8 h-8 border-[3px] border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <span class="text-xs font-medium text-slate-400">Loading audit logs…</span>
    </div>

    <!-- ── Error: 403 Forbidden ── -->
    <div v-else-if="store.isForbidden" class="py-20 flex flex-col items-center gap-3 text-center px-6">
      <ShieldAlert class="w-10 h-10 text-amber-400" />
      <p class="text-sm font-semibold text-slate-700">Access Denied</p>
      <p class="text-xs text-slate-400 max-w-xs">
        Your account does not have permission to view audit logs. Platform Admin role is required.
      </p>
    </div>

    <!-- ── Error: Other ── -->
    <div v-else-if="store.error" class="py-20 flex flex-col items-center gap-3 text-center px-6">
      <AlertCircle class="w-10 h-10 text-rose-400" />
      <p class="text-sm font-semibold text-slate-700">Failed to load audit logs</p>
      <p class="text-xs text-slate-400">{{ store.error }}</p>
      <button
        class="mt-2 px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition"
        @click="store.fetchLogs(store.currentPage)"
      >
        Retry
      </button>
    </div>

    <!-- ── Empty State ── -->
    <div v-else-if="!store.hasLogs" class="py-20 flex flex-col items-center gap-3 text-center px-6">
      <ScrollText class="w-10 h-10 text-slate-200" />
      <p class="text-sm font-semibold text-slate-500">No audit logs found</p>
      <p class="text-xs text-slate-400">
        {{ hasActiveFilters ? "Try adjusting your filters." : "System activity will appear here as actions are performed." }}
      </p>
    </div>

    <!-- ── Data Table ── -->
    <template v-else>
      <div>
        <table class="w-full table-fixed text-left border-collapse text-[10px] sm:text-xs">
          <colgroup>
            <col style="width:26%" />
            <col style="width:20%" />
            <col style="width:18%" />
            <col style="width:24%" />
            <col style="width:12%" />
          </colgroup>
          <thead>
            <tr class="bg-slate-50/60 border-b border-slate-100 font-bold text-slate-400 uppercase tracking-wider">
              <th class="py-2 sm:py-3 px-2 sm:px-6">User</th>
              <th class="py-2 sm:py-3 px-1.5 sm:px-6">Action</th>
              <th class="py-2 sm:py-3 px-1.5 sm:px-6">Module</th>
              <th class="py-2 sm:py-3 px-1.5 sm:px-6">Timestamp</th>
              <th class="py-2 sm:py-3 px-1.5 sm:px-6">IP</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
            <tr
              v-for="log in store.logs"
              :key="log.id"
              class="hover:bg-slate-50/40 transition"
            >
              <!-- User -->
              <td class="py-2.5 sm:py-4 px-2 sm:px-6">
                <div class="flex items-center gap-1.5 sm:space-x-3.5">
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center font-bold text-blue-600 text-[9px] sm:text-[11px] tracking-wider shadow-inner shrink-0"
                  >
                    {{ initials(log.user?.name) }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-800 tracking-tight truncate">
                      {{ log.user?.name || "System" }}
                    </div>
                    <div class="text-[9px] sm:text-[10px] text-slate-400 font-semibold mt-0.5 truncate">
                      {{ log.user?.email || "—" }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Action -->
              <td class="py-2.5 sm:py-4 px-1.5 sm:px-6">
                <div
                  class="flex items-center gap-1 font-bold tracking-tight"
                  :class="actionColor(log.action)"
                >
                  <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="actionDotColor(log.action)"></span>
                  <span class="truncate">{{ log.action }}</span>
                </div>
              </td>

              <!-- Module -->
              <td class="py-2.5 sm:py-4 px-1.5 sm:px-6">
                <span
                  v-if="log.target_table"
                  class="px-1 sm:px-2 py-0.5 rounded text-[9px] sm:text-[10px] font-bold tracking-wide border truncate block w-fit max-w-full"
                  :class="moduleChipClass(log.target_table)"
                >
                  {{ formatModule(log.target_table) }}
                </span>
                <span v-else class="text-slate-300">—</span>
              </td>

              <!-- Timestamp -->
              <td class="py-2.5 sm:py-4 px-1.5 sm:px-6 font-mono text-slate-500 text-[9px] sm:text-[11px]">
                {{ formatDate(log.created_at) }}
              </td>

              <!-- IP Address -->
              <td class="py-2.5 sm:py-4 px-1.5 sm:px-6 font-mono text-slate-500 text-[9px] sm:text-[11px] truncate">
                {{ log.ip_address || "—" }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ── Pagination Footer ── -->
      <div class="px-3 sm:px-6 py-3 sm:py-4 bg-white border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3">

        <!-- Left: entry count + per-page selector -->
        <div class="flex items-center gap-3 text-xs text-slate-400 font-medium">
          <span>
            Showing
            <span class="font-bold text-slate-600">{{ store.meta.from }}</span>
            –
            <span class="font-bold text-slate-600">{{ store.meta.to }}</span>
            of
            <span class="font-bold text-slate-600">{{ store.meta.total.toLocaleString() }}</span>
            entries
          </span>

          <!-- Per-page selector -->
          <div class="relative">
            <select
              :value="store.meta.per_page"
              class="appearance-none pl-2.5 pr-6 py-1.5 bg-white border border-slate-200 rounded-md text-xs font-bold text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer shadow-sm transition"
              @change="onPerPageChange"
            >
              <option value="5">5 / page</option>
              <option value="10">10 / page</option>
              <option value="20">20 / page</option>
              <option value="50">50 / page</option>
            </select>
            <ChevronDown class="pointer-events-none absolute right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400" />
          </div>
        </div>

        <!-- Right: page buttons -->
        <div class="flex items-center gap-1">
          <!-- Previous -->
          <button
            :disabled="store.currentPage <= 1"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border text-xs font-bold transition shadow-sm"
            :class="
              store.currentPage <= 1
                ? 'border-slate-100 text-slate-300 bg-white cursor-not-allowed'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 cursor-pointer'
            "
            @click="store.goToPage(store.currentPage - 1)"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>

          <!-- Page number pills -->
          <template v-for="p in visiblePages" :key="String(p)">
            <span
              v-if="p === '…'"
              class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-300 select-none"
            >…</span>
            <button
              v-else
              class="inline-flex items-center justify-center w-8 h-8 rounded-lg border text-xs font-bold transition shadow-sm"
              :class="
                p === store.currentPage
                  ? 'bg-[#0252D7] border-[#0252D7] text-white shadow-md'
                  : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer'
              "
              @click="store.goToPage(p)"
            >
              {{ p }}
            </button>
          </template>

          <!-- Next -->
          <button
            :disabled="store.currentPage >= store.totalPages"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border text-xs font-bold transition shadow-sm"
            :class="
              store.currentPage >= store.totalPages
                ? 'border-slate-100 text-slate-300 bg-white cursor-not-allowed'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 cursor-pointer'
            "
            @click="store.goToPage(store.currentPage + 1)"
          >
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import {
  Search,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  AlertCircle,
  ShieldAlert,
  ScrollText,
  X,
} from "lucide-vue-next";
import { useAuditLogStore } from "../stores/auditLogStore";

const store = useAuditLogStore();

// ── Local filter refs ───────────────────────────────────────────────────────
const searchInput    = ref("");
const selectedAction = ref("");
const datePreset     = ref("");
let   searchDebounce = null;

const hasActiveFilters = computed(
  () =>
    store.filters.search    !== "" ||
    store.filters.action    !== "" ||
    store.filters.date_from !== ""
);

// ── Filter handlers ─────────────────────────────────────────────────────────
function onSearchInput() {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(
    () => store.applyFilters({ search: searchInput.value.trim() }),
    400
  );
}

function onActionChange() {
  store.applyFilters({ action: selectedAction.value });
}

function onDatePresetChange() {
  const now     = new Date();
  const date_to = now.toISOString().slice(0, 10);
  let date_from = "";

  if (datePreset.value === "24h") {
    const d = new Date(now); d.setDate(d.getDate() - 1);
    date_from = d.toISOString().slice(0, 10);
  } else if (datePreset.value === "7d") {
    const d = new Date(now); d.setDate(d.getDate() - 7);
    date_from = d.toISOString().slice(0, 10);
  } else if (datePreset.value === "30d") {
    const d = new Date(now); d.setDate(d.getDate() - 30);
    date_from = d.toISOString().slice(0, 10);
  }

  store.applyFilters({ date_from, date_to: datePreset.value ? date_to : "" });
}

function onPerPageChange(e) {
  store.setPerPage(Number(e.target.value));
}

async function resetFilters() {
  searchInput.value    = "";
  selectedAction.value = "";
  datePreset.value     = "";
  await store.resetFilters();
}

// ── Pagination page list ────────────────────────────────────────────────────
const visiblePages = computed(() => {
  const total   = store.totalPages;
  const current = store.currentPage;

  if (total <= 1)  return [1];
  if (total <= 7)  return Array.from({ length: total }, (_, i) => i + 1);
  if (current <= 4)         return [1, 2, 3, 4, 5, "…", total];
  if (current >= total - 3) return [1, "…", total - 4, total - 3, total - 2, total - 1, total];
  return [1, "…", current - 1, current, current + 1, "…", total];
});

// ── Display helpers ─────────────────────────────────────────────────────────
function initials(name) {
  if (!name) return "SY";
  return name.split(" ").slice(0, 2).map((w) => w[0]?.toUpperCase() ?? "").join("");
}

function formatDate(iso) {
  if (!iso) return "—";
  try {
    return new Date(iso).toLocaleString("en-US", {
      month: "short", day: "2-digit", year: "numeric",
      hour: "2-digit", minute: "2-digit", second: "2-digit",
      hour12: false,
    });
  } catch { return iso; }
}

function formatModule(table) {
  if (!table) return "";
  return table.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

const MODULE_COLORS = {
  hospitals:            "bg-blue-50   border-blue-100/50  text-blue-600",
  users:                "bg-slate-100 border-slate-200     text-slate-600",
  appointments:         "bg-teal-50   border-teal-100/50   text-teal-600",
  departments:          "bg-indigo-50 border-indigo-100/40 text-indigo-600",
  healthcare_providers: "bg-cyan-50   border-cyan-100/50   text-cyan-600",
  doctor_leaves:        "bg-amber-50  border-amber-100/50  text-amber-700",
  facilities:           "bg-violet-50 border-violet-100/50 text-violet-600",
  auth:                 "bg-pink-50   border-pink-100/40   text-pink-600",
};

function moduleChipClass(table) {
  return MODULE_COLORS[table] ?? "bg-slate-100 border-slate-200 text-slate-600";
}

function actionColor(action) {
  const a = (action ?? "").toLowerCase();
  if (/fail|error|delet|deny|unauthori/.test(a)) return "text-rose-600";
  if (/creat|regist|add|approv/.test(a))          return "text-emerald-700";
  if (/login|logout/.test(a))                      return "text-indigo-600";
  return "text-slate-800";
}

function actionDotColor(action) {
  const a = (action ?? "").toLowerCase();
  if (/fail|error|delet|deny|unauthori/.test(a)) return "bg-rose-500 animate-pulse";
  if (/creat|regist|add|approv/.test(a))          return "bg-emerald-500";
  if (/login|logout/.test(a))                      return "bg-indigo-500";
  return "bg-blue-500";
}

// ── Init ────────────────────────────────────────────────────────────────────
onMounted(async () => {
  await Promise.all([store.fetchLogs(1), store.fetchActions()]);
});
</script>
