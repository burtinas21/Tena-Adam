import api from "./axios";

export default {
  /**
   * Create vital signs for a medical encounter.
   * Required: encounter_id, patient_id
   * Optional: blood_pressure_systolic, blood_pressure_diastolic, pulse_rate,
   *           respiratory_rate, temperature, weight, height, blood_oxygen, measured_at
   */
  create(data) {
    return api.post("/vitals", data);
  },

  /**
   * Get a single vital record by ID.
   */
  getById(id) {
    return api.get(`/vitals/${id}`);
  },

  /**
   * Update a vital record.
   */
  update(id, data) {
    return api.put(`/vitals/${id}`, data);
  },

  /**
   * Delete a vital record.
   */
  destroy(id) {
    return api.delete(`/vitals/${id}`);
  },
};
