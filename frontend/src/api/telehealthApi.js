import api from "./axios";

export default {
  // ── Sessions ───────────────────────────────────────────────────────────────

  /**
   * Get all telehealth sessions for the current authenticated user.
   * Doctor → their sessions, Patient → their sessions, Admin → all hospital sessions.
   */
  getMySessions() {
    return api.get("/telehealth-sessions/my-sessions");
  },

  /**
   * Get a single telehealth session by ID.
   */
  getSession(id) {
    return api.get(`/telehealth-sessions/${id}`);
  },

  /**
   * Get the telehealth session linked to a specific appointment.
   */
  getSessionByAppointment(appointmentId) {
    return api.get(`/telehealth-sessions/appointment/${appointmentId}`);
  },

  /**
   * Create a new telehealth session (manual URL or platform dispatch).
   * payload: { appointment_id, doctor_id, start_time, end_time, platform, session_url?, ... }
   */
  createSession(payload) {
    return api.post("/telehealth-sessions", payload);
  },

  /**
   * Create a Google Meet session automatically via Google Calendar API.
   * payload: { appointment_id, doctor_id, start_time, end_time, platform: 'google_meet' }
   */
  createGoogleMeetSession(payload) {
    return api.post("/telehealth-sessions/google-meet", payload);
  },

  /**
   * Update session details (session_url, platform, recording_consent, etc.).
   */
  updateSession(id, payload) {
    return api.put(`/telehealth-sessions/${id}`, payload);
  },

  /**
   * Start a scheduled session → status becomes 'active'.
   */
  startSession(id) {
    return api.post(`/telehealth-sessions/${id}/start`);
  },

  /**
   * Complete an active session → status becomes 'completed'.
   */
  completeSession(id) {
    return api.post(`/telehealth-sessions/${id}/complete`);
  },

  /**
   * Cancel a session → status becomes 'cancelled'.
   */
  cancelSession(id) {
    return api.post(`/telehealth-sessions/${id}/cancel`);
  },

  /**
   * Reschedule a session by adding minutes to the appointment time.
   * payload: { add_minutes: 10 }
   */
  rescheduleSession(id, addMinutes) {
    return api.post(`/telehealth-sessions/${id}/reschedule`, { add_minutes: addMinutes });
  },

  // ── Attendance ─────────────────────────────────────────────────────────────

  /**
   * List all attendance records for a session.
   */
  listAttendance(sessionId) {
    return api.get(`/telehealth-sessions/${sessionId}/attendance`);
  },

  /**
   * Join a session (record attendance).
   * payload: { session_id, user_id, device_type? }
   */
  joinSession(sessionId, payload) {
    return api.post(`/telehealth-sessions/${sessionId}/attendance`, payload);
  },

  /**
   * Leave a session (update attendance with left_at).
   */
  leaveSession(sessionId, userId) {
    return api.put(`/telehealth-sessions/${sessionId}/attendance/${userId}`);
  },

  // ── Google OAuth ───────────────────────────────────────────────────────────

  /**
   * Get the Google OAuth redirect URL (opens in new tab or redirect).
   */
  getGoogleAuthUrl() {
    return `${api.defaults.baseURL}/telehealth-sessions/google/redirect`;
  },
};
