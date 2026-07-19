import { defineStore } from "pinia";
import vitalApi from "../api/vitalApi";

export const useVitalStore = defineStore("vital", {
  state: () => ({
    currentVital: null,
    loading: false,
    saving: false,
    error: null,
  }),

  actions: {
    async create(data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await vitalApi.create(data);
        this.currentVital = res.data?.data ?? res.data;
        return this.currentVital;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to save vitals";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async fetchById(id) {
      try {
        this.loading = true;
        this.error = null;
        const res = await vitalApi.getById(id);
        this.currentVital = res.data?.data ?? res.data;
        return this.currentVital;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load vitals";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await vitalApi.update(id, data);
        this.currentVital = res.data?.data ?? res.data;
        return this.currentVital;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update vitals";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async destroy(id) {
      try {
        this.saving = true;
        this.error = null;
        await vitalApi.destroy(id);
        this.currentVital = null;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete vital";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    setVital(vital) {
      this.currentVital = vital;
    },

    clearError() {
      this.error = null;
    },
  },
});
