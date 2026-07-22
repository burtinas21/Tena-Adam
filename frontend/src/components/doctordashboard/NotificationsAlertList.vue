<template>
  <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-x-2">
        <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
        <span
          v-if="unread > 0"
          class="w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center"
        >{{ unread }}</span>
      </div>
      <RouterLink to="/doctor/notifications" class="text-[10px] font-semibold text-blue-600 hover:underline">
        See all
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-y-3">
      <div v-for="i in 3" :key="i" class="h-12 bg-gray-50 rounded-lg animate-pulse"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="!items.length" class="py-6 flex flex-col items-center gap-y-1">
      <BellOff class="w-7 h-7 text-gray-200" />
      <p class="text-xs text-gray-400 font-medium">No notifications.</p>
    </div>

    <!-- Items -->
    <div v-else class="flex flex-col gap-y-3 divide-y divide-gray-50 text-xs">
      <div
        v-for="n in items"
        :key="n.id"
        class="flex gap-x-3 items-start pt-3 first:pt-0"
        :class="{ 'opacity-60': n.isRead }"
      >
        <component
          :is="iconFor(n.type)"
          class="w-4 h-4 mt-0.5 flex-shrink-0"
          :class="iconColor(n.type)"
        />
        <div class="min-w-0">
          <h4 class="font-bold text-gray-800 truncate">{{ n.title }}</h4>
          <p class="text-gray-500 font-medium mt-0.5 line-clamp-2">{{ n.message }}</p>
          <span class="text-[9px] text-gray-400 font-bold block mt-1 uppercase">
            {{ timeAgo(n.time) }}
          </span>
        </div>
        <div v-if="!n.isRead" class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0 mt-1.5"></div>
      </div>
    </div>

    <!-- Mark all read -->
    <button
      v-if="unread > 0"
      class="w-full text-center mt-3 pt-3 border-t border-gray-50 text-xs font-bold text-blue-600 hover:underline"
      @click="store.markAllRead()"
    >
      Mark all as read
    </button>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { AlertTriangle, CalendarX, Bell, BellOff, Info, CheckCircle } from "lucide-vue-next";
import { useDoctorDashboardStore } from "../../stores/doctorDashboardStore";

const store   = useDoctorDashboardStore();
const loading = computed(() => store.loading);
const items   = computed(() => store.recentNotifications);
const unread  = computed(() => store.unreadNotifications);

function iconFor(type) {
  const t = (type ?? "").toLowerCase();
  if (t.includes("cancel") || t.includes("alert")) return AlertTriangle;
  if (t.includes("appoint"))                        return CalendarX;
  if (t.includes("success") || t.includes("done"))  return CheckCircle;
  if (t.includes("info") || t.includes("system"))   return Info;
  return Bell;
}

function iconColor(type) {
  const t = (type ?? "").toLowerCase();
  if (t.includes("cancel") || t.includes("alert") || t.includes("urgent")) return "text-red-500";
  if (t.includes("appoint"))  return "text-amber-500";
  if (t.includes("success"))  return "text-emerald-500";
  return "text-blue-500";
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
