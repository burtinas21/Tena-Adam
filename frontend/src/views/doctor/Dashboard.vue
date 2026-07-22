<template>
  <main class="flex-1 bg-[#F8FAFC] dark:bg-[#0f172a] p-6 overflow-y-auto font-sans dark:text-slate-200">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Welcome back, {{ dash.doctorName }}
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Here is your overview for {{ dash.todayDate }}.
        </p>
      </div>
      <div class="flex items-center gap-x-3">
        <button
          class="border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm"
          :class="{ 'opacity-50 cursor-not-allowed': dash.loading }"
          @click="dash.fetchAll()"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': dash.loading }" />
          Refresh
        </button>
        <RouterLink
          to="/doctor/medicalencounter"
          class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm"
        >
          <Plus class="w-4 h-4" />
          New Encounter
        </RouterLink>
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
      <!-- Left 3/4 -->
      <div class="xl:col-span-3 flex flex-col gap-y-6">
        <!-- Stat cards -->
        <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
          <DoctorStatCard
            label="Today's Appts"
            :value="dash.todayAppointmentsCount"
            :icon="CalendarDays"
            :loading="dash.loading"
          />
          <DoctorStatCard
            label="Waiting"
            :value="dash.waitingCount"
            :icon="Users2"
            :isAlert="dash.waitingCount > 5"
            :loading="dash.loading"
          />
          <DoctorStatCard
            label="Completed"
            :value="dash.completedTodayCount"
            :icon="CheckCircle2"
            :loading="dash.loading"
          />
          <DoctorStatCard
            label="Telemedicine"
            :value="dash.telemed"
            :icon="Video"
            :loading="dash.loading"
          />
          <DoctorStatCard
            label="Pending"
            :value="dash.pendingFollowUp"
            :icon="Clock"
            :loading="dash.loading"
          />
          <DoctorStatCard
            label="Notifications"
            :value="dash.unreadNotifications"
            :icon="Bell"
            :isAlert="dash.unreadNotifications > 0"
            :loading="dash.loading"
          />
        </section>

        <!-- Schedule timeline -->
        <ScheduleTimeline />

        <!-- Patient queue -->
        <PatientQueueTable />
      </div>

      <!-- Right column -->
      <div class="flex flex-col gap-y-6">
        <DoctorQuickActions />
        <NotificationsAlertList />
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted } from "vue";
import { RouterLink } from "vue-router";
import {
  Plus, RefreshCw, AlertCircle,
  CalendarDays, Users2, CheckCircle2,
  Video, Clock, Bell,
} from "lucide-vue-next";

import DoctorStatCard         from "../../components/doctordashboard/DoctorStatCard.vue";
import ScheduleTimeline       from "../../components/doctordashboard/ScheduleTimeline.vue";
import PatientQueueTable      from "../../components/doctordashboard/PatientQueueTable.vue";
import DoctorQuickActions     from "../../components/doctordashboard/DoctorQuickActions.vue";
import NotificationsAlertList from "../../components/doctordashboard/NotificationsAlertList.vue";

import { useDoctorDashboardStore } from "../../stores/doctorDashboardStore";

const dash = useDoctorDashboardStore();

onMounted(() => {
  dash.fetchAll();
});
</script>
