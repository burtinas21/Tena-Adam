import api from "./axios";

export default {
  getAll() {
    return api.get("/appointments");
  },
  getById(id) {
    return api.get(`/appointments/${id}`);
  },
  create(data) {
    return api.post("/appointments", data);
  },
  update(id, data) {
    return api.put(`/appointments/${id}`, data);
  },
  /**
   * Reschedule an appointment by selecting a new slot.
   * @param {string} id   - Appointment UUID
   * @param {string} slotId - AppointmentSlot UUID
   */
  reschedule(id, slotId) {
    return api.put(`/appointments/${id}/reschedule`, { slot_id: slotId });
  },

  /**
   * Hospital-admin reassigns a leave-affected appointment to a different doctor.
   * @param {string} id     - Appointment UUID
   * @param {string} slotId - AppointmentSlot UUID belonging to the replacement doctor
   */
  adminReschedule(id, slotId) {
    return api.put(`/appointments/${id}/admin-reschedule`, { slot_id: slotId });
  },

  /**
   * Fetch available doctors + slots for a hospital/department/date.
   * Used by the admin reassign picker after leave approval.
   */
  getAvailableDoctorSlots({ hospital_id, department_id, date, exclude_doctor_id }) {
    return api.get("/appointments/available-doctor-slots", {
      params: { hospital_id, department_id, date, exclude_doctor_id },
    });
  },

  destroy(id) {
    return api.delete(`/appointments/${id}`);
  },
};
