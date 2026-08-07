import { defineStore } from "pinia";
import roleApi from "../api/roleApi";

export const useRoleStore = defineStore("role", {
  state: () => ({
    roles: [],
    loading: false,
    error: null,
  }),

  getters: {
    systemRoles: (state) =>
      state.roles.filter((r) =>
        ["platform_admin", "hospital_admin", "doctor", "receptionist", "patient"].includes(r.name)
      ),
    customRoles: (state) =>
      state.roles.filter(
        (r) =>
          !["platform_admin", "hospital_admin", "doctor", "receptionist", "patient"].includes(
            r.name
          )
      ),
  },

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await roleApi.getAll();
        this.roles = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load roles";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await roleApi.create(data);
        const created = res.data?.data ?? res.data;
        this.roles.push(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create role";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await roleApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.roles.findIndex((r) => r.id === id);
        if (idx !== -1) this.roles[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update role";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await roleApi.destroy(id);
        this.roles = this.roles.filter((r) => r.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete role";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async syncPermissions(roleId, permissionIds) {
      try {
        this.error = null;
        // Use a dedicated syncing flag — not the global loading flag —
        // so card skeletons don't flash on every auto-save toggle.
        const res = await roleApi.syncPermissions(roleId, permissionIds);
        const updated = res.data?.data ?? res.data;
        const idx = this.roles.findIndex((r) => r.id === roleId);
        if (idx !== -1) this.roles[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to save permissions";
        throw err;
      }
    },
  },
});
