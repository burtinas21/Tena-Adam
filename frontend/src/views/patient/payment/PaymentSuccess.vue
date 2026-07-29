<template>
  <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">

      <!-- Verifying spinner -->
      <template v-if="verifying">
        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-[#004795] animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Verifying payment…</h1>
        <p class="text-sm text-gray-500">Please wait while we confirm your payment.</p>
      </template>

      <!-- Success -->
      <template v-else-if="confirmed">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
        <p class="text-sm text-gray-500 mb-6">
          Your appointment has been confirmed. You will receive a notification with the details.
        </p>

        <!-- Download Invoice button (shown immediately after successful verification) -->
        <div v-if="invoiceId" class="mb-4">
          <button
            @click="downloadInvoice"
            :disabled="downloading"
            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition disabled:opacity-50"
          >
            <svg v-if="downloading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            {{ downloading ? 'Downloading…' : 'Download Invoice' }}
          </button>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <router-link
            to="/patient/appointments"
            class="px-5 py-2.5 bg-[#004795] text-white text-sm font-semibold rounded-xl hover:bg-[#003670] transition"
          >
            View My Appointments
          </router-link>
          <router-link
            to="/patient/dashboard"
            class="px-5 py-2.5 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition"
          >
            Go to Dashboard
          </router-link>
        </div>
      </template>

      <!-- Still processing -->
      <template v-else>
        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Payment Processing</h1>
        <p class="text-sm text-gray-500 mb-6">
          Your payment was received but confirmation is still processing. Your appointment will be updated shortly — please check back in a moment.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <router-link
            to="/patient/appointments"
            class="px-5 py-2.5 bg-[#004795] text-white text-sm font-semibold rounded-xl hover:bg-[#003670] transition"
          >
            View My Appointments
          </router-link>
          <router-link
            to="/patient/dashboard"
            class="px-5 py-2.5 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition"
          >
            Go to Dashboard
          </router-link>
        </div>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../../api/axios";
import paymentApi from "../../../api/paymentApi";

const verifying  = ref(true);
const confirmed  = ref(false);
const invoiceId  = ref(null);
const downloading = ref(false);

onMounted(async () => {
  const params = new URLSearchParams(window.location.search);
  const txRef  = params.get("tx_ref") || params.get("trx_ref");

  if (txRef) {
    try {
      const res = await api.post("/payments/callback", { tx_ref: txRef });
      confirmed.value = res.data?.success === true;
      // Backend returns invoice_id when payment is confirmed
      invoiceId.value  = res.data?.invoice_id ?? null;
    } catch {
      confirmed.value = false;
    }
  }

  verifying.value = false;
});

async function downloadInvoice() {
  if (!invoiceId.value || downloading.value) return;
  downloading.value = true;
  try {
    const blob = await paymentApi.downloadInvoice(invoiceId.value);
    const url  = window.URL.createObjectURL(new Blob([blob.data], { type: "application/pdf" }));
    const link = document.createElement("a");
    link.href  = url;
    link.setAttribute("download", `invoice-${invoiceId.value}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch {
    alert("Invoice download failed. Please try again.");
  } finally {
    downloading.value = false;
  }
}
</script>
