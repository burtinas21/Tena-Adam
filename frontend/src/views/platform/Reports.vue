<template>
  <div class="min-h-screen bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600">
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200/50">
        <div>
          <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Platform Reports</h1>
          <p class="text-xs text-slate-400 font-medium tracking-wide mt-0.5">
            System-wide healthcare statistics and export center
          </p>
        </div>

        <!-- Tab switcher -->
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'px-4 py-2 text-xs font-bold rounded-lg transition',
              activeTab === tab.key
                ? 'bg-[#004795] text-white shadow-sm'
                : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50',
            ]"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Error banner -->
      <div
        v-if="store.error"
        class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg px-4 py-3"
      >
        <AlertCircle class="w-4 h-4 flex-shrink-0" />
        {{ store.error }}
      </div>

      <!-- ── OVERVIEW TAB ─────────────────────────────────────────────────── -->
      <template v-if="activeTab === 'overview'">

        <!-- KPI grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <PlatformReportCard
            title="Total Patients"
            :value="fmt(store.patientStats?.total_patients)"
            :sub="fmt(store.patientStats?.new_patients_this_month) + ' new this month'"
            :icon="Users2"
            color="blue"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Total Appointments"
            :value="fmt(store.appointmentReport?.total_appointments)"
            :sub="fmt(store.appointmentReport?.today_appointments) + ' today'"
            :icon="CalendarCheck2"
            color="emerald"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Completed Consultations"
            :value="fmt(store.appointmentReport?.completed_appointments)"
            :sub="completionRate + '% completion rate'"
            :icon="CheckCircle2"
            color="green"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Telehealth Sessions"
            :value="fmt(store.telehealthStats?.total_sessions)"
            :sub="fmt(store.telehealthStats?.completed_sessions) + ' completed'"
            :icon="Video"
            color="purple"
            :loading="store.loading"
          />
        </div>

        <!-- Trend chart + appointment donut -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
          <div class="lg:col-span-8">
            <PatientGrowthTrendChart
              class="h-full"
              :labels="store.trendLabels"
              :patient-data="store.trendPatientData"
              :appt-data="store.trendAppointmentData"
              :loading="store.loading"
              :export-loading="store.exportLoading"
              @export="handleExport('trend', $event)"
            />
          </div>
          <div class="lg:col-span-4">
            <AppointmentStatusChart
              class="h-full"
              :data="apptChartData"
              :loading="store.loading"
            />
          </div>
        </div>

        <!-- Telehealth stats panel -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <PlatformReportCard
            title="Scheduled Sessions"
            :value="fmt(store.telehealthStats?.scheduled_sessions)"
            sub="Awaiting start"
            :icon="Clock"
            color="amber"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Active Sessions"
            :value="fmt(store.telehealthStats?.active_sessions)"
            sub="Currently live"
            :icon="Radio"
            color="red"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Cancelled Sessions"
            :value="fmt(store.telehealthStats?.cancelled_sessions)"
            sub="Telehealth cancellations"
            :icon="XCircle"
            color="slate"
            :loading="store.loading"
          />
          <PlatformReportCard
            title="Cancelled Appointments"
            :value="fmt(store.appointmentReport?.cancelled_appointments)"
            :sub="cancellationRate + '% of total'"
            :icon="XCircle"
            color="rose"
            :loading="store.loading"
          />
        </div>
      </template>

      <!-- ── DEPARTMENTS TAB ─────────────────────────────────────────────── -->
      <template v-if="activeTab === 'departments'">
        <DepartmentPerformanceTable
          :departments="store.departmentPerformance"
          :loading="store.loading"
        >
          <template #actions>
            <ExportButtons
              :loading="store.exportLoading"
              @export="handleExport('department', $event)"
            />
          </template>
        </DepartmentPerformanceTable>
      </template>

      <!-- ── DOCTORS TAB ──────────────────────────────────────────────────── -->
      <template v-if="activeTab === 'doctors'">
        <DoctorWorkloadTable
          :doctors="store.doctorWorkload"
          :loading="store.loading"
        >
          <template #actions>
            <ExportButtons
              :loading="store.exportLoading"
              @export="handleExport('doctor', $event)"
            />
          </template>
        </DoctorWorkloadTable>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import {
  AlertCircle, Users2, CalendarCheck2, CheckCircle2,
  Video, Clock, Radio, XCircle,
} from 'lucide-vue-next';
import { useReportStore } from '../../stores/reportStore';

import PatientGrowthTrendChart from '../../components/reports/hospital-admin/PatientGrowthTrendChart.vue';
import AppointmentStatusChart from '../../components/reports/hospital-admin/AppointmentStatusChart.vue';
import DepartmentPerformanceTable from '../../components/reports/hospital-admin/DepartmentPerformanceTable.vue';
import DoctorWorkloadTable from '../../components/reports/hospital-admin/DoctorWorkloadTable.vue';
import ExportButtons from '../../components/reports/hospital-admin/ExportButtons.vue';
import PlatformReportCard from '../../components/reports/platform/PlatformReportCard.vue';

const store = useReportStore();

const tabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'departments', label: 'Departments' },
  { key: 'doctors', label: 'Doctors' },
];
const activeTab = ref('overview');

watch(activeTab, async (tab) => {
  if (tab === 'departments') {
    await store.fetchDepartmentPerformance();
  }
  if (tab === 'doctors') {
    await store.fetchDoctorWorkload();
  }
});

onMounted(() => {
  // Always reset and re-fetch — platform admin must see all data fresh,
  // not stale hospital-scoped data cached from a previous hospital-admin session.
  store.$reset();
  store.fetchAll();
});

function fmt(val) {
  if (val === null || val === undefined) return '—';
  return Number(val).toLocaleString();
}

const completionRate = computed(() => {
  const r = store.appointmentReport;
  if (!r || !r.total_appointments) return 0;
  return Math.round((r.completed_appointments / r.total_appointments) * 100);
});

const cancellationRate = computed(() => {
  const r = store.appointmentReport;
  if (!r || !r.total_appointments) return 0;
  return Math.round((r.cancelled_appointments / r.total_appointments) * 100);
});

const apptChartData = computed(() => {
  const r = store.appointmentReport;
  if (!r) return null;
  return {
    total: r.total_appointments,
    completed: r.completed_appointments,
    pending: r.pending_appointments,
    cancelled: r.cancelled_appointments,
    today: r.today_appointments,
  };
});

async function handleExport(type, format) {
  if (format === 'excel') await store.exportExcel(type);
  else if (format === 'csv') await store.exportCsv(type);
  else if (format === 'pdf') await store.exportPdf(type);
}
</script>
