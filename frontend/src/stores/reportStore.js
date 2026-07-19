import { defineStore } from "pinia";
import reportApi from "../api/reportApi";

/**
 * Trigger a file download from a Blob response.
 */
function downloadBlob(blob, filename) {
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

export const useReportStore = defineStore("report", {
  state: () => ({
    patientStats: null,
    appointmentReport: null,
    doctorWorkload: [],
    departmentPerformance: [],
    telehealthStats: null,
    trends: null,
    doctorRatings: [],

    loading: false,
    exportLoading: false,
    error: null,
  }),

  getters: {
    // Appointment donut chart segments (for AppointmentStatusChart)
    appointmentChartData(state) {
      if (!state.appointmentReport) return null;
      const r = state.appointmentReport;
      const total = r.total_appointments || 1;
      return {
        total: r.total_appointments,
        completed: r.completed_appointments,
        cancelled: r.cancelled_appointments,
        pending: r.pending_appointments,
        noShow:
          total -
          r.completed_appointments -
          r.cancelled_appointments -
          r.pending_appointments -
          (r.approved_appointments || 0),
        completedPct: Math.round((r.completed_appointments / total) * 100),
        cancelledPct: Math.round((r.cancelled_appointments / total) * 100),
        pendingPct: Math.round((r.pending_appointments / total) * 100),
      };
    },

    // Healthcare trends ready for the growth chart (last 12 months)
    trendLabels(state) {
      return (state.trends?.trends ?? []).map((t) =>
        new Date(t.month + "-01").toLocaleString("default", { month: "short" })
      );
    },

    trendPatientData(state) {
      return (state.trends?.trends ?? []).map((t) => t.patient_registrations);
    },

    trendAppointmentData(state) {
      return (state.trends?.trends ?? []).map((t) => t.appointments);
    },

    trendConsultationData(state) {
      return (state.trends?.trends ?? []).map((t) => t.completed_consultations);
    },

    trendTelehealthData(state) {
      return (state.trends?.trends ?? []).map((t) => t.telehealth_sessions);
    },
  },

  actions: {
    // ── Fetch all dashboard data in parallel ────────────────────────────────
    async fetchAll() {
      this.loading = true;
      this.error = null;
      try {
        const [
          patientRes,
          apptRes,
          telehealthRes,
          trendsRes,
        ] = await Promise.all([
          reportApi.getPatientStatistics(),
          reportApi.getAppointmentReport(),
          reportApi.getTelehealthStatistics(),
          reportApi.getHealthcareTrends(),
        ]);
        this.patientStats = patientRes.data?.data ?? patientRes.data;
        this.appointmentReport = apptRes.data?.data ?? apptRes.data;
        this.telehealthStats = telehealthRes.data?.data ?? telehealthRes.data;
        this.trends = trendsRes.data?.data ?? trendsRes.data;
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load report data.";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchDoctorWorkload() {
      this.loading = true;
      this.error = null;
      try {
        const res = await reportApi.getDoctorWorkload();
        this.doctorWorkload = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load doctor workload.";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchDepartmentPerformance() {
      this.loading = true;
      this.error = null;
      try {
        const res = await reportApi.getDepartmentPerformance();
        this.departmentPerformance = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          "Failed to load department performance.";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchDoctorRatings() {
      this.loading = true;
      this.error = null;
      try {
        const res = await reportApi.getDoctorRatingStatistics();
        this.doctorRatings = res.data?.data ?? res.data ?? [];
      } catch (err) {
        this.error =
          err.response?.data?.message || "Failed to load doctor ratings.";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // ── Export helpers ───────────────────────────────────────────────────────
    async exportExcel(type) {
      this.exportLoading = true;
      this.error = null;
      try {
        const res = await reportApi.exportExcel(type);
        downloadBlob(res.data, `${type}_report.xlsx`);
      } catch (err) {
        this.error = err.response?.data?.message || "Export failed.";
        throw err;
      } finally {
        this.exportLoading = false;
      }
    },

    async exportCsv(type) {
      this.exportLoading = true;
      this.error = null;
      try {
        const res = await reportApi.exportCsv(type);
        downloadBlob(res.data, `${type}_report.csv`);
      } catch (err) {
        this.error = err.response?.data?.message || "Export failed.";
        throw err;
      } finally {
        this.exportLoading = false;
      }
    },

    async exportPdf(type) {
      this.exportLoading = true;
      this.error = null;
      try {
        const res = await reportApi.exportPdf(type);
        downloadBlob(res.data, `${type}_report.pdf`);
      } catch (err) {
        this.error = err.response?.data?.message || "Export failed.";
        throw err;
      } finally {
        this.exportLoading = false;
      }
    },
  },
});
