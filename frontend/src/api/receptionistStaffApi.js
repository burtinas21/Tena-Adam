import api from "./axios";

export default {
  // List all receptionists for the logged-in admin's hospital
  getAll() {
    return api.get("/hospital-staff", { params: { role: "receptionist" } });
  },

  // Backend determines hospital_id from the authenticated admin — no need to send it
  create(data) {
    const { hospital_id, ...payload } = data; // strip any client-side hospital_id
    return api.post("/hospital-staff", { ...payload, role: "receptionist" });
  },

  update(id, data) {
    return api.put(`/hospital-staff/${id}`, data);
  },

  destroy(id) {
    return api.delete(`/hospital-staff/${id}`);
  },
};
