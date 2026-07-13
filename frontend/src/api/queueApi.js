import api from "./axios";

export default {
  getDoctorQueue(doctorId, date) {
    return api.get(`/queue/doctor/${doctorId}`, { params: { date } });
  },

  /**
   * POST /queue/init
   * Generates queue entries from appointments for a doctor/date (idempotent).
   * Safe to call on every page load — skips silently if queue already exists.
   */
  init(doctorId, date) {
    return api.post("/queue/init", { doctor_id: doctorId, date });
  },

  generate(data) {
    return api.post("/queue/generate", data);
  },

  callNext(doctorId, date) {
    return api.post("/queue/call-next", { doctor_id: doctorId, date });
  },
  complete(queueId) {
    return api.post("/queue/complete", { queue_id: queueId });
  },
  skip(queueId) {
    return api.post("/queue/skip", { queue_id: queueId });
  },
  recall(queueId) {
    return api.post("/queue/recall", { queue_id: queueId });
  },
};
