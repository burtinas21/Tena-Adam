<template>
  <div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow p-6">
      <h1 class="text-2xl font-bold mb-6">Payment</h1>

      <div class="space-y-3">
        <p>
          <strong>Hospital:</strong>
          {{ paymentData.hospital_name }}
        </p>

        <p>
          <strong>Amount:</strong>
          {{ paymentData.amount }} ETB
        </p>
      </div>

      <button
        @click="payNow"
        :disabled="paymentStore.loading"
        class="mt-6 w-full bg-blue-600 text-white py-3 rounded-lg"
      >
        Pay with Chapa
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "vue";
import { usePaymentStore } from "../../stores/paymentStore";

const paymentStore = usePaymentStore();

const paymentData = reactive({
  appointment_id: "",

  patient_id: "",

  hospital_id: "",

  payment_method_id: "",

  amount: 0,

  hospital_name: "",
});

async function payNow() {
  await paymentStore.createPayment(paymentData);
}
</script>
