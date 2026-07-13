import { defineStore } from "pinia";
import doctorApi from "../api/doctorApi";
import scheduleApi from "../api/scheduleApi";
import leaveApi from "../api/leaveApi";

export const useDoctorStore = defineStore("doctor", {
  state: () => ({
    doctors: [],
    selectedDoctor: null,
    schedules: [],      // schedules for selectedDoctor
    leaves: [],         // leaves for selectedDoctor
    allLeaves: [],      // all leaves (for hospital admin view)
    loading: false,
    scheduleLoading: false,
    leaveLoading: false,
    error: null,
  }),

  getters: {
    doctorById: (state) => (id) => state.doctors.find((d) => d.id === id),

    scheduledDays: (state) =>
      state.schedules.map((s) => s.day_of_week),

    sortedSchedules: (state) =>
      [...state.schedules].sort((a, b) => a.day_of_week - b.day_of_week),

    pendingLeaves: (state) =>
      state.allLeaves.filter((l) => l.status === "pending"),
  },

  actions: {
    // ── Doctors ────────────────────────────────────────────────────────────

    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await doctorApi.getAll();
        // HealthcareProviderResource::collection → { data: [...] }
        this.doctors = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load doctors";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    selectDoctor(doctor) {
      this.selectedDoctor = doctor;
      this.schedules = [];
      this.leaves = [];
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await doctorApi.create(data);
        const created = res.data?.data ?? res.data;
        this.doctors.push(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create doctor";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await doctorApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.doctors.findIndex((d) => d.id === id);
        if (idx !== -1) this.doctors[idx] = updated;
        if (this.selectedDoctor?.id === id) this.selectedDoctor = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update doctor";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await doctorApi.destroy(id);
        this.doctors = this.doctors.filter((d) => d.id !== id);
        if (this.selectedDoctor?.id === id) this.selectedDoctor = null;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete doctor";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Schedules ──────────────────────────────────────────────────────────

    async fetchSchedules(doctorId) {
      try {
        this.scheduleLoading = true;
        this.error = null;
        const res = await scheduleApi.getAll();
        // Filter for this doctor
        const all = res.data?.data ?? res.data ?? res.data;
        this.schedules = (Array.isArray(all) ? all : []).filter(
          (s) => s.doctor_id === doctorId
        );
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load schedules";
        throw err;
      } finally {
        this.scheduleLoading = false;
      }
    },

    async createSchedule(data) {
      try {
        this.scheduleLoading = true;
        this.error = null;
        const res = await scheduleApi.create(data);
        const created = res.data?.data ?? res.data;
        this.schedules.push(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create schedule";
        throw err;
      } finally {
        this.scheduleLoading = false;
      }
    },

    async updateSchedule(id, data) {
      try {
        this.scheduleLoading = true;
        this.error = null;
        const res = await scheduleApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.schedules.findIndex((s) => s.id === id);
        if (idx !== -1) this.schedules[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update schedule";
        throw err;
      } finally {
        this.scheduleLoading = false;
      }
    },

    async destroySchedule(id) {
      try {
        this.scheduleLoading = true;
        this.error = null;
        await scheduleApi.destroy(id);
        this.schedules = this.schedules.filter((s) => s.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete schedule";
        throw err;
      } finally {
        this.scheduleLoading = false;
      }
    },

    // ── Leaves ─────────────────────────────────────────────────────────────

    async fetchAllLeaves() {
      try {
        this.leaveLoading = true;
        this.error = null;
        const res = await leaveApi.getAll();
        this.allLeaves = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load leaves";
        throw err;
      } finally {
        this.leaveLoading = false;
      }
    },

    async fetchLeavesForDoctor(doctorId) {
      try {
        this.leaveLoading = true;
        this.error = null;
        const res = await leaveApi.getAll();
        const all = res.data?.data ?? res.data ?? [];
        this.leaves = all.filter((l) => l.doctor_id === doctorId);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load leaves";
        throw err;
      } finally {
        this.leaveLoading = false;
      }
    },

    async approveLeave(id, status) {
      try {
        this.leaveLoading = true;
        this.error = null;
        const res = await leaveApi.approve(id, status);
        const updated = res.data?.data ?? res.data;
        const updateList = (list) => {
          const idx = list.findIndex((l) => l.id === id);
          if (idx !== -1) list[idx] = { ...list[idx], ...updated };
        };
        updateList(this.allLeaves);
        updateList(this.leaves);
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to process leave";
        throw err;
      } finally {
        this.leaveLoading = false;
      }
    },

    async destroyLeave(id) {
      try {
        this.leaveLoading = true;
        this.error = null;
        await leaveApi.destroy(id);
        this.allLeaves = this.allLeaves.filter((l) => l.id !== id);
        this.leaves = this.leaves.filter((l) => l.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete leave";
        throw err;
      } finally {
        this.leaveLoading = false;
      }
    },
  },
});
