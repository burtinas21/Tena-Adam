import { defineStore } from "pinia";
import medicalDocumentApi from "../api/medicalDocumentApi";

export const useMedicalDocumentStore = defineStore("medicalDocument", {
  state: () => ({
    documents: [],
    loading: false,
    uploading: false,
    error: null,
  }),

  getters: {
    byType: (s) => (type) => s.documents.filter((d) => d.document_type === type),

    grouped: (s) => {
      const map = {};
      for (const doc of s.documents) {
        const t = doc.document_type ?? "other";
        if (!map[t]) map[t] = [];
        map[t].push(doc);
      }
      return map;
    },
  },

  actions: {
    async fetchAll(params = {}) {
      try {
        this.loading = true;
        this.error = null;
        const res = await medicalDocumentApi.getAll(params);
        this.documents = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load documents";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchByEncounter(encounterId) {
      try {
        this.loading = true;
        this.error = null;
        const res = await medicalDocumentApi.getByEncounter(encounterId);
        this.documents = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load documents";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchByPatient(patientId) {
      try {
        this.loading = true;
        this.error = null;
        const res = await medicalDocumentApi.getByPatient(patientId);
        this.documents = res.data?.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load documents";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async upload(data) {
      try {
        this.uploading = true;
        this.error = null;
        const res = await medicalDocumentApi.upload(data);
        const created = res.data?.data ?? res.data;
        this.documents.unshift(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to upload document";
        throw err;
      } finally {
        this.uploading = false;
      }
    },

    async destroy(id) {
      try {
        this.error = null;
        await medicalDocumentApi.destroy(id);
        this.documents = this.documents.filter((d) => d.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete document";
        throw err;
      }
    },

    downloadUrl(id) {
      return medicalDocumentApi.downloadUrl(id);
    },

    clearError() {
      this.error = null;
    },
  },
});
