<template>
  <div
    class="min-h-screen bg-[#F8FAFC] dark:bg-[#0f172a] p-4 sm:p-6 font-sans antialiased text-slate-600 dark:text-slate-300 selection:bg-blue-500/10"
  >
    <!-- Grid Canvas Workspace Layout split maps -->
    <div
      class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-3rem)] min-h-[680px]"
    >
      <!-- Video Live Stream workspace box wrapper layout (8 Columns) -->
      <div
        class="lg:col-span-8 flex flex-col justify-between bg-[#1E293B] rounded-2xl overflow-hidden shadow-xl border border-slate-800"
      >
        <VideoCallWorkspace />
        <CallBottomControls />
      </div>

      <!-- Right Records Context Information Side Dock columns layer (4 Columns) -->
      <div class="lg:col-span-4 h-full flex flex-col justify-between">
        <PatientSummarySidebar />
      </div>
    </div>
  </div>
</template>

<script setup>
import VideoCallWorkspace from "../../components/telehealth/doctor/VideoCallWorkspace.vue";
import CallBottomControls from "../../components/telehealth/doctor/CallBottomControls.vue";
import PatientSummarySidebar from "../../components/telehealth/doctor/PatientSummarySidebar.vue";
</script>

<style>
.custom-scroll::-webkit-scrollbar {
  width: 4px;
}
.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 99px;
}
</style>
