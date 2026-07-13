import api from "./axios";

export default {
  getAll() {
    return api.get("/operating-hours");
  },
  getById(id) {
    return api.get(`/operating-hours/${id}`);
  },
  create(data) {
    return api.post("/operating-hours", data);
  },
  update(id, data) {
    return api.put(`/operating-hours/${id}`, data);
  },
  destroy(id) {
    return api.delete(`/operating-hours/${id}`);
  },
};
