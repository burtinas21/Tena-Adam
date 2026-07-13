import { defineStore } from "pinia";
import departmentService from "../services/departmentService";

export const useDepartmentStore = defineStore("department", {
  state: () => ({
    departments: [],
    loading: false,
    error: null,
  }),

  getters: {
    activeDepartments: (state) =>
      state.departments.filter((d) => d.is_active),
  },

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        // departmentService.getAll() returns response.data which is
        // Laravel's { data: [...] } envelope from DepartmentResource::collection
        const envelope = await departmentService.getAll();
        this.departments = envelope.data ?? envelope;
      } catch (error) {
        this.error =
          error.response?.data?.message || "Failed to load departments";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        // departmentService.create() returns response.data which is
        // Laravel's { data: {...} } envelope from new DepartmentResource
        const envelope = await departmentService.create(data);
        const created = envelope.data ?? envelope;
        this.departments.push(created);
        return created;
      } catch (error) {
        this.error =
          error.response?.data?.message || "Failed to create department";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const envelope = await departmentService.update(id, data);
        const updated = envelope.data ?? envelope;
        const index = this.departments.findIndex((d) => d.id === id);
        if (index !== -1) this.departments[index] = updated;
        return updated;
      } catch (error) {
        this.error =
          error.response?.data?.message || "Failed to update department";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await departmentService.destroy(id);
        this.departments = this.departments.filter((d) => d.id !== id);
      } catch (error) {
        this.error =
          error.response?.data?.message || "Failed to delete department";
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
