<template>
  <!-- Background decorative elements -->
  <div
    class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/3 -translate-y-1/3"
  ></div>
  <div
    class="absolute bottom-0 left-0 w-96 h-96 bg-sky-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform -translate-x-1/3 translate-y-1/3"
  ></div>

  <!-- Main Content Container -->
  <div class="max-w-xl space-y-6 relative z-10">
    <h2 class="text-4xl font-bold tracking-tight leading-tight">
      Empowering Healthcare Across Ethiopia
    </h2>
    <p class="text-blue-100 text-sm leading-relaxed font-light">
      A centralized platform for patients, doctors, and hospitals to manage
      appointments, EMR, and telehealth sessions with enterprise-grade security.
    </p>

    <!-- Features Grid -->
    <div class="grid grid-cols-2 gap-4 pt-4">
      <!-- Card 1: Online Appointment Booking -->
      <div
        class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl p-4 flex flex-col justify-between gap-6 hover:bg-white/15 transition-colors"
      >
        <div
          class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"
        >
          <!-- Calendar Icon -->
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <span class="text-xs font-semibold tracking-wide"
          >Online Appointment Booking</span
        >
      </div>

      <!-- Card 2: Real-time Queue Management -->
      <div
        class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl p-4 flex flex-col justify-between gap-6 hover:bg-white/15 transition-colors"
      >
        <div
          class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"
        >
          <!-- Users/Queue Icon -->
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <span class="text-xs font-semibold tracking-wide"
          >Real-time Queue Management</span
        >
      </div>

      <!-- Card 3: Secure Telehealth Consultations -->
      <div
        class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl p-4 flex flex-col justify-between gap-6 hover:bg-white/15 transition-colors"
      >
        <div
          class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"
        >
          <!-- Video Icon -->
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
        </div>
        <span class="text-xs font-semibold tracking-wide"
          >Secure Telehealth Consultations</span
        >
      </div>
      <div
        class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl p-4 flex flex-col justify-between gap-6 hover:bg-white/15 transition-colors"
      >
        <div
          class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center"
        >
       
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
          </svg>
        </div>
        <span class="text-xs font-semibold tracking-wide"
          >Electronic Medical Records (EMR)</span
        >
      </div>
    </div>
  </div>
  <div
    class="text-[11px] text-blue-200 flex items-center gap-1.5 border-t border-white/10 pt-10 relative z-10 mt-20"
  >
    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
      <path
        fill-rule="evenodd"
        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
        clip-rule="evenodd"
      />
    </svg>
    <span
      >Privacy and Security: Your data is encrypted and secure. HIPAA Compliant.
    </span>
  </div>
</template>
