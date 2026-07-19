import api from "./axios";

export default {
  /**
   * List all prescriptions for the authenticated user.
   * Optionally filter by encounter_id.
   */
  getAll(params = {}) {
    return api.get("/prescriptions", { params });
  },

  /**
   * Get a single prescription by ID.
   */
  getById(id) {
    return api.get(`/prescriptions/${id}`);
  },

  /**
   * Create a new prescription for an encounter.
   * Required: encounter_id, medication_name, dosage, frequency
   * Optional: medication_id, route, duration_days, quantity, instructions, refills
   */
  create(data) {
    return api.post("/prescriptions", data);
  },

  /**
   * Update an existing prescription.
   */
  update(id, data) {
    return api.put(`/prescriptions/${id}`, data);
  },

  /**
   * Mark a prescription as completed.
   */
  complete(id) {
    return api.patch(`/prescriptions/${id}/complete`);
  },

  /**
   * Cancel a prescription.
   */
  cancel(id) {
    return api.patch(`/prescriptions/${id}/cancel`);
  },
};
