import { defineStore } from "pinia";
import telehealthApi from "../api/telehealthApi";

export const useTelehealthStore = defineStore("telehealth", {
  state: () => ({
    sessions: [],           // all sessions for current user
    currentSession: null,   // session being viewed / active call
    attendance: [],         // attendance records for currentSession
    loading: false,
    actionLoading: false,   // for start/complete/cancel button states
    error: null,
  }),

  getters: {
    scheduledSessions: (state) =>
      state.sessions.filter((s) => s.status === "scheduled"),

    activeSessions: (state) =>
      state.sessions.filter((s) => s.status === "active"),

    completedSessions: (state) =>
      state.sessions.filter((s) => s.status === "completed"),

    cancelledSessions: (state) =>
      state.sessions.filter((s) => s.status === "cancelled"),

    upcomingSessions: (state) =>
      state.sessions.filter((s) =>
        s.status === "scheduled" || s.status === "active"
      ),

    sessionById: (state) => (id) =>
      state.sessions.find((s) => s.id === id),

    /** Total sessions today */
    todaySessions: (state) => {
      const today = new Date().toISOString().slice(0, 10);
      return state.sessions.filter((s) => {
        const time =
          s.appointment?.scheduled_time || s.started_at || s.created_at;
        return time && time.slice(0, 10) === today;
      });
    },
  },

  actions: {
    // ── Fetch ──────────────────────────────────────────────────────────────

    async fetchMySessions() {
      try {
        this.loading = true;
        this.error = null;
        const res = await telehealthApi.getMySessions();
        this.sessions = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load telehealth sessions";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchSession(id) {
      try {
        this.loading = true;
        this.error = null;
        const res = await telehealthApi.getSession(id);
        this.currentSession = res.data?.data ?? res.data;
        return this.currentSession;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load session";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchSessionByAppointment(appointmentId) {
      try {
        this.loading = true;
        this.error = null;
        const res = await telehealthApi.getSessionByAppointment(appointmentId);
        this.currentSession = res.data?.data ?? res.data;
        return this.currentSession;
      } catch (err) {
        // 404 means no session yet — not a fatal error
        if (err.response?.status !== 404) {
          this.error =
            err.response?.data?.message || "Failed to load session";
        }
        this.currentSession = null;
        return null;
      } finally {
        this.loading = false;
      }
    },

    // ── Create ─────────────────────────────────────────────────────────────

    async createSession(payload) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.createSession(payload);
        const created = res.data?.data ?? res.data;
        this.sessions.unshift(created);
        return created;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to create session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    async createGoogleMeetSession(payload) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.createGoogleMeetSession(payload);
        const created = res.data?.data ?? res.data;
        this.sessions.unshift(created);
        return created;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to create Google Meet session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    // ── Update ─────────────────────────────────────────────────────────────

    async updateSession(id, payload) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.updateSession(id, payload);
        const updated = res.data?.data ?? res.data;
        this._replaceInList(updated);
        if (this.currentSession?.id === id) this.currentSession = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to update session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    // ── Status Transitions ─────────────────────────────────────────────────

    async startSession(id) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.startSession(id);
        const updated = res.data?.data ?? res.data;
        this._replaceInList(updated);
        if (this.currentSession?.id === id) this.currentSession = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to start session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    async completeSession(id) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.completeSession(id);
        const updated = res.data?.data ?? res.data;
        this._replaceInList(updated);
        if (this.currentSession?.id === id) this.currentSession = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to complete session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    async cancelSession(id) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.cancelSession(id);
        const updated = res.data?.data ?? res.data;
        this._replaceInList(updated);
        if (this.currentSession?.id === id) this.currentSession = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to cancel session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    // ── Attendance ─────────────────────────────────────────────────────────

    async fetchAttendance(sessionId) {
      try {
        const res = await telehealthApi.listAttendance(sessionId);
        this.attendance = res.data?.data ?? res.data ?? [];
        return this.attendance;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load attendance";
        throw err;
      }
    },

    async joinSession(sessionId, userId, deviceType = null) {
      try {
        this.actionLoading = true;
        this.error = null;
        const payload = { session_id: sessionId, user_id: userId };
        if (deviceType) payload.device_type = deviceType;
        const res = await telehealthApi.joinSession(sessionId, payload);
        const record = res.data?.data ?? res.data;
        this.attendance.push(record);
        return record;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to join session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    async leaveSession(sessionId, userId) {
      try {
        this.actionLoading = true;
        this.error = null;
        const res = await telehealthApi.leaveSession(sessionId, userId);
        const updated = res.data?.data ?? res.data;
        const idx = this.attendance.findIndex(
          (a) => a.session_id === sessionId && a.user_id === userId
        );
        if (idx !== -1) this.attendance[idx] = updated;
        return updated;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to leave session";
        throw err;
      } finally {
        this.actionLoading = false;
      }
    },

    // ── Helpers ────────────────────────────────────────────────────────────

    _replaceInList(updated) {
      const idx = this.sessions.findIndex((s) => s.id === updated.id);
      if (idx !== -1) this.sessions[idx] = updated;
    },

    clearError() {
      this.error = null;
    },

    setCurrentSession(session) {
      this.currentSession = session;
    },
  },
});
