<template>
  <main class="flex-1 bg-[#F8FAFC] p-6 overflow-y-auto font-sans">
    <!-- Top Action Row Header Area Layout Component Line -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-4 mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
          Welcome back, Dr. Jameson
        </h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
          Here is your overview for today, October 24th.
        </p>
      </div>
      <button
        class="bg-[#004795] hover:bg-[#003670] text-white font-bold text-xs py-2.5 px-4 rounded-lg flex items-center gap-x-1.5 transition shadow-sm self-start sm:self-auto"
      >
        <Plus class="w-4 h-4" />
        New Encounter
      </button>
    </div>

    <!-- Layout Columns Breakdown Partition Partition Grid block wrapper -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
      <!-- Primary Core Function Area (Takes up 3/4 layout spaces width on desktop views) -->
      <div class="xl:col-span-3 flex flex-col gap-y-6">
        <!-- Numerical Stat Counter Panels Section List Grid -->
        <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
          <DoctorStatCard
            label="Today's Appts"
            value="14"
            subBadge="+2"
            :icon="CalendarDays"
          />
          <DoctorStatCard
            label="Waiting"
            value="3"
            :icon="Users2"
            :isAlert="true"
          />
          <DoctorStatCard label="Completed" value="6" :icon="CheckCircle2" />
          <DoctorStatCard label="Telemedicine" value="4" :icon="Video" />
          <DoctorStatCard label="Pending F/U" value="8" :icon="Activity" />
          <DoctorStatCard label="Active Rx" value="24" :icon="Activity" />
        </section>

        <!-- Main Workspace Components Block Stack array rows layout split maps -->
        <ScheduleTimeline />
        <PatientQueueTable />
      </div>

      <!-- Right Column Controls & Sidebar Accessories Feed layout area panel -->
      <div class="flex flex-col gap-y-6">
        <DoctorQuickActions />
        <NotificationsAlertList />
      </div>
    </div>
  </main>
</template>

<script setup>
import {
  Plus,
  CalendarDays,
  Users2,
  CheckCircle2,
  Video,
  Activity,
} from "lucide-vue-next";
import DoctorStatCard from "../../components/doctordashboard/DoctorStatCard.vue";
import ScheduleTimeline from "../../components/doctordashboard/ScheduleTimeline.vue";
import PatientQueueTable from "../../components/doctordashboard/PatientQueueTable.vue";
import DoctorQuickActions from "../../components/doctordashboard/DoctorQuickActions.vue";
import NotificationsAlertList from "../../components/doctordashboard/NotificationsAlertList.vue";
</script>
