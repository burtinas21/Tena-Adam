import api from "./axios";

export default {
  getAll() {
    return api.get("/appointments");
  },
  getById(id) {
    return api.get(`/appointments/${id}`);
  },

  /**
   * Create an appointment.
   * If `data.files` is a non-empty array of File objects we send multipart/form-data
   * so the backend can receive both form fields and file uploads in one request.
   * Otherwise we fall back to plain JSON (no change to existing flow).
   */
  create(data) {
    const hasFiles = data.files && data.files.length > 0;

    if (!hasFiles) {
      // Plain JSON — same as before
      const { files, ...rest } = data;
      return api.post("/appointments", rest);
    }

    // Build FormData for multipart upload
    const form = new FormData();
    form.append("doctor_id",          data.doctor_id);
    form.append("appointment_date",   data.appointment_date);
    form.append("appointment_time",   data.appointment_time);
    form.append("reason",             data.reason);
    form.append("is_telehealth",      data.is_telehealth ? "1" : "0");
    form.append("visit_type",         data.visit_type || "in_person");
    if (data.notes)              form.append("notes",              data.notes);
    if (data.patient_id)         form.append("patient_id",         data.patient_id);
    if (data.payment_method_id)  form.append("payment_method_id",  data.payment_method_id);
    if (data.amount != null)     form.append("amount",             data.amount);

    data.files.forEach((file) => form.append("files[]", file));

    return api.post("/appointments", form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  update(id, data) {
    return api.put(`/appointments/${id}`, data);
  },

  /**
   * Reschedule an appointment by selecting a new slot.
   */
  reschedule(id, slotId) {
    return api.put(`/appointments/${id}/reschedule`, { slot_id: slotId });
  },

  /**
   * Hospital-admin reassigns a leave-affected appointment to a different doctor.
   */
  adminReschedule(id, slotId) {
    return api.put(`/appointments/${id}/admin-reschedule`, { slot_id: slotId });
  },

  /**
   * Fetch available doctors + slots for a hospital/department/date.
   */
  getAvailableDoctorSlots({ hospital_id, department_id, date, exclude_doctor_id }) {
    return api.get("/appointments/available-doctor-slots", {
      params: { hospital_id, department_id, date, exclude_doctor_id },
    });
  },

  destroy(id) {
    return api.delete(`/appointments/${id}`);
  },

  /**
   * Patient hides a completed/cancelled appointment from their own history.
   * The record stays in the DB — doctors and admins still see it.
   */
  hideFromHistory(id) {
    return api.patch(`/appointments/${id}/hide`);
  },

  // ── Referrals ──────────────────────────────────────────────────────────

  /**
   * Doctor refers an appointment to another doctor or department.
   */
  refer(appointmentId, data) {
    return api.post(`/appointments/${appointmentId}/refer`, data);
  },

  /**
   * Get all referrals for an appointment.
   */
  getReferrals(appointmentId) {
    return api.get(`/appointments/${appointmentId}/referrals`);
  },

  /**
   * Get incoming referrals for the authenticated doctor.
   */
  getIncomingReferrals() {
    return api.get("/appointment-referrals/incoming");
  },

  /**
   * Referred-to doctor accepts or rejects a referral.
   * action: 'accept' | 'reject'
   */
  respondReferral(referralId, action, rejectionReason = null) {
    return api.patch(`/appointment-referrals/${referralId}/respond`, {
      action,
      rejection_reason: rejectionReason,
    });
  },
};
