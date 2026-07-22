import { defineStore } from "pinia";
import hospitalApi from "../api/hospitalApi";
import doctorApi from "../api/doctorApi";
import hospitalAdminApi from "../api/hospitalAdminApi";
import reportApi from "../api/reportApi";
import auditLogApi from "../api/auditLogApi";

function unwrapList(res) {
  const body = res.data;
  if (Array.isArray(body)) return body;
  if (Array.isArray(body?.data)) return body.data;
  return [];
}

function unwrapObject(res) {
  const body = res.data;
  return body?.data ?? body ?? null;
}

export function fmtCompact(val) {
  if (val === null || val === undefined) return "—";
  const n = Number(val);
  if (Number.isNaN(n)) return "—";
  if (n >= 1_000_000) {
    return `${(n / 1_000_000).toFixed(1).replace(/\.0$/, "")}M`;
  }
  if (n >= 1000) {
    return `${(n / 1000).toFixed(1).replace(/\.0$/, "")}k`;
  }
  return n.toLocaleString();
}

function growthPct(current, previous) {
  if (!previous) return current > 0 ? "+100%" : "0%";
  const pct = Math.round(((current - previous) / previous) * 100);
  return `${pct >= 0 ? "+" : ""}${pct}%`;
}

function monthKey(dateStr) {
  if (!dateStr) return null;
  return String(dateStr).slice(0, 7);
}

function countsByMonth(items, dateField = "created_at") {
  const counts = {};
  for (const item of items) {
    const key = monthKey(item[dateField]);
    if (!key) continue;
    counts[key] = (counts[key] || 0) + 1;
  }
  return counts;
}

function lastTwoTrendValues(data) {
  if (!data?.length) return [0, 0];
  if (data.length === 1) return [data[0], 0];
  return [data[data.length - 1], data[data.length - 2]];
}

export const usePlatformDashboardStore = defineStore("platformDashboard", {
  state: () => ({
    hospitals: [],
    providers: [],
    staff: [],
    patientStats: null,
    appointmentReport: null,
    trends: null,
    recentLogs: [],
    loading: false,
    error: null,
  }),

  getters: {
    totalHospitals: (state) => state.hospitals.length,

    activeHospitals: (state) =>
      state.hospitals.filter((h) => h.is_active).length,

    totalProviders: (state) => state.providers.length,

    totalPatients: (state) => state.patientStats?.total_patients ?? 0,

    totalAppointments: (state) =>
      state.appointmentReport?.total_appointments ?? 0,

    systemUsers: (state) => state.staff.length + state.providers.length,

    trendMonths: (state) => state.trends?.trends ?? [],

    trendLabels(state) {
      return this.trendMonths.map((t) =>
        new Date(`${t.month}-01`).toLocaleString("default", {
          month: "short",
        })
      );
    },

    trendPatientData(state) {
      return (state.trends?.trends ?? []).map((t) => t.patient_registrations);
    },

    trendAppointmentData(state) {
      return (state.trends?.trends ?? []).map((t) => t.appointments ?? 0);
    },

    hospitalGrowthData(state) {
      const hospitalCounts = countsByMonth(state.hospitals);
      return (state.trends?.trends ?? []).map(
        (t) => hospitalCounts[t.month] || 0
      );
    },

    hospitalTrend(state) {
      const counts = countsByMonth(state.hospitals);
      const months = (state.trends?.trends ?? []).map((t) => t.month);
      if (months.length < 2) return "0%";
      const current = counts[months[months.length - 1]] || 0;
      const previous = counts[months[months.length - 2]] || 0;
      return growthPct(current, previous);
    },

    activeHospitalTrend(state) {
      const total = state.hospitals.length;
      if (!total) return "0%";
      const active = state.hospitals.filter((h) => h.is_active).length;
      return `${Math.round((active / total) * 100)}%`;
    },

    providerTrend() {
      return "0%";
    },

    patientTrend(state) {
      const [current, previous] = lastTwoTrendValues(
        (state.trends?.trends ?? []).map((t) => t.patient_registrations)
      );
      return growthPct(current, previous);
    },

    appointmentTrend(state) {
      const [current, previous] = lastTwoTrendValues(
        (state.trends?.trends ?? []).map((t) => t.appointments)
      );
      return growthPct(current, previous);
    },

    systemUserTrend() {
      return "0%";
    },
  },

  actions: {
    async fetchAll() {
      this.loading = true;
      this.error = null;

      const results = await Promise.allSettled([
        hospitalApi.getAll(),
        doctorApi.getAll(),
        hospitalAdminApi.getAll(),
        reportApi.getPatientStatistics(),
        reportApi.getAppointmentReport(),
        reportApi.getHealthcareTrends(),
        auditLogApi.getRecent(5),
      ]);

      const [
        hospitalRes, providerRes, staffRes,
        patientRes, appointmentRes, trendsRes, logsRes,
      ] = results;

      if (hospitalRes.status    === "fulfilled") this.hospitals        = unwrapList(hospitalRes.value);
      if (providerRes.status    === "fulfilled") this.providers        = unwrapList(providerRes.value);
      if (staffRes.status       === "fulfilled") this.staff            = unwrapList(staffRes.value);
      if (patientRes.status     === "fulfilled") this.patientStats     = unwrapObject(patientRes.value);
      if (appointmentRes.status === "fulfilled") this.appointmentReport= unwrapObject(appointmentRes.value);
      if (trendsRes.status      === "fulfilled") this.trends           = unwrapObject(trendsRes.value);
      if (logsRes.status        === "fulfilled") this.recentLogs       = unwrapList(logsRes.value);

      if (results.every((r) => r.status === "rejected")) {
        this.error = "Failed to load dashboard data.";
      }

      this.loading = false;
    },
  },
});
