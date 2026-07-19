import api from "./axios";

export default {
  getAll() {
    return api.get("/medical-encounters");
  },

  getById(id) {
    return api.get(`/medical-encounters/${id}`);
  },

  /** Get ALL encounters for a specific patient (full history for doctor view) */
  getPatientHistory(patientId) {
    return api.get(`/medical-encounters/patient/${patientId}`);
  },

  create(data) {
    return api.post("/medical-encounters", data);
  },

  update(id, data) {
    return api.put(`/medical-encounters/${id}`, data);
  },

  complete(id) {
    return api.patch(`/medical-encounters/${id}/complete`);
  },

  /** Doctor updates persistent patient medical fields during consultation */
  updatePatientMedical(encounterId, data) {
    return api.patch(`/medical-encounters/${encounterId}/patient-medical`, data);
  },
};
