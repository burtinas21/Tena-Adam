import { defineStore } from "pinia";
import receptionistApi from "../api/receptionistApi";

export const useReceptionistStore = defineStore("receptionist", {
  state: () => ({
    patients: [],
    searchResults: [],
    appointments: [],
    doctors: [],
    loading: false,
    searchLoading: false,
    error: null,
  }),

  getters: {
    todayAppointments: (s) => {
      const today = new Date().toISOString().slice(0, 10);
      return s.appointments.filter((a) =>
        a.scheduled_time?.startsWith(today)
      );
    },
  },

  actions: {
    // ── Patients ──────────────────────────────────────────────────────────

    async fetchPatients() {
      try {
        this.loading = true;
        this.error = null;
        const res = await receptionistApi.getPatients();
        this.patients = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load patients";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async searchPatients(q) {
      try {
        this.searchLoading = true;
        this.error = null;
        const res = await receptionistApi.searchPatients(q);
        this.searchResults = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Search failed";
        throw err;
      } finally {
        this.searchLoading = false;
      }
    },

    async registerPatient(data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await receptionistApi.registerPatient(data);
        const patient = res.data?.data ?? res.data;
        this.patients.unshift(patient);
        return patient;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to register patient";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Appointments ──────────────────────────────────────────────────────

    async fetchAppointments() {
      try {
        this.loading = true;
        this.error = null;
        const res = await receptionistApi.getAppointments();
        this.appointments = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load appointments";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Doctors ───────────────────────────────────────────────────────────

    async fetchDoctors() {
      try {
        this.loading = true;
        this.error = null;
        const res = await receptionistApi.getDoctors();
        this.doctors = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load doctors";
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
