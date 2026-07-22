import { defineStore } from "pinia";
import appointmentApi  from "../api/appointmentApi";
import queueApi        from "../api/queueApi";
import notificationApi from "../api/notificationApi";
import doctorApi       from "../api/doctorApi";
import { useAuthStore } from "./authStore";

function unwrap(res) {
  const body = res.data;
  if (body?.data !== undefined) return body.data;
  if (Array.isArray(body)) return body;
  return body ?? null;
}

function safe(val) {
  return Array.isArray(val) ? val : [];
}

const TODAY = () => new Date().toISOString().slice(0, 10);

export const useDoctorDashboardStore = defineStore("doctorDashboard", {
  state: () => ({
    profile:       null,   // doctor's own provider profile
    appointments:  [],     // all appointments for this doctor
    queue:         [],     // today's queue entries
    notifications: [],     // doctor's notifications

    loading:    false,
    error:      null,
  }),

  getters: {
    // ── Stat cards ──────────────────────────────────────────────────────
    doctorName: (s) => {
      const auth = useAuthStore();
      const u    = auth.user;
      if (!u) return "Doctor";
      return `Dr. ${u.first_name ?? ""} ${u.last_name ?? ""}`.trim();
    },

    todayDate: () => new Date().toLocaleDateString("en-US", {
      weekday: "long", month: "long", day: "numeric",
    }),

    todayAppointments: (s) => {
      const t = TODAY();
      return safe(s.appointments).filter(
        (a) => (a.scheduled_at ?? a.created_at ?? "").slice(0, 10) === t
      );
    },

    todayAppointmentsCount() { return this.todayAppointments.length; },

    waitingCount: (s) =>
      safe(s.queue).filter((q) => q.status === "waiting").length,

    completedTodayCount: (s) =>
      safe(s.queue).filter((q) => q.status === "completed").length,

    telemed: (s) =>
      safe(s.appointments).filter(
        (a) => a.appointment_type === "telemedicine" || a.appointment_type === "telehealth"
      ).length,

    pendingFollowUp: (s) =>
      safe(s.appointments).filter((a) => a.status === "pending").length,

    activePrescriptions: () => 0, // requires prescriptions endpoint

    unreadNotifications: (s) =>
      safe(s.notifications).filter((n) => n.status !== "read").length,

    // ── Schedule timeline (today's appointments sorted by slot time) ──────
    todaySchedule() {
      return [...this.todayAppointments]
        .sort((a, b) => {
          const ta = a.slot?.start_time ?? a.scheduled_at ?? "";
          const tb = b.slot?.start_time ?? b.scheduled_at ?? "";
          return ta.localeCompare(tb);
        })
        .slice(0, 6)
        .map((a) => {
          const slot      = a.slot;
          const startTime = slot?.start_time
            ? fmtTime(slot.start_time)
            : fmtTime(a.scheduled_at);
          const duration  = slot?.start_time && slot?.end_time
            ? durationMins(slot.start_time, slot.end_time) + " min"
            : "—";
          const patient   = a.patient?.user
            ? `${a.patient.user.first_name ?? ""} ${a.patient.user.last_name ?? ""}`.trim()
            : "Unknown Patient";
          const type      = a.appointment_type === "telehealth" || a.appointment_type === "telemedicine"
            ? "video" : "physical";
          return {
            id:       a.id,
            time:     startTime,
            duration,
            patient,
            reason:   a.reason ?? a.notes ?? "",
            status:   a.status,
            type,
            doctorId: a.doctor_id,
          };
        });
    },

    // ── Queue list ────────────────────────────────────────────────────────
    queueList: (s) => safe(s.queue).map((q, idx) => ({
      id:          q.id,
      number:      String(idx + 1).padStart(2, "0"),
      patient:     q.patient?.user
        ? `${q.patient.user.first_name ?? ""} ${q.patient.user.last_name ?? ""}`.trim()
        : "Unknown",
      waitMins:    q.wait_time_minutes ?? waitSince(q.check_in_time),
      priority:    q.priority ?? "normal",
      status:      q.status,
    })),

    // ── Recent notifications (top 5) ──────────────────────────────────────
    recentNotifications: (s) =>
      safe(s.notifications).slice(0, 5).map((n) => ({
        id:      n.id,
        title:   n.title ?? n.type ?? "Notification",
        message: n.message ?? n.data?.message ?? "",
        isRead:  n.status === "read",
        type:    n.type ?? "info",
        time:    n.created_at,
      })),
  },

  actions: {
    async fetchAll() {
      this.loading = true;
      this.error   = null;

      const auth    = useAuthStore();
      const userId  = auth.user?.id;
      const today   = TODAY();

      const results = await Promise.allSettled([
        appointmentApi.getAll(),
        queueApi.getDoctorQueue(userId, today),
        notificationApi.getAll(),
        doctorApi.getMe(),
      ]);

      const [apptR, queueR, notifR, profileR] = results;

      if (apptR.status    === "fulfilled") this.appointments  = safe(unwrap(apptR.value));
      if (queueR.status   === "fulfilled") this.queue         = safe(unwrap(queueR.value));
      if (notifR.status   === "fulfilled") this.notifications = safe(unwrap(notifR.value));
      if (profileR.status === "fulfilled") this.profile       = unwrap(profileR.value);

      if (results.every((r) => r.status === "rejected")) {
        this.error = "Failed to load dashboard data.";
      }

      this.loading = false;
    },

    async markAllRead() {
      try {
        await notificationApi.markAllRead();
        this.notifications = this.notifications.map((n) => ({ ...n, status: "read" }));
      } catch { /* silent */ }
    },
  },
});

// ── Helpers ──────────────────────────────────────────────────────────────────
function fmtTime(iso) {
  if (!iso) return "—";
  try {
    return new Date(iso).toLocaleTimeString("en-US", {
      hour: "2-digit", minute: "2-digit", hour12: true,
    });
  } catch { return iso; }
}

function durationMins(start, end) {
  try {
    const s = new Date(`1970-01-01T${start}`);
    const e = new Date(`1970-01-01T${end}`);
    return Math.round((e - s) / 60000);
  } catch { return "—"; }
}

function waitSince(checkIn) {
  if (!checkIn) return 0;
  return Math.round((Date.now() - new Date(checkIn).getTime()) / 60000);
}
