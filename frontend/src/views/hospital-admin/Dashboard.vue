<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Dashboard Overview</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Real-time hospital operations and performance metrics.
        </p>
      </div>
      <div class="flex items-center gap-x-3">
        <button
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
          @click="dash.fetchAll()"
        >
          <RefreshCw class="w-3.5 h-3.5 text-gray-400" :class="{ 'animate-spin': dash.loading }" />
          Refresh
        </button>
        <button
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
          @click="exportReport"
        >
          <Download class="w-3.5 h-3.5 text-white/90" />
          Export Report
        </button>
      </div>
    </div>

    <!-- Error banner -->
    <div
      v-if="dash.error"
      class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center gap-x-2 text-xs text-red-700 font-medium"
    >
      <AlertCircle class="w-4 h-4 shrink-0" />
      {{ dash.error }}
      <button class="ml-auto underline font-bold" @click="dash.fetchAll()">Retry</button>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
      <!-- Left column (3/4 width) -->
      <div class="xl:col-span-3 flex flex-col gap-y-6">

        <!-- Stat cards grid -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <MiniStatCard
            label="Total Patients"
            :value="dash.totalPatients"
            :icon="Users"
            :trend="newPatientsLabel"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Total Doctors"
            :value="dash.totalDoctors"
            :icon="Stethoscope"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Departments"
            :value="dash.totalDepartments"
            :icon="Building2"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Total Appts"
            :value="dash.totalAppointments"
            :icon="CalendarCheck"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Completed"
            :value="dash.completedAppts"
            :icon="CheckCircle"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Pending"
            :value="dash.pendingAppts"
            :icon="AlertCircle"
            :isAlert="dash.pendingAppts > 0"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="Active Telemed"
            :value="dash.activeTelemed"
            :icon="Video"
            :loading="dash.loading"
          />
          <MiniStatCard
            label="New Patients"
            :value="dash.newPatientsMonth"
            :icon="Clock"
            trend="This month"
            trendColor="text-blue-500"
            :loading="dash.loading"
          />
        </section>

        <!-- Charts row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Appointment trend chart -->
          <div class="md:col-span-2 bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col min-h-[260px]">
            <h2 class="text-base font-bold text-gray-800 mb-2">Appointment Overview</h2>

            <div v-if="dash.loading" class="flex-1 flex items-center justify-center">
              <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div v-else class="flex-1 flex flex-col justify-center gap-y-4 px-2">
              <!-- Completed bar -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1">
                  <span>Completed</span>
                  <span class="text-emerald-600 font-bold">{{ dash.completedAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                  <div
                    class="bg-emerald-500 h-full rounded-full transition-all duration-700"
                    :style="{ width: completedPct + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Pending bar -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1">
                  <span>Pending</span>
                  <span class="text-amber-600 font-bold">{{ dash.pendingAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                  <div
                    class="bg-amber-400 h-full rounded-full transition-all duration-700"
                    :style="{ width: pendingPct + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Cancelled bar -->
              <div>
                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1">
                  <span>Cancelled</span>
                  <span class="text-red-500 font-bold">{{ cancelledAppts }}</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                  <div
                    class="bg-red-400 h-full rounded-full transition-all duration-700"
                    :style="{ width: cancelledPct + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Total label -->
              <p class="text-[11px] text-gray-400 font-medium text-right">
                Total: <span class="font-bold text-gray-600">{{ dash.totalAppointments }}</span> appointments
              </p>
            </div>
          </div>

          <!-- Dept performance -->
          <DeptPerformance />
        </div>

        <!-- Doctor workload table -->
        <QueueTable />
      </div>

      <!-- Right column -->
      <div class="flex flex-col gap-y-6">
        <QuickActionList />
        <RecentActivityList />
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted } from "vue";
import {
  RefreshCw,
  Download,
  Users,
  Stethoscope,
  Building2,
  CalendarCheck,
  CheckCircle,
  AlertCircle,
  Video,
  Clock,
} from "lucide-vue-next";

import MiniStatCard       from "../../components/hospitaldashboard/MiniStatCard.vue";
import RecentActivityList from "../../components/hospitaldashboard/RecentActivityList.vue";
import QuickActionList    from "../../components/hospitaldashboard/QuickActionList.vue";
import DeptPerformance    from "../../components/hospitaldashboard/DeptPerformance.vue";
import QueueTable         from "../../components/hospitaldashboard/QueueTable.vue";

import { useHospitalDashboardStore } from "../../stores/hospitalDashboardStore";
import reportApi from "../../api/reportApi";

const dash = useHospitalDashboardStore();

// ── Computed values ──────────────────────────────────────────────────────────
const cancelledAppts = computed(() =>
  (dash.appointmentReport?.cancelled_appointments) ?? 0
);

const completedPct = computed(() => {
  const t = Math.max(dash.totalAppointments, 1);
  return Math.round((dash.completedAppts / t) * 100);
});

const pendingPct = computed(() => {
  const t = Math.max(dash.totalAppointments, 1);
  return Math.round((dash.pendingAppts / t) * 100);
});

const cancelledPct = computed(() => {
  const t = Math.max(dash.totalAppointments, 1);
  return Math.round((cancelledAppts.value / t) * 100);
});

const newPatientsLabel = computed(() =>
  dash.newPatientsMonth ? `+${dash.newPatientsMonth} this month` : undefined
);

// ── Export ───────────────────────────────────────────────────────────────────
async function exportReport() {
  try {
    await reportApi.exportPdf("appointment");
  } catch {
    // fallback: just download what's visible
  }
}

// ── Init ─────────────────────────────────────────────────────────────────────
onMounted(() => {
  dash.fetchAll();
});
</script>
