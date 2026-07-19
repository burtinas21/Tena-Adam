<template>
  <div
    class="min-h-screen bg-[#F8FAFC] p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-600 selection:bg-blue-600/10"
  >
    <div class="max-w-[1440px] mx-auto space-y-6">

      <!-- Section 1: Heading & Filters -->
      <div
        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200/50"
      >
        <div>
          <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
            Executive Overview
          </h1>
          <p class="text-xs text-slate-400 font-medium tracking-wide mt-0.5">
            Platform performance metrics and trends.
          </p>
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

      <!-- Section 2: KPI Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <PlatformMetricCard
          title="Total Patients"
          :value="fmt(store.patientStats?.total_patients)"
          :trend="'+' + fmt(store.patientStats?.new_patients_this_month) + ' this month'"
          :icon="Users2"
        />
        <PlatformMetricCard
          title="Active Patients"
          :value="fmt(store.patientStats?.active_patients)"
          :trend="fmt(store.patientStats?.inactive_patients) + ' inactive'"
          :icon="Activity"
        />
        <PlatformMetricCard
          title="Total Appointments"
          :value="fmt(store.appointmentReport?.total_appointments)"
          :trend="fmt(store.appointmentReport?.today_appointments) + ' today'"
          :icon="CalendarCheck2"
        />
        <PlatformMetricCard
          title="Telehealth Sessions"
          :value="fmt(store.telehealthStats?.total_sessions)"
          :trend="fmt(store.telehealthStats?.completed_sessions) + ' completed'"
          :icon="Video"
        />
      </div>

      <!-- Section 3: Patient Growth + Appointment Trends -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <div class="lg:col-span-8">
          <PatientGrowthMixedChart
            :labels="store.trendLabels"
            :data="store.trendPatientData"
            :loading="store.loading"
          />
        </div>
        <div class="lg:col-span-4">
          <ApptTrendsBarChart
            :labels="store.trendLabels"
            :data="store.trendAppointmentData"
            :loading="store.loading"
          />
        </div>
      </div>

      <!-- Section 4: Department + Doctor info -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <div class="lg:col-span-8">
          <TopHospitalsTable />
        </div>
        <div class="lg:col-span-4">
          <DoctorActivityHeatmap />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { AlertCircle, Users2, Activity, CalendarCheck2, Video } from 'lucide-vue-next';
import { useReportStore } from '../../stores/reportStore';

import PlatformMetricCard from '../../components/analytics/PlatformMetricCard.vue';
import PatientGrowthMixedChart from '../../components/analytics/PatientGrowthMixedChart.vue';
import ApptTrendsBarChart from '../../components/analytics/ApptTrendsBarChart.vue';
import TopHospitalsTable from '../../components/analytics/TopHospitalsTable.vue';
import DoctorActivityHeatmap from '../../components/analytics/DoctorActivityHeatmap.vue';

const store = useReportStore();

onMounted(() => {
  store.fetchAll();
});

function fmt(val) {
  if (val === null || val === undefined) return '—';
  return Number(val).toLocaleString();
}
</script>
