import { defineStore } from "pinia";
import hospitalAdminApi from "../api/hospitalAdminApi";

export const useHospitalAdminStore = defineStore("hospitalAdmin", {
  state: () => ({
    admins: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await hospitalAdminApi.getAll();
        this.admins = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load hospital admins";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await hospitalAdminApi.create(data);
        const user = res.data?.user ?? res.data;
        this.admins.unshift(user);
        return res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create hospital admin";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await hospitalAdminApi.update(id, data);
        const updated = res.data?.user ?? res.data;
        const index = this.admins.findIndex((a) => a.id === id);
        if (index !== -1) this.admins[index] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update hospital admin";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await hospitalAdminApi.destroy(id);
        this.admins = this.admins.filter((a) => a.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete hospital admin";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
