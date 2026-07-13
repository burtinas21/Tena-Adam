import api from "./axios";

export default {
  getAll() {
    return api.get("/departments");
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
