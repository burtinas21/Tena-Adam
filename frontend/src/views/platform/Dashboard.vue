<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Overview of Smart Care platform metrics.
        </p>
      </div>
      <div class="flex items-center gap-x-3">
        <button
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
          :class="{ 'opacity-50': store.loading }"
          @click="store.fetchAll()"
        >
          <RefreshCw class="w-3.5 h-3.5 text-gray-500" :class="{ 'animate-spin': store.loading }" />
          Refresh
        </button>
        <RouterLink
          to="/platform/reports"
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
        >
          <Download class="w-3.5 h-3.5 text-gray-500" />
          Export Report
        </RouterLink>
        <RouterLink
          to="/platform/hospitalnetwork"
          class="bg-[#004795] hover:bg-[#003670] text-white font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm"
        >
          <Plus class="w-4 h-4 text-white" />
          New Action
        </RouterLink>
      </div>
    </div>

    <!-- Error banner -->
    <div
      v-if="store.error"
      class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center gap-x-2 text-xs text-red-700 font-medium"
    >
      <AlertCircle class="w-4 h-4 shrink-0" />
      {{ store.error }}
      <button class="ml-auto underline font-bold" @click="store.fetchAll()">Retry</button>
    </div>

    <!-- Stat cards row -->
    <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
      <StatCard
        title="Total Hospitals"
        :value="fmt(store.totalHospitals)"
        :trend="store.hospitalTrend"
        :icon="SquarePlus"
        iconColor="text-[#2563EB]"
        iconBgColor="bg-[#EFF6FF]"
        :loading="store.loading"
      />
      <StatCard
        title="Active Hospitals"
        :value="fmt(store.activeHospitals)"
        :trend="store.activeHospitalTrend"
        :icon="CheckCircle"
        iconColor="text-[#059669]"
        iconBgColor="bg-[#ECFDF5]"
        :loading="store.loading"
      />
      <StatCard
        title="Total Providers"
        :value="fmt(store.totalProviders)"
        :trend="store.providerTrend"
        :icon="HeartPulse"
        iconColor="text-[#0284C7]"
        iconBgColor="bg-[#F0F9FF]"
        :loading="store.loading"
      />
      <StatCard
        title="Total Patients"
        :value="fmt(store.totalPatients)"
        :trend="store.patientTrend"
        :icon="Users"
        iconColor="text-[#4F46E5]"
        iconBgColor="bg-[#EEF2FF]"
        :loading="store.loading"
      />
      <StatCard
        title="Total Appointments"
        :value="fmt(store.totalAppointments)"
        :trend="store.appointmentTrend"
        :icon="Calendar"
        iconColor="text-[#9333EA]"
        iconBgColor="bg-[#FBF7FF]"
        :loading="store.loading"
      />
      <StatCard
        title="System Users"
        :value="fmt(store.systemUsers)"
        :trend="store.systemUserTrend"
        :icon="ShieldCheck"
        iconColor="text-[#0D9488]"
        iconBgColor="bg-[#F0FDFA]"
        :loading="store.loading"
      />
    </section>

    <!-- Charts + widgets -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Charts: 2/3 width -->
      <div class="lg:col-span-2">
        <GrowthChart
          :labels="store.trendLabels"
          :hospital-data="store.hospitalGrowthData"
          :patient-data="store.trendPatientData"
          :appointment-data="store.trendAppointmentData"
          :loading="store.loading"
        />
      </div>

      <!-- Side widgets: 1/3 width -->
      <div class="flex flex-col gap-y-6">
        <QuickActions />
        <RecentActivity />
      </div>
    </section>
  </main>
</template>

<script setup>
import { onMounted } from "vue";
import { RouterLink } from "vue-router";
import {
  Download, Plus, RefreshCw, AlertCircle,
  SquarePlus, CheckCircle, HeartPulse,
  Users, Calendar, ShieldCheck,
} from "lucide-vue-next";

import StatCard      from "../../components/platformdashboard/StatCard.vue";
import GrowthChart   from "../../components/platformdashboard/GrowthChart.vue";
import QuickActions  from "../../components/platformdashboard/QuickActions.vue";
import RecentActivity from "../../components/platformdashboard/RecentActivity.vue";

import { usePlatformDashboardStore, fmtCompact } from "../../stores/platformDashboardStore";

const store = usePlatformDashboardStore();

function fmt(val) {
  return fmtCompact(val);
}

onMounted(() => {
  store.fetchAll();
});
</script>
