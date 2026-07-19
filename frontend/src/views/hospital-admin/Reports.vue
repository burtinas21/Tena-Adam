<template>
  <div class="min-h-screen bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600">
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200/50">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reports & Analytics</h1>
          <p class="text-xs text-slate-500 font-medium tracking-wide mt-1">
            Monitor hospital performance and operational insights
          </p>
        </div>

        <!-- Tab switcher -->
        <div class="flex items-center gap-2 shrink-0">
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
        <!-- KPI Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Patient stats -->
          <AnalyticsMetricCard
            title="Total Patients"
            :value="fmt(store.patientStats?.total_patients)"
            :trendValue="'+' + fmt(store.patientStats?.new_patients_this_month) + ' this month'"
            trendType="up"
            :loading="store.loading"
          />
          <AnalyticsMetricCard
            title="Active Patients"
            :value="fmt(store.patientStats?.active_patients)"
            :trendValue="fmt(store.patientStats?.inactive_patients) + ' inactive'"
            trendType="neutral"
            :loading="store.loading"
          />
          <!-- Appointment stats -->
          <AnalyticsMetricCard
            title="Total Appointments"
            :value="fmt(store.appointmentReport?.total_appointments)"
            :trendValue="fmt(store.appointmentReport?.today_appointments) + ' today'"
            trendType="up"
            :loading="store.loading"
          />
          <AnalyticsMetricCard
            title="Completed Consultations"
            :value="fmt(store.appointmentReport?.completed_appointments)"
            :trendValue="completionRate + '% completion rate'"
            trendType="up"
            :loading="store.loading"
          />
          <AnalyticsMetricCard
            title="Telemedicine Sessions"
            :value="fmt(store.telehealthStats?.completed_sessions)"
            :trendValue="fmt(store.telehealthStats?.total_sessions) + ' total sessions'"
            trendType="up"
            :loading="store.loading"
          />
          <AnalyticsMetricCard
            title="Cancelled Appointments"
            :value="fmt(store.appointmentReport?.cancelled_appointments)"
            :trendValue="cancellationRate + '% cancellation rate'"
            trendType="down"
            :loading="store.loading"
          />
        </div>

        <!-- Charts row -->
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
import { AlertCircle } from 'lucide-vue-next';
import { useReportStore } from '../../stores/reportStore';

import AnalyticsMetricCard from '../../components/reports/hospital-admin/AnalyticsMetricCard.vue';
import PatientGrowthTrendChart from '../../components/reports/hospital-admin/PatientGrowthTrendChart.vue';
import AppointmentStatusChart from '../../components/reports/hospital-admin/AppointmentStatusChart.vue';
import DepartmentPerformanceTable from '../../components/reports/hospital-admin/DepartmentPerformanceTable.vue';
import DoctorWorkloadTable from '../../components/reports/hospital-admin/DoctorWorkloadTable.vue';
import ExportButtons from '../../components/reports/hospital-admin/ExportButtons.vue';

const store = useReportStore();

const tabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'departments', label: 'Departments' },
  { key: 'doctors', label: 'Doctors' },
];
const activeTab = ref('overview');

// Load data by active tab — always re-fetch, never use stale data from another session
watch(activeTab, async (tab) => {
  if (tab === 'departments') {
    await store.fetchDepartmentPerformance();
  }
  if (tab === 'doctors') {
    await store.fetchDoctorWorkload();
  }
}, { immediate: false });

onMounted(() => {
  // Reset first so we never show data scoped to a different hospital/role
  store.$reset();
  store.fetchAll();
});

// Computed helpers
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
