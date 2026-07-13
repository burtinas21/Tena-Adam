import { defineStore } from "pinia";
import operatingHourService from "../services/operatingHourService";

// Maps day_of_week integer (0=Sun … 6=Sat) to label
export const DAY_LABELS = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

export const useOperatingHourStore = defineStore("operatingHour", {
  state: () => ({
    hours: [],
    loading: false,
    error: null,
  }),

  getters: {
    // Returns hours sorted by day_of_week
    sortedHours: (state) =>
      [...state.hours].sort((a, b) => a.day_of_week - b.day_of_week),
  },

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await operatingHourService.getAll();
        this.hours = envelope.data ?? envelope;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load operating hours";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await operatingHourService.create(data);
        const created = envelope.data ?? envelope;
        this.hours.push(created);
        return created;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to create operating hour";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await operatingHourService.update(id, data);
        const updated = envelope.data ?? envelope;
        const index = this.hours.findIndex((h) => h.id === id);
        if (index !== -1) this.hours[index] = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to update operating hour";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await operatingHourService.destroy(id);
        this.hours = this.hours.filter((h) => h.id !== id);
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to delete operating hour";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
