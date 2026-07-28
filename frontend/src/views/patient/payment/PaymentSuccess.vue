<template>
  <div class="max-w-lg mx-auto mt-20">
    <div class="bg-white rounded-xl shadow p-8 text-center">
      <div class="text-green-600 text-5xl">✓</div>

      <h1 class="text-2xl font-bold mt-4">Payment Successful</h1>

      <p class="mt-2 text-gray-600">
        Your payment has been received successfully.
      </p>

      <button
        class="mt-6 bg-blue-600 text-white px-6 py-3 rounded"
        @click="downloadInvoice"
      >
        Download Invoice
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from "vue-router";
import { usePaymentStore } from "../../stores/paymentStore";

const paymentStore = usePaymentStore();

const route = useRoute();

function downloadInvoice() {
  paymentStore.downloadInvoice(route.params.invoice);
}
</script>
