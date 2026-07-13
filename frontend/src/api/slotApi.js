import api from "./axios";

export default {
  /** Generate slots for a doctor on a date */
  generate(doctorId, date) {
    return api.post("/slots/generate", { doctor_id: doctorId, date });
  },

  /** Get all slots */
  getAll() {
    return api.get("/slots");
  },

  /**
   * Get available slots for a doctor on a specific date.
   * Uses the dedicated /slots/available endpoint which auto-generates slots
   * if they don't exist yet, then returns only available ones.
   */
  async getAvailable(doctorId, date) {
    const res = await api.get("/slots/available", {
      params: { doctor_id: doctorId, date },
    });
    return res.data?.data ?? res.data ?? [];
  },

  book(slotId) {
    return api.post(`/slots/${slotId}/book`);
  },

  release(slotId) {
    return api.post(`/slots/${slotId}/release`);
  },

  complete(slotId) {
    return api.post(`/slots/${slotId}/complete`);
  },
};
