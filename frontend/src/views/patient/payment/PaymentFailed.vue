<template>
  <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
      <!-- Icon -->
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </div>

      <h1 class="text-xl font-bold text-gray-900 mb-2">Payment Failed</h1>
      <p class="text-sm text-gray-500 mb-6">
        Your payment was not completed. Your appointment is still saved — you can retry payment from your appointments list.
      </p>

      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <router-link
          to="/patient/appointments"
          class="px-5 py-2.5 bg-[#004795] text-white text-sm font-semibold rounded-xl hover:bg-[#003670] transition"
        >
          My Appointments
        </router-link>
        <router-link
          to="/patient/dashboard"
          class="px-5 py-2.5 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition"
        >
          Dashboard
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup></script>
