<template>
  <div
    class="min-h-screen bg-[#F8FAFC] p-6 lg:p-8 font-sans antialiased text-gray-600"
  >
    <div
      class="max-w-[1440px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-4rem)] min-h-[640px]"
    >
      <!-- Left 8 Columns: Live Video Stream Wrapper -->
      <div
        class="lg:col-span-8 relative bg-slate-950 rounded-2xl overflow-hidden shadow-xl border border-slate-900 flex flex-col justify-between"
      >
        <!-- Top Stream Bar Overlays -->
        <div
          class="absolute top-5 left-5 right-5 z-20 flex items-center justify-between pointer-events-none"
        >
          <!-- Timer Badge -->
          <div
            class="flex items-center space-x-2 px-3 py-1.5 bg-slate-900/80 backdrop-blur-md rounded-xl border border-slate-800 shadow-md"
          >
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
            <span class="text-xs font-bold text-white tracking-wider font-mono"
              >00:14:23</span
            >
          </div>

          <!-- Connection Stats Status Badge -->
          <div
            class="p-2 bg-slate-900/80 backdrop-blur-md rounded-xl border border-slate-800 shadow-md"
          >
            <Signal class="w-4 h-4 text-emerald-400" />
          </div>
        </div>

        <!-- Simulated Main Stream (Doctor Feed Image Placeholders) -->
        <div class="absolute inset-0 w-full h-full">
          <img
            src="https://unsplash.com"
            alt="Doctor Video Feed"
            class="w-full h-full object-cover filter brightness-[0.92] contrast-[1.02]"
          />
        </div>

        <!-- Video Overlay Call Action Controls (Dynamic bar component) -->
        <VideoOverlayControls />

        <!-- Picture-in-Picture: User Feed Box inside Stream Area -->
        <div
          class="absolute bottom-5 right-5 w-40 h-52 bg-slate-900 rounded-2xl overflow-hidden border-2 border-slate-800/80 shadow-2xl z-20"
        >
          <img
            src="https://unsplash.com"
            alt="User Video Feed"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute bottom-2 left-2.5 px-1.5 py-0.5 bg-slate-900/70 backdrop-blur-sm rounded-md border border-slate-800"
          >
            <span class="text-[9px] font-bold text-slate-300 tracking-wider"
              >You</span
            >
          </div>
        </div>
      </div>

      <!-- Right 4 Columns: Medical Sidebar Panel Records Container -->
      <div
        class="lg:col-span-4 flex flex-col justify-between space-y-5 h-full overflow-y-auto pr-1"
      >
        <!-- Block 1: Doctor Profile Metadata Card -->
        <DoctorProfileCard
          name="Dr. Robert Chen"
          specialty="Cardiology Specialist"
          time="10:30 AM"
          duration="30 Min"
        />

        <!-- Block 2: Interactive Records & Notes Timeline Area Box -->
        <div
          class="flex-1 bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between"
        >
          <div>
            <!-- Section Header -->
            <div
              class="flex items-center justify-between pb-4 border-b border-gray-50 mb-5"
            >
              <div class="flex items-center space-x-2">
                <FileSpreadsheet class="w-4 h-4 text-gray-500" />
                <h3 class="text-sm font-bold text-gray-900 tracking-tight">
                  Consultation Notes
                </h3>
              </div>
              <button
                class="p-1.5 hover:bg-gray-50 rounded-lg text-gray-400 hover:text-gray-600 transition"
              >
                <Download class="w-4 h-4" />
              </button>
            </div>

            <!-- Notes List Segment Feed tracking images -->
            <div class="space-y-1">
              <ConsultationNoteItem
                title="Symptoms Review"
                content="Patient reports mild chest discomfort during exertion over the last 3 days. No shortness of breath at rest."
                timestamp="10:35 AM"
                :icon="Activity"
                :hasMenu="true"
              />
              <ConsultationNoteItem
                title="Medication Adjustment"
                content="Increasing Lisinopril dosage to 20mg daily. Continue current statin regimen."
                timestamp="10:42 AM"
                :icon="Pill"
              />
              <ConsultationNoteItem
                title="Next Steps"
                content="Schedule follow-up stress test within 2 weeks. Lab work requisition sent to portal."
                timestamp="10:48 AM"
                :icon="Calendar"
              />
            </div>
          </div>

          <!-- Bottom Refill Persistent Action Button Segment -->
          <button
            class="w-full mt-4 flex items-center justify-center space-x-2 py-3 bg-[#0252D7] text-white text-xs font-semibold rounded-xl hover:bg-blue-700 shadow-md shadow-blue-600/10 active:scale-[0.99] transition duration-150"
          >
            <ClipboardCheck class="w-4 h-4" />
            <span>Request Prescription Refill</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import VideoOverlayControls from "../../components/telehealth/VideoOverlayControls.vue";
import DoctorProfileCard from "../../components/telehealth/DoctorProfileCard.vue";
import ConsultationNoteItem from "../../components/telehealth/ConsultationNoteItem.vue";
import {
  Signal,
  FileSpreadsheet,
  Download,
  Activity,
  Pill,
  Calendar,
  ClipboardCheck,
} from "lucide-vue-next";
</script>
