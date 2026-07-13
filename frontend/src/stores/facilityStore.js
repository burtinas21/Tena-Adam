import { defineStore } from "pinia";
import facilityService from "../services/facilityService";

export const useFacilityStore = defineStore("facility", {
  state: () => ({
    facilities: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await facilityService.getAll();
        this.facilities = envelope.data ?? envelope;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load facilities";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await facilityService.create(data);
        const created = envelope.data ?? envelope;
        this.facilities.push(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create facility";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await facilityService.update(id, data);
        const updated = envelope.data ?? envelope;
        const index = this.facilities.findIndex((f) => f.id === id);
        if (index !== -1) this.facilities[index] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update facility";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await facilityService.destroy(id);
        this.facilities = this.facilities.filter((f) => f.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete facility";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
