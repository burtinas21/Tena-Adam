import api from "./axios";

export default {
  getAll(params = {}) {
    return api.get("/healthcare-providers", { params });
  },
  getById(id) {
    return api.get(`/healthcare-providers/${id}`);
  },
  // ── Doctor self-service ──────────────────────────────────────────────────
  getMe() {
    return api.get("/doctor/me");
  },
  updateMe(data) {
    const form = new FormData();
    form.append("_method", "PUT");
    Object.entries(data).forEach(([k, v]) => {
      if (v !== null && v !== undefined) {
        form.append(k, typeof v === "boolean" ? (v ? "1" : "0") : v);
      }
    });
    return api.post("/doctor/me", form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },
  getMySchedules() {
    return api.get("/doctor/my-schedules");
  },
  // ── Hospital admin CRUD ──────────────────────────────────────────────────
  create(data) {
    const form = new FormData();
    Object.entries(data).forEach(([k, v]) => {
      if (v !== null && v !== undefined) {
        form.append(k, typeof v === "boolean" ? (v ? "1" : "0") : v);
      }
    });
    return api.post("/healthcare-providers", form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },
  update(id, data) {
    const form = new FormData();
    form.append("_method", "PUT");
    Object.entries(data).forEach(([k, v]) => {
      if (v !== null && v !== undefined) {
        form.append(k, typeof v === "boolean" ? (v ? "1" : "0") : v);
      }
    });
    return api.post(`/healthcare-providers/${id}`, form, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },
  destroy(id) {
    return api.delete(`/healthcare-providers/${id}`);
  },
};
