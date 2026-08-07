import { defineStore } from "pinia";
import permissionApi from "../api/permissionApi";

export const usePermissionStore = defineStore("permission", {
  state: () => ({
    permissions: [],
    grouped: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await permissionApi.getAll();
        this.permissions = res.data?.data ?? res.data ?? [];
        this.grouped = res.data?.grouped ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load permissions";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
