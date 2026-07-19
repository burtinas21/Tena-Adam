import api from "./axios";

export default {
  // ── Statistics endpoints ──────────────────────────────────────────────────
  getPatientStatistics() {
    return api.get("/reports/patients");
  },

  getAppointmentReport() {
    return api.get("/reports/appointments");
  },

  getDoctorWorkload() {
    return api.get("/reports/doctors/workload");
  },

  getDepartmentPerformance() {
    return api.get("/reports/departments/performance");
  },

  getTelehealthStatistics() {
    return api.get("/reports/telehealth");
  },

  getHealthcareTrends() {
    return api.get("/reports/trends");
  },

  getDoctorRatingStatistics() {
    return api.get("/reports/doctor-ratings");
  },

  // ── Custom reports ────────────────────────────────────────────────────────
  generateCustomReport(reportId, params = {}) {
    return api.post(`/reports/custom/${reportId}`, params);
  },

  storeReport(data) {
    return api.post("/reports", data);
  },

  // ── Export endpoints ──────────────────────────────────────────────────────
  exportExcel(type) {
    return api.get(`/reports/export/excel/${type}`, { responseType: "blob" });
  },

  exportCsv(type) {
    return api.get(`/reports/export/csv/${type}`, { responseType: "blob" });
  },

  exportPdf(type) {
    return api.get(`/reports/export/pdf/${type}`, { responseType: "blob" });
  },
};
