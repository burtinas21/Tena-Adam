import api from "./axios";

export default {
  // ── Symptoms ──────────────────────────────────────────────────────────
  getAll() {
    return api.get("/symptoms");
  },

  getById(id) {
    return api.get(`/symptoms/${id}`);
  },

  create(data) {
    return api.post("/symptoms", data);
  },

  update(id, data) {
    return api.put(`/symptoms/${id}`, data);
  },

  destroy(id) {
    return api.delete(`/symptoms/${id}`);
  },

  // ── Symptom–Department Mappings ───────────────────────────────────────
  getMappingsBySymptom(symptomId) {
    return api.get(`/symptom-mappings/symptom/${symptomId}`);
  },

  getRecommendations(symptomId) {
    return api.get(
      `/symptom-mappings/recommendations-with-appointment/${symptomId}`
    );
  },

  createMapping(data) {
    return api.post("/symptom-mappings", data);
  },

  updateMapping(id, data) {
    return api.put(`/symptom-mappings/${id}`, data);
  },

  destroyMapping(id) {
    return api.delete(`/symptom-mappings/${id}`);
  },

  createAppointmentFromSymptom(symptomId, data) {
    return api.post(`/symptom-mappings/${symptomId}/create-appointment`, data);
  },

  // ── Symptom Analytics ─────────────────────────────────────────────────
  logAnalytic(data) {
    return api.post("/symptom-analytics", data);
  },

  getAllAnalytics() {
    return api.get("/symptom-analytics");
  },

  getTopSymptoms() {
    return api.get("/symptom-analytics/top-symptoms");
  },
};
