import { defineStore } from "pinia";
import reportApi from "../api/reportApi";
import appointmentApi from "../api/appointmentApi";

function unwrap(res) {
  const body = res.data;
  if (body?.data !== undefined) return body.data;
  if (Array.isArray(body)) return body;
  return body ?? null;
}

function safeArray(val) {
  return Array.isArray(val) ? val : [];
}

export const useHospitalDashboardStore = defineStore("hospitalDashboard", {
  state: () => ({
    patientStats: null,
    appointmentReport: null,
    doctorWorkload: [],
    departmentPerformance: [],
    telehealthStats: null,
    recentAppointments: [],

    loading: false,
    error: null,
  }),

  getters: {
    // ── Stat Cards ──────────────────────────────────────────────────────────
    totalPatients:    (s) => s.patientStats?.total_patients        ?? 0,
    newPatientsMonth: (s) => s.patientStats?.new_patients_this_month ?? 0,

    totalDoctors:     (s) => safeArray(s.doctorWorkload).length,
    totalDepartments: (s) => safeArray(s.departmentPerformance).length,

    todayAppointments: (s) => {
      const today = new Date().toISOString().slice(0, 10);
      return safeArray(s.recentAppointments).filter(
        (a) => a.created_at?.slice(0, 10) === today || a.scheduled_at?.slice(0, 10) === today
      ).length;
    },

    completedToday: (s) =>
      safeArray(s.recentAppointments).filter((a) => a.status === "completed").length,

    waitingNow: (s) =>
      safeArray(s.recentAppointments).filter(
        (a) => a.status === "scheduled" || a.status === "confirmed"
      ).length,

    activeTelemed: (s) =>
      s.telehealthStats?.active_sessions ?? s.telehealthStats?.total_sessions ?? 0,

    totalAppointments: (s) => s.appointmentReport?.total_appointments ?? 0,
    pendingAppts: (s)     => s.appointmentReport?.pending_appointments ?? 0,
    completedAppts: (s)   => s.appointmentReport?.completed_appointments ?? 0,

    // ── Department performance bars (name + % of completions) ──────────────
    deptPerformanceList: (s) => {
      return safeArray(s.departmentPerformance)
        .slice(0, 5)
        .map((d) => {
          const total     = Math.max(d.total_appointments ?? 1, 1);
          const completed = d.completed_appointments ?? 0;
          return {
            name: d.department_name ?? d.name ?? "Unknown",
            percentage: Math.min(Math.round((completed / total) * 100), 100),
          };
        });
    },

    // ── Recent appointments for activity list ──────────────────────────────
    recentActivityList: (s) => {
      return safeArray(s.recentAppointments)
        .slice(0, 5)
        .map((a) => ({
          id:         a.id,
          patientName: a.patient?.user?.first_name
            ? `${a.patient.user.first_name} ${a.patient.user.last_name ?? ""}`.trim()
            : "Unknown Patient",
          doctorName: a.doctor?.user?.first_name
            ? `Dr. ${a.doctor.user.first_name} ${a.doctor.user.last_name ?? ""}`.trim()
            : "Unknown Doctor",
          department: a.department?.name ?? "",
          status:     a.status,
          createdAt:  a.created_at,
        }));
    },

    // ── Doctor workload for queue / performance ────────────────────────────
    doctorWorkloadList: (s) => {
      return safeArray(s.doctorWorkload).slice(0, 5).map((d) => ({
        name:         d.doctor_name ?? "Unknown",
        appointments: d.total_appointments ?? 0,
        completed:    d.completed_appointments ?? 0,
        department:   d.department_name ?? "",
      }));
    },
  },

  actions: {
    async fetchAll() {
      this.loading = true;
      this.error   = null;

      const results = await Promise.allSettled([
        reportApi.getPatientStatistics(),
        reportApi.getAppointmentReport(),
        reportApi.getDoctorWorkload(),
        reportApi.getDepartmentPerformance(),
        reportApi.getTelehealthStatistics(),
        appointmentApi.getAll(),
      ]);

      const [
        patientResult,
        appointmentResult,
        doctorResult,
        deptResult,
        telehealthResult,
        apptsResult,
      ] = results;

      if (patientResult.status    === "fulfilled") this.patientStats           = unwrap(patientResult.value);
      if (appointmentResult.status === "fulfilled") this.appointmentReport     = unwrap(appointmentResult.value);
      if (doctorResult.status     === "fulfilled") this.doctorWorkload          = safeArray(unwrap(doctorResult.value));
      if (deptResult.status       === "fulfilled") this.departmentPerformance   = safeArray(unwrap(deptResult.value));
      if (telehealthResult.status === "fulfilled") this.telehealthStats         = unwrap(telehealthResult.value);
      if (apptsResult.status      === "fulfilled") this.recentAppointments      = safeArray(unwrap(apptsResult.value));

      // Surface first error if all critical calls failed
      const criticalFails = results.filter((r) => r.status === "rejected");
      if (criticalFails.length === results.length) {
        this.error = "Failed to load dashboard data. Please refresh.";
      }

      this.loading = false;
    },
  },
});
