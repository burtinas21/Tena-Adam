import { defineStore } from "pinia";
import appointmentApi from "../api/appointmentApi";

export const useAppointmentStore = defineStore("appointment", {
  state: () => ({
    appointments: [],
    incomingReferrals: [],    // referrals assigned to the logged-in doctor
    loading: false,
    error: null,
  }),

  getters: {
    pending:         (s) => s.appointments.filter((a) => a.status === "pending"),
    pendingPayment:  (s) => s.appointments.filter((a) => a.status === "pending_payment"),
    confirmed: (s) => s.appointments.filter((a) => a.status === "confirmed"),
    completed: (s) => s.appointments.filter((a) => a.status === "completed"),
    cancelled: (s) => s.appointments.filter((a) => a.status === "cancelled"),
  },

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error   = null;
        const res    = await appointmentApi.getAll();
        this.appointments = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load appointments";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error   = null;
        const res     = await appointmentApi.create(data);
        // Response contains appointment + checkout_url for Chapa redirect
        const appointment = res.data?.appointment ?? res.data?.data ?? res.data;
        const checkoutUrl = res.data?.checkout_url ?? null;
        if (appointment?.id) {
          this.appointments.unshift(appointment);
        }
        return { appointment, checkoutUrl };
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to book appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateStatus(id, status, extra = {}) {
      try {
        this.loading = true;
        this.error   = null;
        const res     = await appointmentApi.update(id, { status, ...extra });
        const updated = res.data?.data ?? res.data;
        const idx = this.appointments.findIndex((a) => a.id === id);
        if (idx !== -1) this.appointments[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async reschedule(id, slotId) {
      try {
        this.loading = true;
        this.error   = null;
        const res     = await appointmentApi.reschedule(id, slotId);
        const updated = res.data?.data ?? res.data;
        const idx = this.appointments.findIndex((a) => a.id === id);
        if (idx !== -1) this.appointments[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to reschedule appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error   = null;
        await appointmentApi.destroy(id);
        this.appointments = this.appointments.filter((a) => a.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Patient hides a completed/cancelled appointment from their own list.
     * The record is NOT deleted from the DB — doctors/admins still see it.
     */
    async hideFromHistory(id) {
      try {
        this.loading = true;
        this.error   = null;
        await appointmentApi.hideFromHistory(id);
        this.appointments = this.appointments.filter((a) => a.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to hide appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Referrals ──────────────────────────────────────────────────────────

    /**
     * Doctor refers an appointment to another doctor/department.
     */
    async refer(appointmentId, data) {
      try {
        this.loading = true;
        this.error   = null;
        const res    = await appointmentApi.refer(appointmentId, data);
        // Update the local appointment if doctor changed
        await this.fetchAll();
        return res.data?.data ?? res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to refer appointment";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Load incoming referrals for the authenticated doctor.
     */
    async fetchIncomingReferrals() {
      try {
        const res = await appointmentApi.getIncomingReferrals();
        this.incomingReferrals = res.data?.data ?? res.data ?? [];
        return this.incomingReferrals;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load referrals";
        throw err;
      }
    },

    /**
     * Referred-to doctor accepts or rejects a referral.
     */
    async respondReferral(referralId, action, rejectionReason = null) {
      try {
        this.loading = true;
        this.error   = null;
        const res    = await appointmentApi.respondReferral(referralId, action, rejectionReason);
        // Remove from incoming list after responding
        this.incomingReferrals = this.incomingReferrals.filter((r) => r.id !== referralId);
        await this.fetchAll();
        return res.data?.data ?? res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to respond to referral";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
