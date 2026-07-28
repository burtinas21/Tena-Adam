import { defineStore } from "pinia";
import paymentService from "../services/paymentService";

export const usePaymentStore = defineStore("payment", {
  state: () => ({
    payments: [],
    invoices: [],
    refunds: [],
    payment: null,
    invoice: null,
    loading: false,
    error: null,
  }),

  actions: {
    async createPayment(data) {
      this.loading = true;
      this.error = null;

      try {
        const result = await paymentService.createPayment(data);

        this.payment = result.payment;

        paymentService.redirectToCheckout(result.checkout_url);

        return result;
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchPayments() {
      this.loading = true;

      try {
        this.payments = await paymentService.getPayments();
      } finally {
        this.loading = false;
      }
    },

    async fetchInvoices() {
      this.loading = true;

      try {
        this.invoices = await paymentService.getInvoices();
      } finally {
        this.loading = false;
      }
    },

    async fetchInvoice(id) {
      this.loading = true;

      try {
        this.invoice = await paymentService.getInvoice(id);
      } finally {
        this.loading = false;
      }
    },

    async downloadInvoice(id) {
      const response = await paymentService.downloadInvoice(id);

      const url = window.URL.createObjectURL(
        new Blob([response.data])
      );

      const link = document.createElement("a");

      link.href = url;

      link.download = `Invoice-${id}.pdf`;

      document.body.appendChild(link);

      link.click();

      link.remove();
    },

    async fetchRefunds() {
      this.loading = true;

      try {
        this.refunds = await paymentService.getRefunds();
      } finally {
        this.loading = false;
      }
    },
  },
});