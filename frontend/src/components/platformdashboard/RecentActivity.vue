<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-bold text-gray-800">Recent Activity</h2>
      <RouterLink to="/platform/auditlogs" class="text-xs font-semibold text-blue-600 hover:underline">
        View All
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-y-3">
      <div v-for="i in 4" :key="i" class="h-14 bg-gray-50 rounded-lg animate-pulse"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="!items.length" class="py-6 flex flex-col items-center gap-y-2">
      <ScrollText class="w-7 h-7 text-gray-200" />
      <p class="text-xs text-gray-400 font-medium">No recent activity.</p>
    </div>

    <!-- Real activity from audit logs -->
    <div v-else class="flex flex-col gap-y-2.5">
      <div
        v-for="log in items"
        :key="log.id"
        class="flex items-start gap-x-3 p-3 bg-[#F4F7FC] rounded-lg"
      >
        <div :class="['w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5', iconBg(log)]">
          <component :is="iconFor(log)" class="w-4 h-4" :class="iconClr(log)" />
        </div>
        <div class="min-w-0">
          <p class="text-xs text-gray-700 font-medium leading-relaxed">
            <span class="font-bold text-gray-900">{{ log.user?.name || "System" }}</span>
            — {{ log.action }}
            <span v-if="log.target_table" class="text-gray-400"> ({{ formatTable(log.target_table) }})</span>
          </p>
          <span class="text-[10px] text-gray-400 block mt-0.5">{{ timeAgo(log.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { Building2, UserCheck, RefreshCw, ScrollText, Stethoscope, Calendar } from "lucide-vue-next";
import { usePlatformDashboardStore } from "../../stores/platformDashboardStore";

const store   = usePlatformDashboardStore();
const loading = computed(() => store.loading);
const items   = computed(() => store.recentLogs.slice(0, 5));

function iconFor(log) {
  const t = (log.target_table ?? log.action ?? "").toLowerCase();
  if (t.includes("hospital")) return Building2;
  if (t.includes("user") || t.includes("auth")) return UserCheck;
  if (t.includes("doctor") || t.includes("provider")) return Stethoscope;
  if (t.includes("appoint")) return Calendar;
  return RefreshCw;
}

function iconBg(log) {
  const t = (log.target_table ?? log.action ?? "").toLowerCase();
  if (t.includes("hospital")) return "bg-blue-100";
  if (t.includes("user") || t.includes("auth")) return "bg-emerald-100";
  if (t.includes("doctor") || t.includes("provider")) return "bg-cyan-100";
  return "bg-purple-100";
}

function iconClr(log) {
  const t = (log.target_table ?? log.action ?? "").toLowerCase();
  if (t.includes("hospital")) return "text-blue-600";
  if (t.includes("user") || t.includes("auth")) return "text-emerald-600";
  if (t.includes("doctor") || t.includes("provider")) return "text-cyan-600";
  return "text-purple-600";
}

function formatTable(table) {
  return (table ?? "").replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

function timeAgo(iso) {
  if (!iso) return "";
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 60)    return `${Math.round(diff)}s ago`;
  if (diff < 3600)  return `${Math.round(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.round(diff / 3600)}h ago`;
  return `${Math.round(diff / 86400)}d ago`;
}
</script>
