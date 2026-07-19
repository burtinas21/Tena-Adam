import { defineStore } from "pinia";
import medicalEncounterApi from "../api/medicalEncounterApi";

export const useMedicalEncounterStore = defineStore("medicalEncounter", {
  state: () => ({
    encounters: [],
    currentEncounter: null,
    loading: false,
    saving: false,
    error: null,
  }),

  getters: {
    inProgress: (s) => s.encounters.filter((e) => e.status === "in_progress"),
    completed:  (s) => s.encounters.filter((e) => e.status === "completed"),
    cancelled:  (s) => s.encounters.filter((e) => e.status === "cancelled"),
  },

  actions: {
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await medicalEncounterApi.getAll();
        this.encounters = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load encounters";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchById(id) {
      try {
        this.loading = true;
        this.error = null;
        const res = await medicalEncounterApi.getById(id);
        this.currentEncounter = res.data?.data ?? res.data;
        return this.currentEncounter;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load encounter";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /** Fetch all past encounters for a patient (full cross-doctor history) */
    async fetchPatientHistory(patientId) {
      try {
        const res = await medicalEncounterApi.getPatientHistory(patientId);
        return res.data?.data ?? [];
      } catch {
        return [];
      }
    },

    async create(data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await medicalEncounterApi.create(data);
        const created = res.data?.data ?? res.data;
        this.encounters.unshift(created);
        this.currentEncounter = created;
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create encounter";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async update(id, data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await medicalEncounterApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.encounters.findIndex((e) => e.id === id);
        if (idx !== -1) this.encounters[idx] = updated;
        if (this.currentEncounter?.id === id) this.currentEncounter = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update encounter";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    async complete(id) {
      try {
        this.saving = true;
        this.error = null;
        const res = await medicalEncounterApi.complete(id);
        const updated = res.data?.data ?? res.data;
        const idx = this.encounters.findIndex((e) => e.id === id);
        if (idx !== -1) this.encounters[idx] = updated;
        if (this.currentEncounter?.id === id) this.currentEncounter = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to complete encounter";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    /** Doctor updates patient persistent medical fields (blood_type, allergies, medical_history) */
    async updatePatientMedical(encounterId, data) {
      try {
        this.saving = true;
        this.error = null;
        const res = await medicalEncounterApi.updatePatientMedical(encounterId, data);
        // Update the patient fields in the current encounter in-place
        if (this.currentEncounter?.id === encounterId && this.currentEncounter.patient) {
          this.currentEncounter.patient = {
            ...this.currentEncounter.patient,
            ...res.data.data,
          };
        }
        const idx = this.encounters.findIndex((e) => e.id === encounterId);
        if (idx !== -1 && this.encounters[idx].patient) {
          this.encounters[idx] = {
            ...this.encounters[idx],
            patient: { ...this.encounters[idx].patient, ...res.data.data },
          };
        }
        return res.data.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update patient profile";
        throw err;
      } finally {
        this.saving = false;
      }
    },

    setCurrentEncounter(encounter) {
      this.currentEncounter = encounter;
    },

    clearError() {
      this.error = null;
    },
  },
});
