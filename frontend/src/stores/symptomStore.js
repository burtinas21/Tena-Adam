import { defineStore } from "pinia";
import symptomApi from "../api/symptomApi";

export const useSymptomStore = defineStore("symptom", {
  state: () => ({
    symptoms: [],
    loading: false,
    error: null,

    // Recommendations returned after symptom selection
    recommendations: null,
    recommendationsLoading: false,

    // Analytics (admin/doctor view)
    analytics: [],
    topSymptoms: [],
    analyticsLoading: false,

    // Mappings management (admin)
    mappings: [],
    mappingsLoading: false,
  }),

  actions: {
    // ── Symptoms CRUD ───────────────────────────────────────────────────
    async fetchAll() {
      try {
        this.loading = true;
        this.error = null;
        const res = await symptomApi.getAll();
        this.symptoms = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load symptoms";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await symptomApi.create(data);
        const created = res.data?.data ?? res.data;
        this.symptoms.push(created);
        return created;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create symptom";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async update(id, data) {
      try {
        this.loading = true;
        this.error = null;
        const res = await symptomApi.update(id, data);
        const updated = res.data?.data ?? res.data;
        const idx = this.symptoms.findIndex((s) => s.id === id);
        if (idx !== -1) this.symptoms[idx] = updated;
        return updated;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update symptom";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async destroy(id) {
      try {
        this.loading = true;
        this.error = null;
        await symptomApi.destroy(id);
        this.symptoms = this.symptoms.filter((s) => s.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete symptom";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Recommendations ─────────────────────────────────────────────────
    async fetchRecommendations(symptomId) {
      try {
        this.recommendationsLoading = true;
        this.recommendations = null;
        const res = await symptomApi.getRecommendations(symptomId);
        this.recommendations = res.data;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load recommendations";
        throw err;
      } finally {
        this.recommendationsLoading = false;
      }
    },

    // ── Mappings (admin) ─────────────────────────────────────────────────
    async fetchMappingsBySymptom(symptomId) {
      try {
        this.mappingsLoading = true;
        const res = await symptomApi.getMappingsBySymptom(symptomId);
        this.mappings = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load mappings";
        throw err;
      } finally {
        this.mappingsLoading = false;
      }
    },

    async createMapping(data) {
      try {
        this.mappingsLoading = true;
        const res = await symptomApi.createMapping(data);
        return res.data?.data ?? res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to create mapping";
        throw err;
      } finally {
        this.mappingsLoading = false;
      }
    },

    async updateMapping(id, data) {
      try {
        this.mappingsLoading = true;
        const res = await symptomApi.updateMapping(id, data);
        return res.data?.data ?? res.data;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to update mapping";
        throw err;
      } finally {
        this.mappingsLoading = false;
      }
    },

    async destroyMapping(id) {
      try {
        this.mappingsLoading = true;
        await symptomApi.destroyMapping(id);
        this.mappings = this.mappings.filter((m) => m.id !== id);
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to delete mapping";
        throw err;
      } finally {
        this.mappingsLoading = false;
      }
    },

    // ── Analytics ────────────────────────────────────────────────────────
    async fetchAnalytics() {
      try {
        this.analyticsLoading = true;
        const res = await symptomApi.getAllAnalytics();
        this.analytics = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to load analytics";
        throw err;
      } finally {
        this.analyticsLoading = false;
      }
    },

    async fetchTopSymptoms() {
      try {
        this.analyticsLoading = true;
        const res = await symptomApi.getTopSymptoms();
        this.topSymptoms = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load top symptoms";
        throw err;
      } finally {
        this.analyticsLoading = false;
      }
    },

    async logAnalytic(data) {
      try {
        await symptomApi.logAnalytic(data);
      } catch {
        // silent — analytics logging should never block the user flow
      }
    },
  },
});
