import { defineStore } from "pinia";
import hospitalService from "../services/hospitalService";

export const useHospitalStore = defineStore("hospital", {
  state: () => ({
    hospitals: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await hospitalService.getAll();
        this.hospitals = envelope.data ?? envelope;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load hospitals";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await hospitalService.create(data);
        const created = envelope.data ?? envelope;
        this.hospitals.push(created);
        return created;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to create hospital";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await hospitalService.update(id, data);
        const updated = envelope.data ?? envelope;
        const index = this.hospitals.findIndex((h) => h.id === id);
        if (index !== -1) this.hospitals[index] = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to update hospital";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await hospitalService.destroy(id);
        this.hospitals = this.hospitals.filter((h) => h.id !== id);
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to delete hospital";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
