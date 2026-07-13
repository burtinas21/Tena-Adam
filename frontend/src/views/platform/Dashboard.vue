<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <!-- Dashboard Section Header Title and Top Actions -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Overview of Smart Care platform metrics.
        </p>
      </div>
      <!-- Upper Panel Action Trigger Links -->
      <div class="flex items-center gap-x-3">
        <button
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
        >
          <Download class="w-3.5 h-3.5 text-gray-500" />
          Export Report
        </button>
        <button
          class="bg-[#004795] hover:bg-[#003670] text-white font-semibold text-xs py-2 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm"
        >
          <Plus class="w-4 h-4 text-white" />
          New Action
        </button>
      </div>
    </div>

    <!-- Grid Container Row for Stat Cards -->
    <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
      <StatCard
        title="Total Hospitals"
        value="24"
        trend="12%"
        :icon="SquarePlus"
        iconColor="text-[#2563EB]"
        iconBgColor="bg-[#EFF6FF]"
      />
      <StatCard
        title="Active Hospitals"
        value="18"
        trend="5%"
        :icon="CheckCircle"
        iconColor="text-[#059669]"
        iconBgColor="bg-[#ECFDF5]"
      />
      <StatCard
        title="Total Providers"
        value="1,240"
        trend="8%"
        :icon="HeartPulse"
        iconColor="text-[#0284C7]"
        iconBgColor="bg-[#F0F9FF]"
      />
      <StatCard
        title="Total Patients"
        value="45.2k"
        trend="15%"
        :icon="Users"
        iconColor="text-[#4F46E5]"
        iconBgColor="bg-[#EEF2FF]"
      />
      <StatCard
        title="Total Appointments"
        value="128k"
        trend="22%"
        :icon="Calendar"
        iconColor="text-[#9333EA]"
        iconBgColor="bg-[#FBF7FF]"
      />
      <StatCard
        title="System Users"
        value="1.5k"
        trend="0%"
        :icon="ShieldCheck"
        iconColor="text-[#0D9488]"
        iconBgColor="bg-[#F0FDFA]"
      />
    </section>

    <!-- Lower Split Feature Section (Charts and Widgets Side-by-Side) -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Growth Chart Panel takes up 2 columns -->
      <div class="lg:col-span-2">
        <GrowthChart />
      </div>
      <!-- Widget Stack Panel takes up 1 column -->
      <div class="flex flex-col gap-y-6">
        <QuickActions />
        <RecentActivity />
      </div>
    </section>
  </main>
</template>

<script setup>
import {
  Download,
  Plus,
  SquarePlus,
  CheckCircle,
  HeartPulse,
  Users,
  Calendar,
  ShieldCheck,
} from "lucide-vue-next";

// Import separate modular components
import StatCard from "../../components/platformdashboard/StatCard.vue";
import GrowthChart from "../../components/platformdashboard/GrowthChart.vue";
import QuickActions from "../../components/platformdashboard/QuickActions.vue";
import RecentActivity from "../../components/platformdashboard/RecentActivity.vue";
</script>
