<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Payment History</h1>

    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-3">Reference</th>

          <th class="p-3">Amount</th>

          <th class="p-3">Status</th>

          <th class="p-3">Invoice</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="payment in paymentStore.payments" :key="payment.id">
          <td class="p-3">
            {{ payment.reference }}
          </td>

          <td class="p-3">{{ payment.amount }} ETB</td>

          <td class="p-3">
            {{ payment.status }}
          </td>

          <td class="p-3">
            <button
              class="text-blue-600"
              @click="paymentStore.downloadInvoice(payment.invoice.id)"
            >
              Download
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { usePaymentStore } from "../../stores/paymentStore";

const paymentStore = usePaymentStore();

onMounted(() => {
  paymentStore.fetchPayments();
});
</script>
