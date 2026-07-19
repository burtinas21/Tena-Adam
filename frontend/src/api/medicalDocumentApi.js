import api from "./axios";

export default {
  /**
   * List all documents for the authenticated user.
   * Optional query params: encounter_id, patient_id
   */
  getAll(params = {}) {
    return api.get("/medical-documents", { params });
  },

  /**
   * Get documents for a specific patient.
   */
  getByPatient(patientId) {
    return api.get(`/patients/${patientId}/medical-documents`);
  },

  /**
   * Get documents for a specific encounter.
   */
  getByEncounter(encounterId) {
    return api.get(`/encounters/${encounterId}/medical-documents`);
  },

  /**
   * Upload a new document.
   * Sends as multipart/form-data because it includes a file.
   * data: { patient_id, encounter_id?, file (File), document_type, description? }
   */
  upload(data) {
    const form = new FormData();
    form.append("patient_id", data.patient_id);
    form.append("document_type", data.document_type);
    if (data.encounter_id) form.append("encounter_id", data.encounter_id);
    if (data.description)  form.append("description", data.description);
    form.append("file", data.file);

    return api.post("/medical-documents", form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  /**
   * Update document metadata (or replace the file).
   * Uses POST + _method=PUT for multipart support (Laravel method spoofing).
   */
  update(id, data) {
    const form = new FormData();
    if (data.document_type) form.append("document_type", data.document_type);
    if (data.description !== undefined) form.append("description", data.description ?? "");
    if (data.file) form.append("file", data.file);

    return api.post(`/medical-documents/${id}`, form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  /**
   * Delete a document.
   */
  destroy(id) {
    return api.delete(`/medical-documents/${id}`);
  },

  /**
   * Get the download URL for a document (opens directly in browser).
   */
  downloadUrl(id) {
    return `${api.defaults.baseURL}/medical-documents/${id}/download`;
  },
};
