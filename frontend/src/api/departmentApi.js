import api from "./axios";

export default {
  getAll(params = {}) {
    return api.get("/departments", { params });
  },

  getByHospital(hospitalId) {
    return api.get("/departments", { params: { hospital_id: hospitalId } });
  },

  getById(id) {
    return api.get(`/departments/${id}`);
  },

  create(data) {
    return api.post("/departments", data);
  },

  update(id, data) {
    return api.put(`/departments/${id}`, data);
  },

  destroy(id) {
    return api.delete(`/departments/${id}`);
  },
};
