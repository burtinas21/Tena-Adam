import { defineStore } from "pinia";
import prescriptionApi from "../api/prescriptionApi";

export const usePrescriptionStore = defineStore("prescription", {
  state: () => ({
    prescriptions: [],
    loading: false,
    saving: false,
    error: null,
  }),

  getters: {
    active:    (s) => s.prescriptions.filter((p) => p.status === "active"),
    completed: (s) => s.prescriptions.filter((p) => p.status === "completed"),
    cancelled: (s) => s.prescriptions.filter((p) => p.status === "cancelled"),
  },

  actions: {
    async fetchAll(params = {}) {
      try {
        this.loading = true;
        this.error = null;
        const res = await prescriptionApi.getAll(params);
        this.prescriptions = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load prescriptions";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchByEncounter(encounterId) {
      return this.fetchAll({ encounter_id: encounterId });
    },

    async create(data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await prescriptionApi.create(data);
        const created = res.data?.data ?? res.data;
        this.prescriptions.unshift(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create prescription";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async update(id, data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await prescriptionApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.prescriptions.findIndex((p) => p.id === id);
        if (idx !== -1) this.prescriptions[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update prescription";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async complete(id) {
      try {
        this.saving = true;
        this.error = null;
        const res = await prescriptionApi.complete(id);
        const updated = res.data?.data ?? res.data;
        const idx = this.prescriptions.findIndex((p) => p.id === id);
        if (idx !== -1) this.prescriptions[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to complete prescription";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async cancel(id) {
      try {
        this.saving = true;
        this.error = null;
        const res = await prescriptionApi.cancel(id);
        const updated = res.data?.data ?? res.data;
        const idx = this.prescriptions.findIndex((p) => p.id === id);
        if (idx !== -1) this.prescriptions[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to cancel prescription";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    clearError() {
      this.error = null;
    },
  },
});
