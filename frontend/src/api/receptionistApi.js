import api from "./axios";

export default {
  // ── Patient registration ─────────────────────────────────────────────────
  getPatients() {
    return api.get("/receptionist/patients");
  },
  searchPatients(q) {
    return api.get("/receptionist/patients/search", { params: { q } });
  },
  registerPatient(data) {
    return api.post("/receptionist/patients", data);
  },

  // ── Appointments ─────────────────────────────────────────────────────────
  /** Get all appointments scoped to the receptionist's hospital */
  getAppointments() {
    return api.get("/appointments");
  },
  /**
   * Book an appointment on behalf of a patient.
   * Receptionist passes patient_id explicitly.
   */
  bookAppointment(data) {
    return api.post("/appointments", data);
  },

  // ── Doctor schedules (to build time picker) ──────────────────────────────
  getDoctorSchedules() {
    return api.get("/doctor-schedules");
  },

  // ── Doctors at this hospital (already hospital-scoped by backend) ────────
  getDoctors() {
    return api.get("/healthcare-providers");
  },

  // ── Queue (reuse shared queue endpoints) ─────────────────────────────────
  getDoctorQueue(doctorId, date) {
    return api.get(`/queue/doctor/${doctorId}`, { params: { date } });
  },
  initQueue(doctorId, date) {
    return api.post("/queue/init", { doctor_id: doctorId, date });
  },
  addWalkIn(data) {
    return api.post("/queue/generate", data);
  },
};
