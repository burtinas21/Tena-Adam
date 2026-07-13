<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <!-- Top Action Row Header Area -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Dashboard Overview
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Real-time hospital operations and performance metrics.
        </p>
      </div>
      <div class="flex items-center gap-x-3">
        <button
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
        >
          <Calendar class="w-3.5 h-3.5 text-gray-400" />
          Today
        </button>
        <button
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-2 transition shadow-sm"
        >
          <Download class="w-3.5 h-3.5 text-white/90" />
          Export Report
        </button>
      </div>
    </div>

    <!-- Layout Grid Partitioning Container -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
      <!-- Left Dashboard Column Matrix (Takes 3 columns on desktop wide displays) -->
      <div class="xl:col-span-3 flex flex-col gap-y-6">
        <!-- Nested Grid Map for Metric Stat Cards -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <MiniStatCard
            label="Total Patients"
            value="12,450"
            :icon="Users"
            trend="↑ 12%"
          />
          <MiniStatCard label="Total Doctors" value="84" :icon="Stethoscope" />
          <MiniStatCard label="Departments" value="16" :icon="Building2" />
          <MiniStatCard
            label="Today's Appts"
            value="342"
            :icon="CalendarCheck"
          />
          <MiniStatCard
            label="Completed Today"
            value="215"
            :icon="CheckCircle"
          />
          <MiniStatCard
            label="Waiting Now"
            value="48"
            :icon="AlertCircle"
            :isAlert="true"
          />
          <MiniStatCard label="Active Telemed" value="12" :icon="Video" />
          <MiniStatCard
            label="Avg Wait Time"
            value="18m"
            :icon="Clock"
            trend="↓ 5m"
            trendColor="text-emerald-500"
          />
        </section>

        <!-- Graphic Charts & Breakdown Layout Rows -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Main Daily Trends Placeholder Component (Takes up 2 slots wide) -->
          <div
            class="md:col-span-2 bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col min-h-[260px]"
          >
            <h2 class="text-base font-bold text-gray-800 mb-2">
              Daily Appointment Trend
            </h2>
            <div
              class="flex-1 border border-dashed border-gray-200 bg-gray-50/50 rounded-lg flex items-center justify-center"
            >
              <span
                class="text-xs font-semibold text-gray-400 tracking-wide uppercase"
                >Patient Flow Chart Area</span
              >
            </div>
          </div>
        
          <DeptPerformance />
        </div>

        <!-- Live Queue Performance Content Data Grid Table component row -->
        <QueueTable />
      </div>

      <!-- Right Column Widgets Layout Menu Stack -->
      <div class="flex flex-col gap-y-6">
        <QuickActionList />
        <RecentActivityList />
      </div>
    </div>
  </main>
</template>

<script setup>
import {
  Calendar,
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

// Core Local Child Dashboard Component Files Imports
import MiniStatCard from "../../components/hospitaldashboard/QuickActionList.vue";
import RecentActivityList from "../../components/hospitaldashboard/MiniStatCard.vue";
import QuickActionList from "../../components/hospitaldashboard/RecentActivityList.vue";
import DeptPerformance from "../../components/hospitaldashboard/DeptPerformance.vue";
import QueueTable from "../../components/hospitaldashboard/QueueTable.vue";
</script>
