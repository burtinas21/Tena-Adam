import { defineStore } from "pinia";
import appointmentApi from "../api/appointmentApi";

export const useAppointmentStore = defineStore("appointment", {
  state: () => ({
    appointments: [],
    loading: false,
    error: null,
  }),

  getters: {
    pending:   (s) => s.appointments.filter((a) => a.status === "pending"),
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
        const res    = await appointmentApi.create(data);
        const created = res.data?.data ?? res.data;
        this.appointments.unshift(created);
        return created;
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
        const res    = await appointmentApi.update(id, { status, ...extra });
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
  },
});
