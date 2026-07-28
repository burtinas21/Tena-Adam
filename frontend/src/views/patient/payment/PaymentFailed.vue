<template>
  <div class="max-w-lg mx-auto mt-20">
    <div class="bg-white rounded-xl shadow p-8 text-center">
      <div class="text-red-600 text-5xl">✕</div>

      <h1 class="text-2xl font-bold mt-4">Payment Failed</h1>

      <p class="mt-3">Your payment was not completed.</p>

      <RouterLink
        to="/patient/payments"
        class="inline-block mt-6 bg-blue-600 text-white px-6 py-3 rounded"
      >
        Try Again
      </RouterLink>
    </div>
  </div>
</template>

<script setup></script>
