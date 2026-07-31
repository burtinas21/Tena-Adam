<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">

    <!-- ── Header ─────────────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-3 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-slate-100 tracking-tight">
          {{ $t('dashboard.overview') }}
        </h1>
        <p class="text-xs text-gray-500 dark:text-slate-400 font-medium mt-0.5">
          {{ $t('dashboard.subtitle') }}
        </p>
      </div>

      <div class="flex items-center gap-x-2">
        <!-- Today date pill -->
        <button
          class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700
                 hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200
                 font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm"
          @click="dash.fetchAll()"
          :title="todayLabel"
        >
          <CalendarDays class="w-3.5 h-3.5 text-gray-400" :class="{ 'animate-spin': dash.loading }" />
          {{ todayLabel }}
        </button>

        <!-- Export -->
        <button
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2 px-4 rounded-lg
                 flex items-center gap-x-1.5 transition shadow-sm"
          @click="exportReport"
        >
          <Download class="w-3.5 h-3.5 text-white/90" />
          {{ $t('dashboard.export_report') }}
        </button>
      </div>
    </div>

    <!-- ── Error banner ───────────────────────────────────────────────── -->
    <div
      v-if="dash.error"
      class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
             rounded-lg flex items-center gap-x-2 text-xs text-red-700 dark:text-red-400 font-medium"
    >
      <AlertCircle class="w-4 h-4 shrink-0" />
      {{ dash.error }}
      <button class="ml-auto underline font-bold" @click="dash.fetchAll()">
        {{ $t('dashboard.retry') }}
      </button>
    </div>

    <!-- ── Main layout: left content + right sidebar ──────────────────── -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">

      <!-- ── LEFT (3/4) ──────────────────────────────────────────────── -->
      <div class="xl:col-span-3 flex flex-col gap-y-5">

        <!-- Stat cards: 4 per row, 2 rows = 8 cards -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <MiniStatCard
            :label="$t('dashboard.total_patients')"
            :value="dash.totalPatients"
            :icon="Users"
            :trend="newPatientsLabel"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.total_doctors')"
            :value="dash.totalDoctors"
            :icon="Stethoscope"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.departments')"
            :value="dash.totalDepartments"
            :icon="Building2"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.total_appointments')"
            :value="dash.totalAppointments"
            :icon="CalendarCheck"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.completed')"
            :value="dash.completedAppts"
            :icon="CheckCircle"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.pending')"
            :value="dash.pendingAppts"
            :icon="AlertCircle"
            :isAlert="dash.pendingAppts > 0"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.active_telemed')"
            :value="dash.activeTelemed"
            :icon="Video"
            :loading="dash.loading"
          />
          <MiniStatCard
            :label="$t('dashboard.new_patients')"
            :value="dash.newPatientsMonth"
            :icon="Clock"
            :trend="$t('dashboard.this_month')"
            trendColor="text-blue-500"
            :loading="dash.loading"
          />
        </section>

        <!-- Daily Appointment Trend + Dept Performance -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

          <!-- Daily Appointment Trend (bar overview) -->
          <div class="md:col-span-2 bg-white dark:bg-slate-800 p-5 rounded-xl border border-gray-100 dark:border-slate-700 shadow-sm flex flex-col min-h-[260px]">
            <h2 class="text-base font-bold text-gray-800 dark:text-slate-100 mb-4">
              Daily Appointment Trend
            </h2>

            <div v-if="dash.loading" class="flex-1 flex items-center justify-center">
              <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div v-else class="flex-1 flex flex-col justify-center gap-y-4 px-2">
              <!-- Completed -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 dark:text-slate-400 mb-1.5">
                  <span>{{ $t('dashboard.completed') }}</span>
                  <span class="text-emerald-600 font-bold">{{ dash.completedAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                  <div
                    class="bg-emerald-500 h-full rounded-full transition-all duration-700"
                    :style="{ width: completedPct + '%' }"
                  />
                </div>
              </div>

              <!-- Pending -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 dark:text-slate-400 mb-1.5">
                  <span>{{ $t('dashboard.pending') }}</span>
                  <span class="text-amber-600 font-bold">{{ dash.pendingAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                  <div
                    class="bg-amber-400 h-full rounded-full transition-all duration-700"
                    :style="{ width: pendingPct + '%' }"
                  />
                </div>
              </div>

              <!-- Cancelled -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 dark:text-slate-400 mb-1.5">
                  <span>{{ $t('dashboard.cancelled') }}</span>
                  <span class="text-red-500 font-bold">{{ cancelledAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                  <div
                    class="bg-red-400 h-full rounded-full transition-all duration-700"
                    :style="{ width: cancelledPct + '%' }"
                  />
                </div>
              </div>

              <p class="text-[11px] text-gray-400 font-medium text-right">
                Total: <span class="font-bold text-gray-600 dark:text-slate-300">{{ dash.totalAppointments }}</span>
              </p>
            </div>
          </div>

          <!-- Dept Performance -->
          <DeptPerformance />
        </div>

        <!-- Live Queue Performance table -->
        <LiveQueueTable />
      </div>

      <!-- ── RIGHT SIDEBAR (1/4) ─────────────────────────────────────── -->
      <div class="flex flex-col gap-y-5">
        <QuickActionList />
        <RecentActivityList />
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import {
  Download,
  Users,
  Stethoscope,
  Building2,
  CalendarCheck,
  CalendarDays,
  CheckCircle,
  AlertCircle,
  Video,
  Clock,
} from "lucide-vue-next";

import MiniStatCard    from "../../components/hospitaldashboard/MiniStatCard.vue";
import DeptPerformance from "../../components/hospitaldashboard/DeptPerformance.vue";
import LiveQueueTable  from "../../components/hospitaldashboard/LiveQueueTable.vue";
import QuickActionList from "../../components/hospitaldashboard/QuickActionList.vue";
import RecentActivityList from "../../components/hospitaldashboard/RecentActivityList.vue";

import { useHospitalDashboardStore } from "../../stores/hospitalDashboardStore";
import reportApi from "../../api/reportApi";

const { t } = useI18n();
const dash  = useHospitalDashboardStore();

// ── Today label ──────────────────────────────────────────────────────────────
const todayLabel = computed(() => {
  return new Date().toLocaleDateString("en-GB", {
    day: "numeric", month: "short", year: "numeric",
  });
});

// ── Appointment % bars ────────────────────────────────────────────────────────
const cancelledAppts = computed(() =>
  dash.appointmentReport?.cancelled_appointments ?? 0
);

const completedPct = computed(() => {
  const total = Math.max(dash.totalAppointments, 1);
  return Math.round((dash.completedAppts / total) * 100);
});
const pendingPct = computed(() => {
  const total = Math.max(dash.totalAppointments, 1);
  return Math.round((dash.pendingAppts / total) * 100);
});
const cancelledPct = computed(() => {
  const total = Math.max(dash.totalAppointments, 1);
  return Math.round((cancelledAppts.value / total) * 100);
});

const newPatientsLabel = computed(() =>
  dash.newPatientsMonth
    ? `+${dash.newPatientsMonth} ${t("dashboard.this_month")}`
    : undefined
);

// ── Export ────────────────────────────────────────────────────────────────────
async function exportReport() {
  try {
    await reportApi.exportPdf("appointment");
  } catch {
    // silent fallback
  }
}

onMounted(() => {
  dash.fetchAll();
});
</script>
